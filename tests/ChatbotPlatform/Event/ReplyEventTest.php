<?php

namespace Tests\dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Message\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ReplyEventTest extends TestCase
{
    public function testSimpleReplyEvent()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');
        $replyEvent = new ReplyEvent($message);

        $this->assertFalse($replyEvent->hasResponse());

        $this->assertFalse($replyEvent->hasResponse());
        $this->assertSame($message, $replyEvent->getReply());
        $this->assertTrue($replyEvent->getReply()->getDiscussion() === '12345');
    }

    public function testResponseEvent()
    {
        $replyEvent = new ReplyEvent(new Message(ChatbotMessengers::AJAX, '12345'));

        $this->assertFalse($replyEvent->hasResponse());

        $response = new Response();
        $replyEvent->setResponse($response);

        $this->assertTrue($replyEvent->hasResponse());
        $this->assertSame($response, $replyEvent->getResponse());
    }
}
