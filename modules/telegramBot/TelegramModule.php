<?php

namespace app\modules\telegramBot;

use app\modules\telegramBot\controllers\BotController;
use dicr\helper\Url;
use dicr\telegram\request\SetWebhook;
use Yii;

/**
 * telegramBot module definition class
 */
class TelegramModule extends \dicr\telegram\TelegramModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\telegramBot\controllers';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->handler = [BotController::class, 'webhookResponse'];
    }

    public function installWebHook() : void
    {
        /** @var SetWebhook $request */
        $request = $this->createRequest([
            'class' => SetWebhook::class,
            'url' => '217.77.219.102:8443/telegramBot/bot',
        ]);

        // при ошибке будет Exception
        $request->send();

        Yii::debug('Установлен webhook: ' . $request->url, __METHOD__);
    }



}
