<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Application;
use App\Actions\EventSaver;
use App\Commands\SaveEventCommand;
use App\Models\Event;
use App\Database\SQLite;

/**
 * @covers SaveEventCommand
 */

class SaveEventCommandTest extends TestCase {

    /**
     * @dataProvider handleEventDataProvider
     */
    public function testHandleEventSaveEvent(array $eventDto, array $expectedArray): void
    {
        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sqlMock = $this->getMockBuilder(SQLite::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventSaverMock = $this->getMockBuilder(EventSaver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventSaverMock->expects($this->once())
            ->method('handle')
            ->with(
                // "name, text, receiver_id, cron",
                "name, text, receiver_id, minute, hour, day, month, weekday",
                $expectedArray
            );
        
        $eventSaver =  new EventSaver(new Event(new SQLite($appMock)));

        $saveEventCommand = new SaveEventCommand($appMock);
        $saveEventCommand->handleEvent($eventDto);
    }

    public static function handleEventDataProvider(): array {
        return [
            [
                [
                    'name' => 'some-name',
                    'text' => 'some-text',
                    'receiver' => 'some-reciver',
                    'cron' => 'some-minute some-hour some-day some-month some-week'
                ],
                [
                    'some-name',
                    'some-text',
                    'some-reciver',
                    'some-cron',
                    'some-minute',
                    'some-hour',
                    'some-day',
                    'some-month',
                    'some-week'
                ],
            ],
        ];
    }


    public function testgetOptionValuesReturnOptions() {

        $saveEventsCommand = new App\Commands\SaveEventCommand(new App\Application(dirname(__DIR__)));

        $result = $saveEventsCommand->getOptionValues();
        $test = [];

        self::assertEquals($result, $test);

    }
    
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

   

}
