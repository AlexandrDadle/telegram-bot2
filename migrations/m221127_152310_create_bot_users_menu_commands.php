<?php

use yii\db\Migration;

/**
 * Class m221127_152310_create_bot_users_menu_commands
 */
class m221127_152310_create_bot_users_menu_commands extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_menu_commands}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned(),
            'command' => $this->text()->notNull()
        ]);

        $this->createIndex(
            'idx-bot_users-bot_user_menu_commands',
            '{{%user_menu_commands}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-bot_users-bot_user_menu_commands',
            '{{%user_menu_commands}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_menu_commands}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221127_152310_create_bot_users_menu_commands cannot be reverted.\n";

        return false;
    }
    */
}
