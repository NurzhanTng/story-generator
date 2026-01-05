<?php
namespace app\modules\story\controllers;

use Yii;
use yii\web\Controller;
use app\modules\story\models\StoryForm;
use yii\httpclient\Client;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $model = new StoryForm();
        $story = null;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $url = Yii::$app->getModule('story')->python_api_url . '/generate_story';
                $client = new Client();

                // Приводим characters к массиву строк, даже если один элемент
                $charactersList = ['Заяц', 'Волк', 'Лиса', 'Медведь', 'Ёж'];
                $characters = array_map(fn($i) => $charactersList[$i], (array)$model->characters);


                $response = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl($url)
                    ->setData([
                        'age' => (int)$model->age,
                        'language' => $model->language,
                        'characters' => $characters,
                    ])
                    ->setFormat(Client::FORMAT_JSON)
                    ->send();

                if ($response->isOk) {
                    // Если ответ реально JSON
                    $data = json_decode($response->content, true);
                    if ($data === null) {
                        $story = $response->content; // просто выводим текст
                    } else {
                        $story = $data['story'] ?? 'Пустой ответ от Python API';
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Python API вернул ошибку: ' . $response->statusCode . ' ' . $response->content);
                }

            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Не удалось соединиться с Python API 2: ' . $e->getMessage());
            }
        }

        return $this->render('index', [
            'model' => $model,
            'story' => $story,
        ]);
    }
}
