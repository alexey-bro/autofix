<?php

use yii\db\Migration;

class m260416_184011_update_promotion_and_payment extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%promotion}}', 'order');
        $this->addColumn('{{%promotion}}', 'durationDays', $this->smallInteger()->unsigned()->after('isPaid'));

        $this->dropColumn('{{%payment}}', 'paid');
        $this->alterColumn('{{%payment}}', 'payment_id', $this->string()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->addColumn('{{%promotion}}', 'order', $this->integer()->unsigned());
        $this->dropColumn('{{%promotion}}', 'durationDays');

        $this->addColumn('{{%payment}}', 'paid', $this->boolean()->notNull()->defaultValue(0));

        $this->alterColumn('{{%payment}}', 'payment_id', $this->integer()->unsigned()->notNull());

    }
}
