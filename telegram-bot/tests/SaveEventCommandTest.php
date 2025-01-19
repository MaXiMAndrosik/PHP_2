<?php

use PHPUnit\Framework\TestCase;

class SaveEventCommandTest extends TestCase {
    
    /**
    * @dataProvider isNeedHelpDataProvider
    */

    public function testIsNeedHelpReturnBool(array $options, bool $isNeedHelp) {

        $handleEventsCommand = new App\Commands\SaveEventCommand(new App\Application(dirname(__DIR__)));

        $result = $handleEventsCommand->isNeedHelp($options);

        self::assertEquals($result, $isNeedHelp);

    }

    public static function isNeedHelpDataProvider() {
        return [
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => 'some-receiver',
                    'cron' => 'some-cron',
                    'help' => null,
                    'h' => null
                ],
                false
            ],
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => 'some-receiver',
                    'cron' => 'some-cron',
                    'help' => 'need-help',
                    'h' => null
                ],
                true
            ],
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => 'some-receiver',
                    'cron' => 'some-cron',
                    'help' => null,
                    'h' => 'need-help'
                ],
                true
            ],
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => 'some-receiver',
                    'cron' => null,
                    'help' => null,
                    'h' => null
                ],
                true
            ],
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => null,
                    'cron' => 'some-cron',
                    'help' => null,
                    'h' => null
                ],
                true
            ],
            [
                [
                    'name' => 'some-name',
                    'text' => null,
                    'receiver' => 'some-receiver',
                    'cron' => 'some-cron',
                    'help' => null,
                    'h' => null
                ],
                true
            ],
            [
                [
                    'name' => null,
                    'text' => 'some-text',
                    'receiver' => 'some-receiver',
                    'cron' => 'some-cron',
                    'help' => null,
                    'h' => null
                ],
                true
            ]
            ];
    }

    /**
    * @dataProvider getCronValuesDataProvider
    */

    public function testgetCronValuesReturnMapedArray(string $cronString, array $cronStringResult) {

        $handleEventsCommand = new App\Commands\SaveEventCommand(new App\Application(dirname(__DIR__)));

        $result = $handleEventsCommand->getCronValues($cronString);

        self::assertEquals($result, $cronStringResult);

    }

    public static function getCronValuesDataProvider() {

        return [
            ['* * * * *' => '* * * * *', [null, null, null, null, null]],
            ['1 * * * *' => '1 * * * *', [1, null, null, null, null]],
            ['1 2 * * *' => '1 2 * * *', [1, 2, null, null, null]],
            ['1 2 3 * *' => '1 2 3 * *', [1, 2, 3, null, null]],
            ['1 2 3 4 *' => '1 2 3 4 *', [1, 2, 3, 4, null]],
            ['1 2 3 4 5' => '1 2 3 4 5', [1, 2, 3, 4, 5]],
            ['1 2 3 4' => '1 2 3 4', [1, 2, 3, 4]],
            ['1 * 3' => '1 2 *', [1, 2, null]],
            ['5' => '5', [5]]
        ];
    }

}
