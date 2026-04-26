<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m260423_183220_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //TODO: сделать категории или folder как в мтс перед тем как выклыдывать, мб это и вовсе не пригодится

        $this->createTable('{{%files}}', [
            'id'            => $this->primaryKey(),
            'user_id'       => $this->integer()->unsigned()->notNull()->comment('Кто создал'),
            'entity_id'     => $this->integer()->null()->comment('ID сущности'),
            'uuid'          => $this->string(36)->notNull()->unique()->comment('Публичный идентификатор файла'),
//            'entity_type'   => $this->string(50)->null()->comment('Тип сущности: user, category, service'),
//            'folder'        => $this->string(50)->notNull()->comment('К чему относится сущность'),
            'type'          => $this->smallInteger()->notNull()->comment('Предназначение'),
            'sub_type'      => $this->smallInteger()->notNull()->comment('Категория предназначение'),

            // Мета-данные файла
            'original_name' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'path'          => $this->string(255)->notNull()->comment('Путь до файла'),
//            'name'          => $this->string(255)->notNull()->comment('Имя файла'),
            'mime_type'     => $this->string(100)->notNull(),
            'description'   => $this->string(150)->null()->comment('Описание'),
            'size'          => $this->integer()->notNull()->comment('Размер в байтах'),
            'file_hash'     => $this->string(64)->notNull()->comment('Хеш файла для поиска дубликатов'),
            'extension'     => $this->string(50)->notNull()->comment('Расширение файла'),

//            'status'        => $this->smallInteger()->notNull()->defaultValue(10)->comment('Статус: 1-удален, 5-временный, 10-активный'),
//            'is_draft'      => $this->boolean()->notNull()->defaultValue(true)->comment('Ещё не привязан к сущности'),

            'created_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // TODO: добавить индексы!

/*

Как сейчас в мтс

CREATE TABLE `upload` (
  `id` int(11) NOT NULL,
  `type` smallint(6) UNSIGNED NOT NULL,
  `sub_type` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `multiple_id` int(11) UNSIGNED DEFAULT NULL,
  `section` varchar(20) NOT NULL DEFAULT '0',
  `folder` varchar(40) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mime` varchar(150) NOT NULL,
  `extension` varchar(15) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `user_create` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` varchar(20) NOT NULL,
  `moved` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `upload`
--
ALTER TABLE `upload`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_upload_name` (`name`),
  ADD KEY `idx_upload_combined` (`type`,`sub_type`,`multiple_id`),
  ADD KEY `idx_upload_type` (`type`),
  ADD KEY `idx_upload_sub_type` (`sub_type`),
  ADD KEY `idx_upload_multiple_id` (`multiple_id`),
  ADD KEY `idx-upload-moved` (`moved`);

 */

/*
 * так предлагает клауди
        $this->createTable('files', [
            'id'          => $this->primaryKey(),
            'uuid'        => $this->string(36)->notNull()->unique()->comment('Публичный идентификатор файла'),

            // Полиморфная связь
            'entity_type' => $this->string(50)->null()->comment('Тип сущности: user, category, service'),
            'entity_id'   => $this->integer()->null()->comment('ID сущности'),

            // Мета-данные файла
            'disk'        => $this->string(20)->notNull()->defaultValue('local')->comment('Хранилище: local, s3'),
            'path'        => $this->string(500)->notNull()->comment('Путь к файлу на диске'),
            'url'         => $this->string(500)->notNull()->comment('Публичный URL'),
            'filename'    => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'mime_type'   => $this->string(100)->notNull(),
            'size'        => $this->integer()->notNull()->comment('Размер в байтах'),
            'collection'  => $this->string(50)->notNull()->defaultValue('default')->comment('Коллекция: avatar, gallery, cover'),

            // Черновик
            'is_draft'    => $this->boolean()->notNull()->defaultValue(true)->comment('Ещё не привязан к сущности'),
            'expires_at'  => $this->integer()->null()->comment('Когда удалить черновик'),

            'created_at'  => $this->integer()->notNull(),
            'updated_at'  => $this->integer()->notNull(),
        ]);

        // Основной индекс для поиска файлов конкретной сущности
        $this->createIndex('idx_files_entity', 'files', ['entity_type', 'entity_id', 'collection']);

        // Индекс для cron — поиск просроченных черновиков
        $this->createIndex('idx_files_drafts', 'files', ['is_draft', 'expires_at']);

        $this->createIndex('idx_files_uuid', 'files', 'uuid', true);
*/

        /*
        А так deep seek
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),

            // Основные поля
            'entity_type' => $this->string(50)->notNull()->comment('Тип сущности: user, category, promotion и т.д.'),
            'entity_id' => $this->integer()->notNull()->comment('ID сущности'),
            'field_name' => $this->string(50)->notNull()->defaultValue('default')->comment('Поле сущности: avatar, photo, logo и т.д.'),

            // Информация о файле
            'original_name' => $this->string(255)->notNull()->comment('Оригинальное имя файла'),
            'file_name' => $this->string(255)->notNull()->comment('Сгенерированное имя файла'),
            'file_path' => $this->string(500)->notNull()->comment('Путь к файлу'),
            'file_hash' => $this->string(64)->notNull()->comment('Хеш файла для поиска дубликатов'),
            'file_size' => $this->bigInteger()->notNull()->comment('Размер в байтах'),
            'mime_type' => $this->string(100)->notNull()->comment('MIME тип'),
            'extension' => $this->string(10)->notNull()->comment('Расширение файла'),

            // Дополнительные параметры
            'width' => $this->integer()->null()->comment('Ширина изображения'),
            'height' => $this->integer()->null()->comment('Высота изображения'),
            'alt_text' => $this->string(255)->null()->comment('Alt текст'),
            'title' => $this->string(255)->null()->comment('Заголовок'),
            'description' => $this->text()->null()->comment('Описание'),

            // Сортировка и статус
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('Порядок сортировки'),
            'is_main' => $this->boolean()->notNull()->defaultValue(0)->comment('Основной файл'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('Статус: 1-удален, 5-временный, 10-активный'),

            // Метаданные
            'metadata' => $this->json()->null()->comment('Дополнительные метаданные'),
            'created_by' => $this->integer()->null()->comment('Кто создал'),
            'created_at' => $this->integer()->notNull()->comment('Время создания'),
            'updated_at' => $this->integer()->notNull()->comment('Время обновления'),

        ], $tableOptions);

                // Создаем индексы
        $this->createIndex('idx_files_entity', '{{%files}}', ['entity_type', 'entity_id', 'field_name']);
        $this->createIndex('idx_files_status', '{{%files}}', 'status');
        $this->createIndex('idx_files_hash', '{{%files}}', 'file_hash');
        $this->createIndex('idx_files_created_by', '{{%files}}', 'created_by');

        // Внешний ключ для created_by (если есть таблица user)
        $this->addForeignKey(
            'fk_files_created_by',
            '{{%files}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

         */


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
    }
}
