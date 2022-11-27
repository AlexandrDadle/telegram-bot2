<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bot_user_menu_commands".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $command
 */
class UserMenuCommands extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_user_menu_commands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['command'], 'required'],
            [['command'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Юзера',
            'command' => 'Команда',
        ];
    }

    /**
     * @param $command string Команда введенная пользователем
     * @param $userID integer ID пользователя
     * @return void
     */
    public static function addCommand(string $command, int $userID)
    {
        $object = new UserMenuCommands();
        $object->user_id = $userID;
        $object->command = $command;
        $object->save();
    }

    /**
     * @return UserMenuCommands|array|ActiveRecord|null
     */
    public function getLastUserCommand()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->orderBy('fld_id DESC')->one();
    }
}
