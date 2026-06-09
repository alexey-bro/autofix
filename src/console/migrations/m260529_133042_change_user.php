<?php

use yii\db\Migration;

class m260529_133042_change_user extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user}}', 'phone', $this->string()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}