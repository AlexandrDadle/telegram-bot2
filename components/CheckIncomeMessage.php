<?php

namespace app\components;


use app\models\Products;
use app\models\User;
use dicr\telegram\entity\ReplyKeyboardRemove;
use dicr\telegram\entity\Update;
use dicr\telegram\request\SendMessage;
use dicr\telegram\TelegramModule;
use http\Message;
use http\Url;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * @prop
 */
class CheckIncomeMessage extends BaseObject
{
    /** @var TelegramModule|null  */
    private $telegramModule = null;
    public $prodName;
    public $prodType;
    public $prodBrutto;
    public $prodNetto;
    public $prodCount;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->telegramModule == null) {
            /** @var TelegramModule $module получаем модуль */
            $this->telegramModule = Yii::$app->get('telegram');
            $this->telegramModule->handler = [$this, 'webhookResponse'];
        }
    }

    public function webhookResponse(Update $update, TelegramModule $module)
    {
        Yii::debug(['update' => $update], 'webhook');
    }



    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function handleUpdate(Update $update)
    {
        $command = new CheckBackCommand;
        $prod = new Products();
        $text = $update->message->text;
        $chatID = $update->message->chat->id;
        $userId = $update->message->from->id;
        $user = User::findOne(['tg_user_id' => $userId]);
        if (!$user) {
            $user = User::create($update->message->from, $update->message->chat);
        }
        if (!empty($text)) {
            switch ($text) {
                case '/start':
                    $messageText = 'Login: ';
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Вход':
                    $command->setEmptyCommandFile($update->message->from->id);
                    if (!$user->isTeammate()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = "
```

📇  $user->first_name

🆔 $user->tg_user_id

```
                ";
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Склад':
                    if (!$user->hasStoreAccess()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = "
```
      📦Склад
      
 • Шишки      -  " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_ZIOLO) . "
 
 • Амфетамин  - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_BIALKO) . "
 
 • ЛСД        - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_KWAS) . "
 
 • Гашиш      - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_GASH) . "
 
 
 
```
                ";
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Доступные позиции':
                case 'Шишки':
                case 'Тип продукта':
                case 'Весь товар':
                case 'Управление складом':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = $text;
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Добавить продукт':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'http://www.shop-bot/product/create';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;


                //Typ produktu
                case 'Smell Bomb':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'Smell Bomb';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;

                case 'Цена брутто':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'Цена брутто';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Назад':
                    $lastCommand = $command->getLastCommand($update->message->from->id);
                    $messageText = $lastCommand;
                    $replyMarkup = $user->getReplayMarkupKeyboard($lastCommand);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;

            }

        }
    }

    /**
     * @throws Exception
     */

}