<?php

namespace App\Commands;

use App\Application;
use App\Commands\TgManagerCommand;

class TgMessagesDaemonCommand extends Command {

    const CACHE_PATH = __DIR__ . '/../../cache.txt';

    // private TelegramApiImpl $tgApi;

    private TgManagerCommand $tgManagerCommand;

    public function __construct(Application $app) {
        $this->app = $app;
        $this->tgManagerCommand = new TgManagerCommand($app);
    }

    public function run(array $options = []): void {
        $this->initPcntl();
        $this->daemonRun($options);
    }

    private function daemonRun(array $options) {

        if (file_exists(self::CACHE_PATH)) {
            $lastData = (int)file_get_contents(self::CACHE_PATH);
        } else {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
        }

        while (true) {
            if ($lastData === time()) {
                sleep(5);
                continue;
            }

            $this->tgManagerCommand->run($options);

            $lastData = time();

            sleep(5);
        }
    }

    private function initPcntl(): void {
        $callback = function ($signal) {
            switch ($signal) {
                case SIGTERM:
                case SIGINT:
                case SIGHUP:
                    $lastData = time();
                    file_put_contents(self::CACHE_PATH, $lastData);
                    exit;
            }
        };

        pcntl_signal(SIGTERM, $callback);
        pcntl_signal(SIGHUP, $callback);
        pcntl_signal(SIGINT, $callback);
    }

}