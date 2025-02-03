<?php

namespace App\Actions;

class MessageHistorySaver {

    const FILENAME = 'messagesFromID';

    public function saveHistory(array $history) {

        date_default_timezone_set('Europe/Minsk');

        $userID = key($history["result"]);
        $userTextArray = $history["result"][$userID];

            $saveMessageHistory = '';

            foreach ($userTextArray as $text) {
                $saveMessageHistory .= date('d M Y H:i:s') . '    ' . $text . PHP_EOL;
            }

            $fileHandler = fopen(self::FILENAME . $userID . '.txt', 'a');
            fwrite($fileHandler, $saveMessageHistory);
            fclose($fileHandler);
            
    }


}