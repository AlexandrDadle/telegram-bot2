<?php
declare(strict_types = 1);
namespace app\components\telegramRequests;

use dicr\telegram\TelegramModule;
use dicr\telegram\TelegramResponse;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;

class TelegramRequest extends \dicr\telegram\TelegramRequest
{
    /** @var \app\modules\telegramBot\TelegramModule */
    protected $module;


    public function __construct(\app\modules\telegramBot\TelegramModule $module, array $config = [])
    {
        parent::__construct($module, $config);

        $this->module = TelegramModule::getInstance();

    }

    public function func(): string
    {
        return static::func();
    }

    /**
     * Отправляет запрос.
     *
     * @return array ответ (переопределяется в наследуемом классе)
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
        $res = $req->send();
        Yii::error('Ответ: ' . $res->toString(), __METHOD__);

        if (! $res->isOk) {
            throw new Exception('HTTP-error: ' . $res->statusCode);
        }

        // формируем ответ Telegram
        $res->format = Client::FORMAT_JSON;
        $tgResponse = new TelegramResponse([
            'json' => $res->data
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