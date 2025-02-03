<?php

use App\Helpers\Str;
use PHPUnit\Framework\TestCase;

/**
 * @covers Str
 */

class StrTest extends TestCase
{
    /**
     * @dataProvider studlyDataProvider
     */
    
    public function testStudlyReplaceOccurrences(string $value, string $studly)
    {
        $str = new Str();

        $result = $str->camel($value);

        self::assertEquals($result, $studly);
    }

        public function studlyDataProvider(): array
    {
        return
            [
                ['handle-events_command', 'HandleEventsCommand'],
                ['handleevents_command', 'HandleeventsCommand'],
                ['handle-eventscommand', 'HandleEventscommand'],
                ['handle-events    command', 'HandleEventsCommand'],
            ]
        ;
    }
}
