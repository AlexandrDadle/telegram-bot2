<?php

namespace app\modules\telegramBot;

use dicr\telegram\entity\Update;
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

    public function handle(Update $update)
    {
//        Yii::error([unserialize($update->message), 'return' => 'true'], 'webhook');
//        Yii::error([Json::decode($update->message), 'return' => 'true'], 'webhook');
        Yii::error([$update->message->attributes, 'return' => 'true'], 'webhook');
        Yii::error([$update->message->from->attributes, 'return' => 'true'], 'webhook');
        Yii::error([$update->message->chat->attributes, 'return' => 'true'], 'webhook');

        return true;
    }


}
