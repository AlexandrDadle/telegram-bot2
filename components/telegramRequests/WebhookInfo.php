<?php

namespace app\components\telegramRequests;

use dicr\telegram\request\GetWebhookInfo;
use dicr\telegram\TelegramRequest;

class WebhookInfo extends TelegramRequest
{

    public function func(): string
    {
        return 'getWebhookInfo';
    }
}