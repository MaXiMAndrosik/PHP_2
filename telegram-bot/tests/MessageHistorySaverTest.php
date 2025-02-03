<?php

use App\Actions\MessageHistorySaver;
use PHPUnit\Framework\TestCase;

/**
 * @covers MessageHistorySaver
 */

class MessageHistorySaverTest extends TestCase {

    const FILENAME = 'messagesFromID';

    /**
     * @dataProvider saveHistoryDataProvider
     */
    public function testSaveHistoryToFile(array $history, string $expectedArray): void {

        date_default_timezone_set('Europe/Minsk');

        $messageHistory = new MessageHistorySaver();

        $userID = key($history["result"]);

        if (file_exists(self::FILENAME . $userID . '.txt')) {
            unlink(self::FILENAME . $userID . '.txt');
        }

        $messageHistory->saveHistory($history);


        $data = file_get_contents(self::FILENAME . $userID . '.txt');

        $this->assertEquals($expectedArray, $data);

        if (file_exists(self::FILENAME . $userID . '.txt')) {
            unlink(self::FILENAME . $userID . '.txt');
        }


    }

    public static function saveHistoryDataProvider(): array {
        return [
            [
                [
                    'result' => [123456789 => ['Hello world!', 'Hello people!']]
                ],
                    date('d M Y H:i:s') . '    ' .  'Hello world!' . PHP_EOL .
                    date('d M Y H:i:s') . '    ' .  'Hello people!' . PHP_EOL
            ],
        ];
    }



}