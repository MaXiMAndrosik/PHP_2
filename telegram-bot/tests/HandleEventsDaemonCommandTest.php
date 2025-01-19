<?php

use PHPUnit\Framework\TestCase;

class HandleEventsDaemonCommandTest extends TestCase {

    public function testGetCurrentTime() {

        $handleEventsCommand = new App\Commands\HandleEventsDaemonCommand(new App\Application(dirname(__DIR__)));
    
    
        self::assertEquals(
            $handleEventsCommand->getCurrentTime(),
            [
                date("i"),
                date("H"),
                date("d"),
                date("m"),
                date("w")
            ]
        );
    
    }
}