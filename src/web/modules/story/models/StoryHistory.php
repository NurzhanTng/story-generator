<?php

namespace app\modules\story\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Модель для истории сказок
 *
 * @property int $id
 * @property string $title
 * @property int $age
 * @property string $language
 * @property array $characters
 * @property string $story_text
 * @property int $created_at
 * @property int $updated_at
 */
class StoryHistory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%story_history}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['title', 'age', 'language', 'characters', 'story_text'], 'required'],
            ['age', 'integer', 'min' => 3, 'max' => 18],
            ['language', 'in', 'range' => ['ru', 'kk']],
            ['title', 'string', 'max' => 255],
            ['story_text', 'string'],
            ['characters', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'age' => 'Возраст',
            'language' => 'Язык',
            'characters' => 'Персонажи',
            'story_text' => 'Текст сказки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Получить персонажей как массив
     */
    public function getCharactersArray()
    {
        return is_string($this->characters) 
            ? json_decode($this->characters, true) 
            : $this->characters;
    }

    /**
     * Установить персонажей из массива
     */
    public function setCharactersArray($characters)
    {
        $this->characters = is_array($characters) 
            ? json_encode($characters, JSON_UNESCAPED_UNICODE) 
            : $characters;
    }

    /**
     * Получить форматированную дату
     */
    public function getFormattedDate()
    {
        return \Yii::$app->formatter->asDatetime($this->created_at, 'php:d.m.Y H:i');
    }

    /**
     * Получить все истории отсортированные по дате
     */
    public static function getRecentStories($limit = 50)
    {
        return static::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Получить короткий превью текста
     */
    public function getPreview($length = 100)
    {
        return mb_substr(strip_tags($this->story_text), 0, $length) . '...';
    }
}