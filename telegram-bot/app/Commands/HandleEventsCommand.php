<?php

namespace App\Commands;

use App\Application;
use App\Database\SQLite;
use App\Actions\EventSender;
use App\Models\Event;
use App\Queue\RabbitMQ;
use App\Telegram\TelegramApiImpl;

class HandleEventsCommand extends Command {

    protected Application $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function run(array $options = []): void {

        $event = new Event(new SQLite($this->app));
        $events = $event->select();

        $queue = new RabbitMQ('eventSender');

        $eventSender = new EventSender(new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN')), $queue);

        foreach ($events as $event) {
            if ($this->shouldEventBeRun($event)) {
                $eventSender->sendMessage($event['receiver_id'], $event['text']);
            }
        }
    }

    public function shouldEventBeRun($event): bool {

        $currentMinute = date("i");
        $currentHour = date("H");
        $currentDay = date("d");
        $currentMonth = date("m");
        $currentWeekday = date("w");

        foreach ($event as $key => $value) {
            if ($value === null) {
                $createVarName = 'current' . ucfirst($key);
                $event[$key] = (int)${$createVarName};
            }
        }

        return // true;

            ($event['minute'] === (int)$currentMinute &&
                $event['hour'] === (int)$currentHour &&
                $event['day'] === (int)$currentDay &&
                $event['month'] === (int)$currentMonth &&
                $event['weekday'] === (int)$currentWeekday);
    }
}
