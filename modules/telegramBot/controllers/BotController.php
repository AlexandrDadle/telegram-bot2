<?php

namespace app\modules\telegramBot\controllers;

use app\modules\telegramBot\TelegramModule;
use dicr\telegram\entity\Update;
use dicr\telegram\entity\WebhookInfo;
use dicr\telegram\request\DeleteWebHook;
use dicr\telegram\request\GetWebhookInfo;
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

        $response = $module->createRequest(['class' => Update::class]);
        $response = $response->send();




        return $this->asJson($response);
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