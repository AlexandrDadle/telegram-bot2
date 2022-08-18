<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amount}}`.
 */
class m220419_093108_create_amount_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%amount}}', [
            'id' => $this->primaryKey(),
            'waga' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%amount}}');
    }
}
