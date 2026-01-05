<?php
namespace app\modules\story\models;

use yii\base\Model;

class StoryForm extends Model
{
    public $age;
    public $language;
    public $characters = [];

    public function rules()
    {
        return [
            [['age', 'language', 'characters'], 'required', 'message' => 'Это поле обязательно'],
            ['age', 'integer', 'min' => 3, 'max' => 18, 'message' => 'Возраст должен быть от 3 до 18 лет'],
            ['language', 'in', 'range' => ['ru', 'kk'], 'message' => 'Выберите корректный язык'],
            ['characters', 'each', 'rule' => ['string']],
            ['characters', 'validateCharacters'],
        ];
    }

    public function validateCharacters($attribute, $params)
    {
        if (empty($this->characters)) {
            $this->addError($attribute, 'Выберите хотя бы одного персонажа');
        }
    }

    public function attributeLabels()
    {
        return [
            'age' => 'Возраст',
            'language' => 'Язык',
            'characters' => 'Персонажи',
        ];
    }
}