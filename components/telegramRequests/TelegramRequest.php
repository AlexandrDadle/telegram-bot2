<?php

namespace app\components\telegramRequests;

use app\modules\telegramBot\TelegramModule;
use dicr\telegram\TelegramResponse;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;

abstract class TelegramRequest extends \dicr\telegram\TelegramRequest
{
    /** @var TelegramModule */
    protected $module;

    /**
     * @inheritDoc
     */
    abstract function func(): string;

    /**
     * Конструктор.
     *
     * @param TelegramModule $module
     * @param array $config
     */
    public function __construct(TelegramModule $module, array $config = [])
    {
        $this->module = $module;

        parent::__construct($module,$config);
    }

    /**
     * Отправляет запрос.
     *
     * @return array Ответ (переопределяется в наследуемом классе)
     * @throws Exception
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function send()
    {
        // фильтруем данные
        $data = array_filter(
            $this->json,
            static fn($val): bool => $val !== null && $val !== '' && $val !== []
        );

        // создаем запрос
        $req = $this->module->httpClient()
            ->post($this->func(), $data, [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]);

        // получаем ответ
        Yii::error('Запрос: ' . $req->toString(), __METHOD__);
        $result = $req->send();
        Yii::error('Ответ: ' . $result->toString(), __METHOD__);

        if (! $result->isOk) {
            throw new Exception('HTTP-error: ' . $result->statusCode);
        }

        // формируем ответ Telegram
        $result->format = Client::FORMAT_JSON;
        $tgResponse = new TelegramResponse([
            'json' => $result->data
        ]);

        // обработка ошибок
        if (empty($tgResponse->ok)) {
            // если запрос был отфильтрован из-за flood-фильтра, то повторяем запрос
            $retryAfter = (int)$tgResponse->parameters->retryAfter;

            if (! empty($retryAfter)) {
                Yii::error(
                    'Сработал flood-фильтр, ожидаем ' . $retryAfter . ' секунд ...', __METHOD__
                );

                // спим ...
                sleep($retryAfter);

                // повторяем отправку запроса
                return $this->send();
            }

            throw new Exception('Ошибка отправки запроса: ' . $tgResponse->description);
        }

        // возвращаем результат
        return $tgResponse->result;
    }
}