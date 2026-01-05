<?php
namespace app\modules\story\controllers;

use Yii;
use yii\web\Controller;
use app\modules\story\models\StoryForm;

class DefaultController extends Controller
{
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
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
                // Отправляем каждый чанк как SSE событие
                if (!empty(trim($data))) {
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

        } catch (\Exception $e) {
            echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
            flush();
        }
        
        // ВАЖНО: Завершаем приложение, чтобы Yii2 не пытался отправить response
        Yii::$app->end();
    }
}