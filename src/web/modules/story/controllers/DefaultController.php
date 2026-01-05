<?php

namespace app\modules\story\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\modules\story\models\StoryForm;
use app\modules\story\models\StoryHistory;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'stream' => ['POST'],
                    'view' => ['GET'],
                    'list' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Главная страница с формой генерации
     */
    public function actionIndex()
    {
        $model = new StoryForm();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Endpoint для streaming генерации сказки
     */
    public function actionStream()
    {
        // Отключаем стандартную обработку response
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        
        // Отправляем заголовки сразу
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');
        
        // Отключаем буферизацию
        while (ob_get_level()) {
            ob_end_flush();
        }

        // Читаем JSON из body
        $rawBody = Yii::$app->request->getRawBody();
        $postData = json_decode($rawBody, true);

        if (!$postData) {
            echo "data: " . json_encode(['error' => 'Invalid JSON data']) . "\n\n";
            flush();
            Yii::$app->end();
        }

        $model = new StoryForm();
        $model->age = $postData['age'] ?? null;
        $model->language = $postData['language'] ?? null;
        $model->characters = $postData['characters'] ?? [];

        if (!$model->validate()) {
            echo "data: " . json_encode(['error' => $model->getFirstErrors()]) . "\n\n";
            flush();
            Yii::$app->end();
        }

        // Буфер для накопления полного текста сказки
        $fullStoryText = '';

        try {
            $url = Yii::$app->getModule('story')->python_api_url . '/generate_story';
            
            $requestData = json_encode([
                'age' => (int)$model->age,
                'language' => $model->language,
                'characters' => $model->characters,
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($requestData)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$fullStoryText) {
                // Отправляем каждый чанк как SSE событие
                if (!empty(trim($data))) {
                    // Накапливаем полный текст
                    $fullStoryText .= $data;
                    
                    // Оборачиваем в SSE формат
                    echo "data: " . json_encode(['chunk' => $data]) . "\n\n";
                    flush();
                }
                return strlen($data);
            });

            curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo "data: " . json_encode(['error' => curl_error($ch)]) . "\n\n";
                flush();
            }

            curl_close($ch);

            // Сигнал завершения
            echo "data: " . json_encode(['done' => true]) . "\n\n";
            flush();

            // Сохраняем сказку в базу данных после успешной генерации
            if (!empty($fullStoryText)) {
                $this->saveStoryToDatabase($model, $fullStoryText);
            }

        } catch (\Exception $e) {
            echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
            flush();
        }
        
        // ВАЖНО: Завершаем приложение, чтобы Yii2 не пытался отправить response
        Yii::$app->end();
    }

    /**
     * Сохранение сказки в базу данных
     */
    protected function saveStoryToDatabase(StoryForm $form, string $storyText)
    {
        try {
            $story = new StoryHistory();
            
            // Генерируем заголовок из первых слов сказки
            $title = $this->generateTitle($storyText, $form->characters);
            
            $story->title = $title;
            $story->age = $form->age;
            $story->language = $form->language;
            $story->setCharactersArray($form->characters);
            $story->story_text = $storyText;
            
            if (!$story->save()) {
                Yii::error('Failed to save story: ' . json_encode($story->errors), __METHOD__);
            } else {
                Yii::info("Story saved successfully with ID: {$story->id}", __METHOD__);
            }
        } catch (\Exception $e) {
            Yii::error("Exception while saving story: " . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Генерация заголовка для сказки
     */
    protected function generateTitle(string $text, array $characters): string
    {
        // Убираем markdown разметку
        $cleanText = strip_tags(preg_replace('/[#*_`]/', '', $text));
        
        // Берем первое предложение или первые 50 символов
        $sentences = preg_split('/[.!?]+/', $cleanText, 2);
        $firstSentence = trim($sentences[0] ?? '');
        
        if (mb_strlen($firstSentence) > 50) {
            $title = mb_substr($firstSentence, 0, 47) . '...';
        } elseif (!empty($firstSentence)) {
            $title = $firstSentence;
        } else {
            // Если не удалось извлечь заголовок, генерируем из персонажей
            $title = 'Сказка про ' . implode(', ', array_slice($characters, 0, 2));
            if (count($characters) > 2) {
                $title .= ' и др.';
            }
        }
        
        return $title;
    }

    /**
     * Просмотр отдельной сказки (AJAX)
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $story = StoryHistory::findOne($id);
        
        if (!$story) {
            return [
                'success' => false,
                'error' => 'Story not found',
            ];
        }
        
        return [
            'success' => true,
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'age' => $story->age,
                'language' => $story->language,
                'characters' => $story->getCharactersArray(),
                'story_text' => $story->story_text,
                'created_at' => $story->getFormattedDate(),
            ],
        ];
    }

    /**
     * Список всех сказок (AJAX)
     */
    public function actionList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $stories = StoryHistory::getRecentStories(50);
        
        return [
            'success' => true,
            'stories' => array_map(function($story) {
                return [
                    'id' => $story->id,
                    'title' => $story->title,
                    'language' => $story->language,
                    'created_at' => $story->getFormattedDate(),
                ];
            }, $stories),
        ];
    }
}