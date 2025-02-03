<?php

namespace App\Actions;

use App\Application;
use App\Actions\CronValues;
use App\Telegram\TelegramApiImpl;
use App\Cache\Redis;
use App\Queue\RabbitMQ;
use Predis\Client;

class TgEvent {

    protected Application $app;
    const SEND_MES_1 = 'Укажите название события';
    const SEND_MES_2 = 'Укажите ID пользователя';
    const SEND_MES_3 = 'Укажите текст напоминания';
    const SEND_MES_4 = 'Укажите расписание в формате crontab (* * * * *)';

    private array|null $eventArray;

    private int $sendMes;


    public function __construct(Application $app) {
        $this->app = $app;
        $this->eventArray = [];
        $this->sendMes = 0;
        
    }

    protected function getRedis(): Redis {
        $client = new Client([
            'scheme' => 'tcp', 'host'   => '127.0.0.1', 'port'   => 6379,
        ]);
        return new Redis($client);
    }

    protected function getEventSender(): EventSender {
        return new EventSender($this->getTelegramApiImp(), new RabbitMQ('eventSender'));
    }

    protected function getTelegramApiImp(): TelegramApiImpl {
        return new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
    }

    protected function getCronValues(): CronValues {
        return new CronValues();
    }

    public function handleEvent(array $inMessages): void {

        $userID = key($inMessages["result"]);

        if ($this->getRedis()->get('tg_cron_set:id') == $userID && $this->getRedis()->has('tg_cron:send_message:SEND_MES')) {

            $this->sendMes = (int)$this->getRedis()->get('tg_cron:send_message:SEND_MES');

            $this->sendMessagesForSetCronData($userID);

            $this->setSaveEventData($userID, $inMessages);

            $this->sendMes += 1; 

            $this->getRedis()->set('tg_cron:send_message:SEND_MES', $this->sendMes, 300);

        }

        if ((int)$this->getRedis()->get('tg_cron:send_message:SEND_MES') == 6) {
            $this->getEventSender()->sendMessage((int)$userID, 'Расписание успешно установлено');
        }

    }

    public function sendMessagesForSetCronData(int $userID): void {

        switch ($this->sendMes) {
            case 1:
                $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_1);
                break;
            case 2:
                $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_2);
                break;
            case 3:
                $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_3);
                break;
            case 4:
                $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_4);
                break;
        }
    }

    public function setSaveEventData(int $userID, array $inMessages) {

        $key = 'tg_cron:' . $userID . ':event';

        $this->eventArray = json_decode($this->getRedis()->get( $key), true);

        switch ($this->sendMes) {
            case 2:
                $this->eventArray['name'] = $inMessages["result"][$userID][0];
                break;
            case 3:
                if (!$this->checkReceiverData((int)$inMessages["result"][$userID][0])) {
                    $this->getEventSender()->sendMessage((int)$userID, "Неверный формат ID пользователя");
                    $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_2);
                    $this->sendMes -= 1; 
                    break;
                }
                $this->eventArray['receiver'] = (int)$inMessages["result"][$userID][0];
                break;
            case 4:
                $this->eventArray['text'] = $inMessages["result"][$userID][0];
                break;
            case 5:
                if (!$this->checkCronData($inMessages["result"][$userID][0])) {
                    $this->getEventSender()->sendMessage((int)$userID, "Неверный формат расписания");
                    $this->getEventSender()->sendMessage((int)$userID, self::SEND_MES_4);
                    $this->sendMes -= 1; 
                    break;
                }
                $this->eventArray['cron'] = $inMessages["result"][$userID][0];
                break;
        }

        $this->getRedis()->set( $key, json_encode($this->eventArray, true), 300);

        // $options = json_decode($this->getRedis()->get( $key), true);

        // var_dump($options);

    }

    public function checkCronData(string $cronData): bool {
        $cronValues = $this->getCronValues()->getCronValues($cronData);
        if (!$this->getCronValues()->checkCronValues($cronValues)) {
            return false;
        }
        return true;
    }

    public function checkReceiverData(int $receiverData): bool {
        return true;
        // if (is_int($receiverData) && $receiverData >= 100000000 && $receiverData <= 999999999) {
        //     return true;
        // }
        // return false;
    }

    // public function runEvent(array $inMessages): void {}

    // public function checkEvent(int $userID): bool {
    //     return $this->getRedis()->has('tg_cron:'. $userID. ':event');
    // }

}
