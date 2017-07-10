<?php

namespace Tests\dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Message\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestEventTest extends TestCase
{
    public function testSimpleRequestEvent()
    {
        $requestEvent = new RequestEvent($this->getMockRequest());

        $this->assertFalse($requestEvent->hasResponse());
        $this->assertFalse($requestEvent->hasMessage());
        $this->assertFalse($requestEvent->isPropagationStopped());

        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $requestEvent->setMessage($message);

        $this->assertFalse($requestEvent->hasResponse());
        $this->assertTrue($requestEvent->hasMessage());
        $this->assertTrue($requestEvent->isPropagationStopped());
        $this->assertSame($message, $requestEvent->getMessage());
        $this->assertTrue($requestEvent->getMessage()->getDiscussionId() === '12345');
    }

    private function getMockRequest()
    {
        $request = $this
          ->getMockBuilder(Request::class)
          ->disableOriginalConstructor()
          ->getMock()
        ;

        return $request;
    }
}
