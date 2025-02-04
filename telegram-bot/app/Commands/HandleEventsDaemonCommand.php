<?php

declare(ticks=1);

namespace App\Commands;

use App\Application;

class HandleEventsDaemonCommand extends Command {
    protected Application $app;

    const CACHE_PATH = __DIR__ . '/../../cache.txt';

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function run(array $options = []): void {
        $this->initPcntl();
        $this->daemonRun($options);
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

    private function daemonRun(array $options) {

        if (file_exists(self::CACHE_PATH)) {
            $lastData = (int)file_get_contents(self::CACHE_PATH);
        } else {
            $lastData = time();
            file_put_contents(self::CACHE_PATH, $lastData);
        }

        $handleEventsCommand = new HandleEventsCommand($this->app);
        $queueManagerCommand = new QueueManagerCommand($this->app);

        $queueManagerCommand->run($options);

        while (true) {
            if ($lastData === time()) {
                sleep(10);
                continue;
            }

            $handleEventsCommand->run($options);

            $lastData = time();

            sleep(55);
        }
    }

}
