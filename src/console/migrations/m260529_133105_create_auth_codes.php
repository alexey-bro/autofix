<?php

use yii\db\Migration;

class m260529_133105_create_auth_codes extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auth_codes}}', [
            'id'         => $this->primaryKey(),
            'email'      => $this->string(255)->notNull(),
            'code'       => $this->string(16)->notNull(),
            'type'       => $this->string(20)->notNull(), // 'login' | 'register'
            'attempts'   => $this->smallInteger()->defaultValue(0)->notNull(),
            'is_used'    => $this->boolean()->defaultValue(false)->notNull(),
            'ip'         => $this->string(45)->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'expired_at' => $this->timestamp()->notNull(),
        ]);

        $this->createIndex('idx_auth_codes_email',   '{{%auth_codes}}', 'email');
        $this->createIndex('idx_auth_codes_code',   '{{%auth_codes}}', 'code');
        $this->createIndex('idx_auth_codes_expired', '{{%auth_codes}}', 'expired_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_codes}}');
    }

}