<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_tokens}}`.
 */
class m260426_174429_create_user_tokens_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%user_tokens}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'token'      => $this->string(64)->notNull()->unique(),
            'device_id'  => $this->string(255)->null(),   // опционально
            'expired_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-user_tokens-user_id',
            '{{%user_tokens}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE'
        );

        $this->createIndex('idx-user_tokens-token', '{{%user_tokens}}', 'token');
        $this->createIndex('idx-user_tokens-user_id', '{{%user_tokens}}', 'user_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%user_tokens}}');

    }
}
