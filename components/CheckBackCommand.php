<?php

namespace app\components;


use app\modules\telegramBot\TelegramModule;
use dicr\helper\ArrayHelper;
use Yii;

class CheckBackCommand
{

    public static array $commands =
        [
            'Вход',
            'Статистика',
            'Менеджмент',
            'Склад',
            'Весь товар',
            'Ценообразование',
            'Управление складом',
            'Доступные позиции',
            'Шишки',
            'Добавить продукт',
            'Тип продукта'
        ];

    const SEPORATOR = '|';
    private static string $offSetFilePath = '@app/tmp';
    private static string $fileName = 'lastcommand.txt';


    public function setCommand($message, $id)
    {
        if (in_array($message, self::$commands)) {
            $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . $id . '_' . self::$fileName);
            $file = fopen($path, 'r+');
            $wholeLine = fgets($file); // Прочитал
            if (empty($wholeLine)) {
                $arrayCommands = [];
            } else {
                $arrayCommands = explode(self::SEPORATOR, $wholeLine);
            }
            if (end($arrayCommands) == $message) {
                fclose($file);
            } else {
                $arrayCommands[] = $message;
                $arrayCommands = implode(self::SEPORATOR, $arrayCommands);
                ftruncate($file, 0);
                rewind($file);
                fwrite($file, $arrayCommands);
                fclose($file);
            }

        }
    }

    public function getLastCommand($id)
    {
        $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . $id . '_' . self::$fileName);
        $file = fopen($path, 'a+');
        $wholeLine = fgets($file); // Прочитал
        $arrayCommands = explode(self::SEPORATOR, $wholeLine);
        array_pop($arrayCommands);
        $len = count($arrayCommands);
        if ($len > 0) {
            $command = $arrayCommands[$len - 1];
        } else {
            $command = 'Вход';
            $arrayCommands = [$command];
        }
        $arrayCommands = implode(self::SEPORATOR, $arrayCommands);
        ftruncate($file, 0);
        rewind($file);
        fwrite($file, $arrayCommands);
        fclose($file);
        return $command;
    }


    public function setEmptyCommandFile(int $id)
    {
        $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . $id . '_' . self::$fileName);
        $file = fopen($path, 'w+');
        fclose($file);
    }

    /**
     * Метод возвращает TRUE, если пользователь нажал Назад и попал в главное меню
     * @param $id int ID пользователя
     * @return bool
     */
    public function getIsMainMenu(int $id): bool
    {
        $path = Yii::getAlias(self::$offSetFilePath . DIRECTORY_SEPARATOR . $id . '_' . self::$fileName);
        $file = fopen($path, 'a+');
        $data = fgets($file);
        $dataArray = explode(self::SEPORATOR, $data);
        if ($dataArray[0] == 'Вход'){
            fclose($file);
            return true;
        }
        fclose($file);
        return false;
    }
}