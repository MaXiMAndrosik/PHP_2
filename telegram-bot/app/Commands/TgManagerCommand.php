<?php

namespace App\Commands;

use App\Application;
use App\Actions\MessageHistorySaver;
use App\Actions\TgEvent;
use App\Commands\SaveEventCommand;
use App\Cache\Redis;
use App\Telegram\TelegramApiImpl;
use Predis\Client;

class TgManagerCommand extends Command {
    protected Application $app;
    private int $offset;
    private array|null $oldMessages;

    public function __construct(Application $app) {
        $this->app = $app;
        $this->offset = 0;
        $this->oldMessages = [];
    }

    function run(array $options = []): void {

        $newMessages = [];
        $newMessages = $this->getNewMessages();

        if (!empty($newMessages['result'])) {
            $this->receiveNewMessages($newMessages);

            if ($this->searchCronCommand($newMessages)) {

                $this->getRedis()->set('tg_cron_set:id', key($newMessages["result"]), 300);
                $this->getRedis()->set('tg_cron:send_message:SEND_MES', 1, 300);
                $key = 'tg_cron:' . key($newMessages["result"]) . ':event';
                $this->getRedis()->delete($key);

            }

            if ($this->getRedis()->has('tg_cron_set:id')) {

                $this->getTgEvent()->handleEvent($newMessages);

            }

            if ((int)$this->getRedis()->get('tg_cron:send_message:SEND_MES') == 6) {
                $userID = key($newMessages["result"]);
                $this->getRedis()->delete('tg_cron:send_message:SEND_MES');
                $this->getRedis()->delete('tg_cron_set:id');
                $this->getSaveEventCommand()
                    ->handleEvent(json_decode($this->getRedis()->get( 'tg_cron:' . $userID . ':event'), true));
    
            }
        }
    }

    protected function getTelegramApiImp(): TelegramApiImpl {
        return new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
    }

    protected function getTgEvent(): TgEvent {
        return new TgEvent($this->app);
    }

    protected function getRedis(): Redis {
        $client = new Client([
            'scheme' => 'tcp', 'host'   => '127.0.0.1', 'port'   => 6379,
        ]);
        return new Redis($client);
    }

    protected function getMessageHistorySaver(): MessageHistorySaver {
        return new MessageHistorySaver();
    }

    protected function getSaveEventCommand(): SaveEventCommand {
        return new SaveEventCommand($this->app);
    }

    private function getNewMessages() {

        $this->offset = $this->getRedis()->get('tg_messages:offset', 0);

        $result = $this->getTelegramApiImp()->getMessages($this->offset);

        $this->getRedis()->set('tg_messages:offset', $result['offset']?? 0);

        $this->oldMessages = json_decode($this->getRedis()->get('tg_messages:old_messages'), true);

        return $result;
    }

    private function receiveNewMessages(array $result): void {

        if (!empty($result["result"])) {
            $this->getMessageHistorySaver()->saveHistory($result);
        }

        foreach ($result['result'] ?? [] as $chatId => $newMessage) {
            if (isset($this->oldMessages[$chatId])) {
                $this->oldMessages[$chatId] = [...$this->oldMessages[$chatId], ...$newMessage];
            } else {
                $this->oldMessages[$chatId] = $newMessage;
            }
        }

        $this->getRedis()->set('tg_messages:old_messages', json_encode($this->oldMessages, true));

    }

    public function searchCronCommand(array $inMessages): bool {

        $userID = key($inMessages["result"]);
        $userTextArray = $inMessages["result"][$userID];

        foreach ($userTextArray as $value) {
            if ($value == '/cron') {
                return true;
            }
        }

        return false;

    }

}
