<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers HandleEventsCommand
 */

class HandleEventsCommandTest extends TestCase {
    
    /**
    * @dataProvider someDataProvider
    */    

    public function testShouldEventBeRunReceiveReturnBool (array $event, bool  $shouldEventBeRan): void {

        // var_dump($event, $shouldEventBeRan);
        // echo date('d M Y H:i');
        // die;

        $handleEventsCommand = new App\Commands\HandleEventsCommand(new App\Application(dirname(__DIR__)));

        $result = $handleEventsCommand->shouldEventBeRun($event);


        self::assertEquals($result, $shouldEventBeRan);
    }

    public static function someDataProvider(): array {

        date_default_timezone_set('Europe/Minsk');

        return [
            [
                [
                    'minute' => (int)date("i"),
                    'hour' => (int)date("H"),
                    'day' => (int)date("d"),
                    'month' => (int)date("m"),
                    'weekday' => (int)date("w")
                ],
                true
            ],
            [
                [
                    'minute' => null,
                    'hour' => (int)date("H"),
                    'day' => (int)date("d"),
                    'month' => (int)date("m"),
                    'weekday' => (int)date("w")
                ],
                true
            ],
            [
                [
                    'minute' => (int)date("i"),
                    'hour' => null,
                    'day' => (int)date("d"),
                    'month' => (int)date("m"),
                    'weekday' => (int)date("w")
                ],
                true
            ],
            [
                [
                    'minute' => (int)date("i"),
                    'hour' => (int)date("H"),
                    'day' => null,
                    'month' => (int)date("m"),
                    'weekday' => (int)date("w")
                ],
                true
            ],
            [
                [
                    'minute' => (int)date("i"),
                    'hour' => (int)date("H"),
                    'day' => (int)date("d"),
                    'month' => null,
                    'weekday' => (int)date("w")
                ],
                true
            ],
            [
                [
                    'minute' => (int)date("i"),
                    'hour' => (int)date("H"),
                    'day' => (int)date("d"),
                    'month' => (int)date("m"),
                    'weekday' => null
                ],
                true
            ],
            [
                [
                    'minute' => null,
                    'hour' => null,
                    'day' => null,
                    'month' => null,
                    'weekday' => null
                ],
                true
            ]
        ];
    }

}

