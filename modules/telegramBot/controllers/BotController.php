<?php

namespace app\modules\telegramBot\controllers;

use app\modules\telegramBot\TelegramModule;
use dicr\telegram\entity\Message;
use dicr\telegram\entity\Update;
use dicr\telegram\entity\WebhookInfo;
use dicr\telegram\request\DeleteWebHook;
use dicr\telegram\request\GetWebhookInfo;
use dicr\telegram\request\SendMessage;
use dicr\telegram\TelegramResponse;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * @property TelegramModule $module
 */
class BotController extends Controller
{
    /**
     * @inheritDoc
     * Отключаем CSRF для запросов от Telegram.
     */
    public $enableCsrfValidation = false;


    public static function webhookResponse(Update $update, TelegramModule $module)
    {
        Yii::debug(['update' => $update], 'webhook');
    }

    public function actionIndex(): Response
    {
        /** @var TelegramModule $module */
        $module = Yii::$app->get('telegram');

        $response = Yii::$app->request->post();

        Yii::error($response);

        $request = $this->telegramModule->createRequest([
            'class' => SendMessage::class,
            'chatId' => '347236018',
            'text' => $response['text'],
        ]);
        $request->send();


        return true;
    }

    public function actionSetWebHook(): Response
    {
        $this->module->installWebHook();

        return $this->asJson([]);
    }

    public function actionDeleteWebHook()
    {
        /** @var TelegramModule $module */
        $module = Yii::$app->get('telegram');

        $webhook = $module->createRequest(['class' => DeleteWebHook::class]);

        $response = $webhook->send();

        return $this->asJson($response);

    }

    public function actionWebHookInfo()
    {
        /** @var TelegramModule $module */
        $module = Yii::$app->get('telegram');

        $webhook = $module->createRequest(['class' => GetWebhookInfo::class]);

        $response = $webhook->send();

        return $this->asJson($response);

    }


}