<?php

namespace app\controllers;

use app\components\CheckBackCommand;
use app\components\CheckIncomeMessage;
use app\components\telegramRequests\GetUpdates;
use app\components\UpdateOffSet;
use app\models\Products;
use app\models\User;
use dicr\telegram\entity\InlineKeyboardMarkup;
use dicr\telegram\entity\KeyboardButton;
use dicr\telegram\entity\Message;
use dicr\telegram\entity\ReplyKeyboardMarkup;
use dicr\telegram\entity\Update;
use dicr\telegram\entity\WebhookInfo;
use dicr\telegram\request\GetWebhookInfo;
use dicr\telegram\request\SendMessage;
use dicr\telegram\request\SetWebhook;
use dicr\telegram\TelegramModule;
use dicr\telegram\TelegramResponse;
use dicr\telegram\WebhookController;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /** @var \app\modules\telegramBot\TelegramModule $module получаем модуль */
        $module = TelegramModule::getInstance();

        $update = $module->createRequest([
            'class' => Update::class,
        ]);
        $response = $update->send();

        return $this->render('test', ['response' =>  $response]);

    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetUpdates(): string
    {
        $offset = new UpdateOffSet();
        $command = new CheckBackCommand();
        $request = $offset->getUpdateRequest();
        $response = $request->send();
        $lastUpdate = count($response) - 1;
        if ($lastUpdate >= 0) {
            $offset->setUpdateId($response[$lastUpdate]->updateId);
        }
        $message = new CheckIncomeMessage();

        /** @var Update $update */
        foreach ($response as $update) {
            $message->handleUpdate($update);
            $command->setCommand($update->message->text, $update->message->from->id);
        }
        return $this->render('getupdates', ['updates' => $response]);
    }

    public function actionWebHookInfo()
    {
        /** @var TelegramModule $module получаем модуль */
        $module = Yii::$app->get('telegram');

        $request = $module->createRequest([
            'class' => GetWebhookInfo::class,

        ]);
        $response = $request->send();
        return $this->render('webhookinfo', ['hookinfo' => $response]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionSetWebHook()
    {
        /** @var TelegramModule $module Получаем модуль */
        $module = Yii::$app->get('telegram');

        $webhook = $module->createRequest([
            'class' => SetWebhook::class,
            'url' => 'https://www.dadle-service.shop/',
            'maxConnections' => 100,
        ]);
        $webhook->send();
//        return $webhook;
    }


}
