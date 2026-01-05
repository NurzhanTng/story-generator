<?php

use yii\db\Migration;

/**
 * Миграция для создания таблицы истории сказок
 * Запустить: php yii migrate --migrationPath=@app/modules/story/migrations
 */
class m250105_000000_create_story_history_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%story_history}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Название сказки'),
            'age' => $this->integer()->notNull()->comment('Возраст ребенка'),
            'language' => $this->string(5)->notNull()->comment('Язык сказки (ru/kk)'),
            'characters' => $this->json()->notNull()->comment('Массив персонажей'),
            'story_text' => $this->text()->notNull()->comment('Текст сказки'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->notNull()->comment('Дата обновления'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        // Индексы для быстрой выборки
        $this->createIndex(
            'idx-story_history-created_at',
            '{{%story_history}}',
            'created_at'
        );

        $this->createIndex(
            'idx-story_history-language',
            '{{%story_history}}',
            'language'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%story_history}}');
    }
}