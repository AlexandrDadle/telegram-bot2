<?php

namespace app\modules\telegramBot;

use app\components\CheckBackCommand;
use app\models\Products;
use app\models\User;
use app\models\UserMenuCommands;
use dicr\helper\ArrayHelper;
use dicr\http\HttpCompressionBehavior;
use dicr\telegram\entity\Update;
use dicr\telegram\request\SendMessage;
use dicr\telegram\request\SetWebhook;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * telegramBot module definition class
 */
class TelegramModule extends \dicr\telegram\TelegramModule
{

    public function installWebHook(): void
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

    /** @var Client */
    private $_httpClient;

    /**
     * Клиент HTTP.
     *
     * @return Client
     * @throws InvalidConfigException
     */
    public function httpClient(): Client
    {
        if ($this->_httpClient === null) {
            $this->_httpClient = Yii::createObject(array_merge([
                'class' => Client::class,
                'baseUrl' => $this->apiUrl . '/bot' . $this->botToken,
                'as compression' => HttpCompressionBehavior::class
            ], $this->httpClientConfig ?: []));
        }

        return $this->_httpClient;
    }

    public function handle(Update $update): bool
    {
        $command = new UserMenuCommands();
        $text = $update->message->text;
        $userID = $update->message->from->id;
        $chatID = $update->message->chat->id;
        $user = User::findOne(['tg_user_id' => $userID]);
        if (!$user) {
            $user = User::create($update);
        }
        if (in_array($text, Yii::$app->params['backCommands'])){
            UserMenuCommands::addCommand($text, $userID);
        }

        if (!empty($text)) {
            switch ($text) {
                case '/start':
                    $messageText = 'Login: ';
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup);
                    break;
                case 'Вход':
                    if (!$user->isTeammate()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = "
```
📇 $user->first_name

🆔 $user->tg_user_id
```
";
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup, SendMessage::PARSE_MODE_MARKDOWN_V2);
                    break;
                case 'Склад':
                    if (!$user->hasStoreAccess()) {
                        $messageText = $user->hasNoAccess();
                    } else {
                        $messageText = "
```
       📦Склад            
      
 • Шишки      - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_ZIOLO) . "
 
 • Амфетамин  - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_BIALKO) . "
 
 • ЛСД        - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_KWAS) . "
 
 • Гашиш      - " . Products::getAllCountInStockByType(Products::TYPE_PRODUCT_GASH) . "
 
```
                ";
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($text);
                    $this->sendMessage($chatID, $messageText, $replyMarkup, SendMessage::PARSE_MODE_MARKDOWN_V2);
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
                case 'Назад':
                    $lastCommand = $command->getLastUserCommand();
                    $messageText = $lastCommand;
                    $parseMode = null;
                    if ($lastCommand == 'Вход') {
                        $messageText = "
```
📇 $user->first_name
                            
🆔 $user->tg_user_id
```
                            ";
                        $parseMode = SendMessage::PARSE_MODE_MARKDOWN_V2;
                    }
                    $replyMarkup = $user->getReplayMarkupKeyboard($lastCommand);
                    $this->sendMessage($chatID, $messageText, $replyMarkup, $parseMode);
                    break;
                default:
                    $textMessage = 'Неизвестная команда';
                    $this->sendMessage($chatID, $textMessage);
            }

        }
        return true;
    }

    public function sendMessage($chatID, $messageText, $replyMarkup = null, $parseMode = null)
    {
        if ($replyMarkup) {
            $encodedMarkup = json_encode($replyMarkup);
            /** @var SendMessage $request Формируем запрос */
            $request = $this->createRequest([
                'class' => SendMessage::class,
                'chatId' => $chatID,
                'text' => $messageText,
                'parseMode' => $parseMode,
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
        try {
            Yii::error($request->attributes, 'webhook');
            $request->send();
        } catch (\Exception $exception) {
            $this->sendMessage($chatID, $exception->getMessage(), []);
        }

    }


}
