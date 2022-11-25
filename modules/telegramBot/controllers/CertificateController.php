<?php

namespace app\modules\telegramBot\controllers;

class CertificateController extends \yii\web\Controller
{

    public function actionUpload()
    {
        return $this->render('form-upload', ['token' => \Yii::$app->params['token']]);
    }
}