<?php

namespace App\Actions;

use App\Telegram\TelegramApi;
use App\Queue\Queue;
use App\Queue\Queueable;

class EventSender implements Queueable {

    private string $receiver;
    private string $message;

    public function __construct(private TelegramApi $telegram, private Queue $queue) {
    }

    public function sendMessage(string $receiver, string $message): void {
        $this->toQueue($receiver, $message);
        date_default_timezone_set('Europe/Minsk');
        echo date('d.m.y H:i') . " Я отправил сообщение \"$message\" получателю с id $receiver" . PHP_EOL;
    }

    public function handle(): void {
        $this->telegram->sendMessage($this->receiver, $this->message);
    }

    public function toQueue(...$args): void {

        $this->receiver = $args[0];
        $this->message = $args[1];

        $this->queue->sendMessage(serialize($this));
    }


}