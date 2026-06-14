<?php

use yii\db\Migration;

class m260611_134111_update_token_add_fcm_token extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%user_tokens}}', 'device_token', $this->string()->defaultValue(null));
        $this->addColumn('{{%user_tokens}}', 'device_type', $this->smallInteger()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_tokens}}', 'device_token');
        $this->dropColumn('{{%user_tokens}}', 'device_type');
    }

}
