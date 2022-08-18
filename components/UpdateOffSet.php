<?php
namespace app\components;


use app\components\telegramRequests\GetUpdates;
use dicr\telegram\TelegramModule;
use Yii;
use yii\base\InvalidConfigException;

class UpdateOffSet
{
    private $offSet = 0;
    private static $offSetFilePath = '@app/tmp';
    private static $fileName = 'offset.txt';
    private $count = 100;

    /**
     * @throws InvalidConfigException
     */
    public function getUpdateRequest()
    {
        $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . self::$fileName);
        $file = fopen($path, 'r+');
        $lastOffSet = fgets($file, 4096);
        $this->offSet = $lastOffSet;
        if ($lastOffSet) {
            $this->offSet = 1 + $lastOffSet;
        }
        fclose($file);

        /** @var TelegramModule $module получаем модуль */
        $module = Yii::$app->get('telegram');

        return $module->createRequest([
            'class' => GetUpdates::class,
            'offset' => $this->offSet,
            'limit' => $this->count,

        ]);
    }
    public function setUpdateId($id)
    {
        $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . self::$fileName);
        $file = fopen($path, 'w+');
        fwrite($file, $id);
        fclose($file);
    }
}