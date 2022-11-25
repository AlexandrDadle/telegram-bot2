<?php

namespace app\modules\telegramBot\controllers;

use app\components\telegramRequests\WebhookInfo;
use app\modules\telegramBot\TelegramModule;
use dicr\telegram\entity\Update;
use dicr\telegram\request\GetWebhookInfo;
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
//        if (!Yii::$app->request->isPost) {
//            throw new BadRequestHttpException();
//        }

        Yii::debug('Webhook: ' . Yii::$app->request->rawBody,  'webhook');

        $ret = true;

        // вызываем пользовательский обработчик
        if (!empty($this->module->handler)) {
            $update = new Update([
                'json' => Yii::$app->request->bodyParams
            ]);

            $ret = call_user_func($this->module->handler, $update, $this->module);
        }

        return $this->asJson($ret);
    }

    public function actionIndex1(): Response
    {
       $this->module->installWebHook();

        return $this->asJson([]);
    }

    public function actionIndex2(): void
    {
        /** @var GetWebhookInfo $request */
        $request = $this->module->createRequest([
            'class' => GetWebhookInfo::class,
        ]);

        $info = $request->send();

        printf("URL: %s\n", $info->url ?: '-');

        printf("HasCustomCertificate: %s\n", $info->hasCustomCertificate ? 'yes' : 'no');

        printf("PendingUpdateCount: %d\n", $info->pendingUpdateCount);

        printf("LastErrorDate: %s\n", empty($info->lastErrorDate) ? '-' :
            date('d.m.Y H:i:s', $info->lastErrorDate)
        );

        printf("LastErrorMessage: %s\n", $info->lastErrorMessage ?: '-');
        printf("MaxConnections: %d\n", $info->maxConnections);

        printf("AllowedUpdates: %s\n", empty($info->allowedUpdates) ? '-' :
            implode(', ', $info->allowedUpdates)
        );

//        return $this->asJson(['ok' => true]);
    }
}