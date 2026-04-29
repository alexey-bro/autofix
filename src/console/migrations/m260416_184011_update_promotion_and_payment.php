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
        $this->addColumn('{{%promotion}}', 'amount', $this->decimal(12, 2)->defaultValue(0)->after('master_id'));
        $this->addColumn('{{%promotion}}', 'payment_id',  $this->string()->null());

        $this->dropColumn('{{%payment}}', 'paid');
        $this->dropColumn('{{%payment}}', 'amount');
        $this->alterColumn('{{%payment}}', 'payment_id', $this->string()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->addColumn('{{%promotion}}', 'order', $this->integer()->unsigned());
        $this->dropColumn('{{%promotion}}', 'durationDays');
        $this->dropColumn('{{%promotion}}', 'amount');
        $this->dropColumn('{{%promotion}}', 'payment_id');

        $this->addColumn('{{%payment}}', 'paid', $this->boolean()->notNull()->defaultValue(0));
        $this->addColumn('{{%payment}}', 'amount', $this->decimal(12, 2)->defaultValue(0));

    }
}
