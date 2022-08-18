<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sales}}`.
 */
class m220419_093158_create_sales_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sales}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sales}}');
    }
}
