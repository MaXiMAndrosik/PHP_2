<?php

namespace App\Commands;

use App\Application;
use App\Telegram\TelegramApiImpl;

class TgMessagesCommand extends Command {

    const CACHE_PATH = __DIR__ . '/../../cache.txt';

    private TelegramApiImpl $tgApi;
    private array $messageHistory = [];
    private int $messageOffsets;

    public function __construct(Application $app) {
        $this->app = $app;
        $this->tgApi = new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
        $this->messageHistory = [];
        $this->messageOffsets = 1;
    }

    public function run(array $options = []): void {

        $this->initPcntl();
        $this->daemonRun($options);
    }

    private function daemonRun(array $options) {

        if (file_exists(self::CACHE_PATH)) {
            $lastData = (int)file_get_contents('cache.txt');
        } else {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
        }

        while (true) {
            if ($lastData === time()) {
                sleep(5);
                continue;
            }

            $messages = $this->tgApi->getMessage($this->messageOffsets);

            if (empty($messages["result"])) {
                continue;
            }

            $this->messageOffsets = $messages["offset"];

            $userID = key($messages["result"]);
            $userText = $messages["result"][$userID][0];
            $saveMessage = 'Message from ' . $userID . ': ' . $userText;

            $fileHandler = fopen('incomingMessages.txt', 'a');
            fwrite($fileHandler, $saveMessage.PHP_EOL);
            fclose($fileHandler);

            $this->messageHistory[] = $saveMessage;
            $lastData = time();

            // sleep(5);
            // exit();
        }
    }

    protected function initPcntl(): void {

        pcntl_signal(SIGTERM, function($signal) {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
            exit;
        });
        pcntl_signal(SIGINT, function($signal) {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
            exit;
        });
        pcntl_signal(SIGHUP, function($signal) {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
            exit;
        });

    }

}