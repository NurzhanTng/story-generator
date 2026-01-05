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
            [['age', 'language', 'characters'], 'required'],
            ['age', 'integer', 'min' => 1],
            ['language', 'in', 'range' => ['ru', 'kk']],
            ['characters', 'each', 'rule' => ['string']],
        ];
    }

    public static function getCharacterOptions()
    {
        return ['Заяц','Волк','Лиса','Медведь','Ёж'];
    }
}
