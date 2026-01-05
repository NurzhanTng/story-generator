<?php
namespace app\modules\story;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'app\modules\story\controllers';
    public $python_api_url;

    public function init()
    {
        parent::init();
        if (!$this->python_api_url) {
            $this->python_api_url = 'http://127.0.0.1:8000';
        }
    }
}
