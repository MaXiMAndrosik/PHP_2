<?php

declare(strict_types=1);

use App\Actions\EventSender;
use App\Telegram\TelegramApiImpl;
use PHPUnit\Framework\TestCase;

/**
 * @covers EventSender
 */
class EventSenderTest extends TestCase {

    /**
     * @dataProvider eventHandleDataProvider
     */
    public function testSendMessage(int $receiver, string $message): void {


        $tgMock = $this->getMockBuilder(TelegramApiImpl::class)
            ->disableOriginalConstructor()
            // ->setMethods(['sendMessage'])
            ->getMock();

        $tgMock->expects($this->once())
            ->method('sendMessage')
            ->with(
                $receiver, $message
            );

        $eventSender = new EventSender($tgMock);

        $eventSender->sendMessage($receiver, $message);


    }

    public static function eventHandleDataProvider(): array {
        return [
            [
                123, '$mock'
            ],
            [
                456, 'Hello, World!'
            ],
            [
                789, 'Goodbye, World!'
            ]
        ];
    }

}
