<?php

namespace app\components\telegramRequests;



use dicr\telegram\entity\Message;
use dicr\telegram\entity\Update;
use dicr\telegram\TelegramRequest;
use yii\base\Exception;

class GetUpdates extends TelegramRequest
{

    public $offset;
    public $limit;
    public $timeout;


    public function func(): string
    {
        return 'getUpdates';
    }

    /**
     * @inheritDoc
     * @return array отправленное сообщение
     * @throws Exception
     */
    public function send(): array
    {
        $res = [];
        $updatesArray = parent::send();
        foreach ($updatesArray as $update){
            $res[] = new Update(['json' => $update]);
        }
        return $res;

    }
}