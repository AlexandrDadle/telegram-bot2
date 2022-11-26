<?php

namespace app\modules\telegramBot;

use app\components\CheckBackCommand;
use app\models\Products;
use app\models\User;
use dicr\telegram\entity\Update;
use dicr\telegram\request\SendMessage;
use dicr\telegram\request\SetWebhook;
use Yii;
use yii\helpers\Json;

/**
 * telegramBot module definition class
 */
class TelegramModule extends \dicr\telegram\TelegramModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\telegramBot\controllers';


    public function installWebHook() : void
    {
        /** @var SetWebhook $request */
        $request = $this->createRequest([
            'class' => SetWebhook::class,
            'url' => 'https://www.dadle-service.shop/telegramBot/bot',
            'maxConnections' => 100,
        ]);

        // при ошибке будет Exception
        $request->send();

        Yii::debug('Установлен webhook: ' . $request->url, __METHOD__);
    }

    public function handle(Update $update): bool
    {
        $command = new CheckBackCommand;
        $messageText = $update->message->text;
        $userName = $update->message->from->userName;
        $userID = $update->message->from->id;
        $chatID = $update->message->chat->id;

        $user = User::findOne(['tg_user_id' => $userID]);
        if (!$user) {
            $user = User::create($update);
        }
        if (!empty($messageText)) {
            switch ($messageText) {
                case '/start':
                    $messageText = 'Login: ';
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
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
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
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
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Добавить продукт':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'http://www.shop-bot/product/create';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;


                //Typ produktu
                case 'Smell Bomb':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'Smell Bomb';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;

                case 'Цена брутто':
                    if (!$user->isAdmin()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = 'Цена брутто';
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($messageText);
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
        return true;
    }

    public function sendMessage($chatID, $messageText, $replyMarkup)
    {
        if ($replyMarkup) {
            $encodedMarkup = json_encode($replyMarkup);
            /** @var SendMessage $request Формируем запрос */
            $request = $this->createRequest([
                'class' => SendMessage::class,
                'chatId' => $chatID,
                'text' => $messageText,
                'parseMode' => SendMessage::PARSE_MODE_MARKDOWN_V2,
                'replyMarkup' => $encodedMarkup,
            ]);
        } else {
            /** @var SendMessage $request Формируем запрос */
            $request = $this->createRequest([
                'class' => SendMessage::class,
                'chatId' => $chatID,
                'text' => $messageText
            ]);
        }
        // отправка сообщения
        $response = $request->send();

        Yii::error($response->text, 'webhook');
    }


}
