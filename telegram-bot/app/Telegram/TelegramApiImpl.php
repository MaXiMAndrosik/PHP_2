<?php

namespace App\Telegram;

use Exception;

class TelegramApiImpl implements TelegramApi {

    const ENDPOINT = 'https://api.telegram.org/bot';

    private int $offset;
    private string $token;

    public function __construct(string $token, int $offset = 0)
    {
        $this->token = $token;
        $this->offset = $offset;
    }

    /**
     * @param int $offset
     */

    public function getMessages(int $offset): array {
        $url = self::ENDPOINT .$this->token . '/getUpdates?timeout=1';
        $result = [];

        while (true) {

            $ch = curl_init("{$url}&offset={$offset}");
            // Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            // Return the response instead of printing it
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // curl_setopt($ch,CURLOPT_VERBOSE,1);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Не рекомендуется отключать проверку SSL-пиров
             // Рекомендуется использовать нативное хранилище корневых сертификатов вашей ОС
            curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);

            $response = json_decode(curl_exec($ch), true);

            if (!$response) {
                print curl_errno($ch) .': '. curl_error($ch) . PHP_EOL;
            }

            if (!$response['ok'] || empty($response['result'])) break;

            foreach ($response['result'] as $data) {
                $result[$data['message']['chat']['id']] = [...$result[$data['message']['chat']['id']] ?? [], $data['message']['text']];
                $offset = $data['update_id'] + 1;
            }
            curl_close($ch);

            if (count($response['result']) < 100) break;
        }

        return [
            'offset' => $offset,
            'result' => $result,
        ];
    }

    /**
     * @param int $chatId
     * @param string $text
     */
    public function sendMessage(int $chatId, string $text): void {

        $url = self::ENDPOINT . $this->token . '/sendMessage';

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        $ch = curl_init($url);

        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_POST, true); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Attach the encoded JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Set the content type to application/json
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of printing it
        curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);

        curl_exec($ch);

        curl_close($ch);
    }

}