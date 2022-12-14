<?php

namespace app\models;

use dicr\telegram\entity\Chat;
use yii\db\ActiveRecord;
use yii\debug\UserswitchAsset;

/**
 * @property $id int
 * @property $first_name string
 * @property $second_name string
 * @property $tg_user_id int
 * @property $tg_chat_id int
 * @property $user_type int
 * @property $tg_user_name string
 * @property-read null $authKey
 * @property $registration_date string [dateTime]
 *
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
//    public $id;
//    public $username;
//    public $password;
//    public $authKey;
//    public $accessToken;
    public const POSITION_ADMIN = 1;
    public const POSITION_USER = 2;
    public const POSITION_OPER = 3;
    public const POSITION_KLADMAN = 4;


//    private static $users = [
//        '100' => [
//            'id' => '100',
//            'username' => 'admin',
//            'password' => 'admin',
//            'authKey' => 'test100key',
//            'accessToken' => '100-token',
//        ],
//        '101' => [
//            'id' => '101',
//            'username' => 'demo',
//            'password' => 'demo',
//            'authKey' => 'test101key',
//            'accessToken' => '101-token',
//        ],
//        '102' => [
//            'id' => '102',
//            'username' => 'demo',
//            'password' => 'demo',
//            'authKey' => 'test102key',
//            'accessToken' => '102-token',
//        ],
//    ];

    public static function tableName(): string
    {
        return '{{%users}}';
    }

    public function rules()
    {
        return [
            ['user_type', 'default', 'value' => self::POSITION_USER],
            [['tg_chat_id', 'tg_user_id', 'user_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        foreach (self::$users as $user) {
//            if (strcasecmp($user['username'], $username) === 0) {
//                return new static($user);
//            }
//        }

        return null;
    }

    /**
     * Tworzy nowego u??ytkownika w BOT.
     *
     * @param \dicr\telegram\entity\User|null $telegramUser
     * @param Chat $chat Czat uzytkownika, z kt??rego si?? odbywa rejestracja
     * @return User
     */
    public static function create(?\dicr\telegram\entity\User $telegramUser, Chat $chat): User
    {
        $newUser = new User();
        $newUser->first_name = $telegramUser->firstName;
        $newUser->second_name = $telegramUser->lastName;
        $newUser->tg_chat_id = $chat->id;
        $newUser->tg_user_id = $telegramUser->id;
        $newUser->tg_user_name = $telegramUser->userName;
        $newUser->registration_date = (new \DateTime())->format('Y-m-d H:i:s');
        $newUser->save();

        return $newUser;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function getReplayMarkupKeyboard($command)
    {
        if ($command != '/start' && $this->isUser()) {
            return ['remove_keyboard' => true];
        }
        switch ($command) {
            //
            // Sterowanie wej????iem
            //
            case '/start':
                $replyMarkup = [
                    'keyboard' => [
                        ['????????'],
                    ],
                    'resize_keyboard' => true
                ];
                break;
            case '????????':
                if ($this->isAdmin()) {
                    $replyMarkup = [
                        'keyboard' => [
                            ['????????????????????'],
                            ['????????????????????'],
                            ['??????????'],
                            ['??????????????????????????????'],
                        ],
                        'resize_keyboard' => true
                    ];
                } elseif ($this->isOperator()) {
                    $replyMarkup = [
                        'keyboard' => [
                            ['???????????? ?????????????? ????????'],
                            ['??????????'],
                            ['??????????'],
                        ],
                        'resize_keyboard' => true
                    ];
                } elseif ($this->isKladman()) {
                    $replyMarkup = [
                        'keyboard' => [
                            ['???????????? ?????????????? ????????'],
                            ['??????????'],
                            ['?????????????? ????????'],
                        ],
                        'resize_keyboard' => true
                    ];
                } elseif ($this->isUser()) {
                    $replyMarkup = [
                        'remove_keyboard' => true
                    ];
                }
                break;
            //
            //Sterowanie magazynem
            //
            case '??????????':
                if ($this->hasStoreAccess()) {
                    if ($this->isAdmin()) {
                        $replyMarkup = [
                            'keyboard' => [
                                ['???????????????????? ??????????????'],
                                ['???????????????? ??????????????'],
                                ['??????????']
                            ],
                            'resize_keyboard' => true
                        ];
                    } elseif ($this->isOperator()) {
                        $replyMarkup = [
                            'keyboard' => [
                                ['???????????? ????????????'],
                                ['?????????? ???????????????????????????'],
                            ],
                            'resize_keyboard' => true
                        ];
                    }
                } else {
                    return ['remove_keyboard' => true];
                }
                break;
            case '???????????????????? ??????????????':
                if (!$this->isAdmin()) {
                    return ['remove_keyboard' => true];
                } else {
                    $replyMarkup = [
                        'keyboard' => [
                            ['???????? ??????????'],
                            ['??????????'],
                        ],
                        'resize_keyboard' => true
                    ];
                }
                break;
            case '?????????????????? ??????????????':
                if (!$this->isAdmin()) {
                    return ['remove_keyboard' => true];
                } else {
                    $replyMarkup = [
                        'keyboard' => [
                            ['?????? ????????????????'],
                            ['??????????']
                        ],
                        'resize_keyboard' => true
                    ];
                }
                break;
            case '???????? ??????????':
                if (!$this->isAdmin()) {
                    return ['remove_keyboard' => true];
                } else {
                    $replyMarkup = [
                        'keyboard' => [
                            ['??????????'],
                            ['????????????????'],
                            ['??????'],
                            ['??????????'],
                            ['??????????']
                        ],
                        'resize_keyboard' => true
                    ];
                }
                break;
            case '??????????':
                if (!$this->isAdmin()) {
                    return ['remove_keyboard' => true];
                } else {
                    $replyMarkup['keyboard'] = Products::getProductsByTypeForKeyboard(Products::TYPE_PRODUCT_ZIOLO);
                    $replyMarkup['resize_keyboard'] = true;
                }
                break;

            case 'Smell Bomb':
                if (!$this->isAdmin()) {
                    return ['remove_keyboard' => true];
                } else {
                    $replyMarkup = [
                        'keyboard' => [
                            ['???????? ????????????'],
                            ['???????? ??????????'],
                            ['???? ????????????'],
                            ['??????????']
                        ],
                        'resize_keyboard' => true
                    ];
                }
                break;
            default:
                $replyMarkup = [];
        }
        return $replyMarkup;
    }

    public function hasNoAccess(): string
    {
        return '??????????????? ????????????.';
    }

    public function isAdmin(): bool
    {
        return $this->user_type == self::POSITION_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->user_type == self::POSITION_USER;
    }

    public function isOperator(): bool
    {
        return $this->user_type == self::POSITION_OPER;
    }

    public function isKladman(): bool
    {
        return $this->user_type == self::POSITION_KLADMAN;
    }


    public function hasStoreAccess(): bool
    {
        return $this->isAdmin() || $this->isOperator();
    }

    public function isTeammate(): bool
    {
        return $this->isAdmin() || $this->isOperator() || $this->isKladman();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}
