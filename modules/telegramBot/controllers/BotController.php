<?php

namespace app\modules\telegramBot\controllers;

use app\modules\telegramBot\TelegramModule;
use dicr\helper\ArrayHelper;
use dicr\telegram\entity\Message;
use dicr\telegram\entity\Update;
use dicr\telegram\entity\WebhookInfo;
use dicr\telegram\request\DeleteWebHook;
use dicr\telegram\request\GetWebhookInfo;
use dicr\telegram\request\SendMessage;
use dicr\telegram\TelegramRequest;
use dicr\telegram\TelegramResponse;
use Yii;
use yii\web\BadRequestHttpException;
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
        if (! Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        Yii::debug('Webhook: ' . Yii::$app->request->rawBody);

        $ret = true;

        // вызываем пользовательский обработчик
        if (! empty($this->module->handler)) {
            $update = new Update([
                'json' => Yii::$app->request->bodyParams
            ]);

            $ret = call_user_func($this->module->handler, $update, $this->module);
        }

        return $this->asJson($ret);
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