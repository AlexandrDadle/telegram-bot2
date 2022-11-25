<?php

namespace app\modules\telegramBot\controllers;

use app\modules\telegramBot\TelegramModule;
use dicr\telegram\entity\Update;
use dicr\telegram\entity\WebhookInfo;
use dicr\telegram\request\DeleteWebHook;
use dicr\telegram\request\GetWebhookInfo;
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
//        if (!Yii::$app->request->isPost) {
//            throw new BadRequestHttpException();
//        }

//        Yii::debug('Webhook: ' . Yii::$app->request->rawBody,  'webhook');
//
//        $ret = true;
//
//        // вызываем пользовательский обработчик
//        if (!empty($this->module->handler)) {
//            $update = new Update([
//                'json' => Yii::$app->request->bodyParams
//            ]);
//
//            $ret = call_user_func($this->module->handler, $update, $this->module);
//        }

        /** @var TelegramModule $module */
        $module = Yii::$app->get('telegram');

        return $this->asJson(['status' => 'ok']);
    }



    public function actionIndex2(): void
    {
        /** @var GetWebhookInfo $request */
        $request = $this->module->createRequest([
            'class' => GetWebhookInfo::class,
        ]);

        $info = $request->send();

        printf("URL: %s\n", $info->url ?: '-');
        echo '<br>';

        printf("HasCustomCertificate: %s\n", $info->hasCustomCertificate ? 'yes' : 'no');
        echo '<br>';

        printf("PendingUpdateCount: %d\n", $info->pendingUpdateCount);
        echo '<br>';

        printf("LastErrorDate: %s\n", empty($info->lastErrorDate) ? '-' :
            date('d.m.Y H:i:s', $info->lastErrorDate)
        );
        echo '<br>';

        printf("LastErrorMessage: %s\n", $info->lastErrorMessage ?: '-');
        echo '<br>';
        printf("MaxConnections: %d\n", $info->maxConnections);
        echo '<br>';

        printf("AllowedUpdates: %s\n", empty($info->allowedUpdates) ? '-' :
            implode(', ', $info->allowedUpdates)
        );
        echo '<br>';

//        return $this->asJson(['ok' => true]);
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