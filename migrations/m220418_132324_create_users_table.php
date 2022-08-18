<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m220418_132324_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey()->unsigned(),
            'first_name' => $this->string(100),
            'second_name' => $this->string(100),
            'tg_user_id' => $this->integer()->notNull(),
            'tg_chat_id' => $this->integer()->notNull(),
            'user_type' => $this->integer()->notNull(),
            'tg_user_name' => $this->string(),
            'registration_date' => $this->dateTime(),
        ]);
        $this->addCommentOnColumn('{{%users}}', 'tg_user_id', 'ID użytkownika w Telegramie');
        $this->addCommentOnColumn('{{%users}}', 'tg_chat_id', 'ID czatu użytkownika w z botem');
        $this->addCommentOnColumn('{{%users}}', 'user_type', 'Typ użytkownika');
        $this->addCommentOnColumn('{{%users}}', 'tg_user_name', 'Nazwa użytkownika w Telegramie');
        $this->addCommentOnColumn('{{%users}}', 'registration_date', 'Data zalozenia konta');
        $this->addCommentOnTable('{{%users}}', 'Tabela z użytkowanikamy w BOT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
