<?php

namespace App\Telegram;

interface TelegramApi {

    /**
     * @param int $offset
     * @return TelegramMessageDto[]
     */

    // public function __construct(string $token);

    public function getMessages (int $offset): array;

    public function sendMessage(int $chatId, string $text);

}