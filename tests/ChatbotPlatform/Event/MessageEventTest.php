<?php

namespace Tests\dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Message;
use PHPUnit\Framework\TestCase;

class MessageEventTest extends TestCase
{
    /**
     * @expectedException \dLdL\ChatbotPlatform\Exception\InvalidMessageException
     * @expectedExceptionMessage Reply must have original message discussion ID
     */
    public function testInvalidReply()
    {
        $replyEvent = new MessageEvent(new Message(ChatbotMessengers::AJAX, '12345', 'Michel'));

        $replyEvent->setReply(new Message(ChatbotMessengers::AJAX, '54321', 'Albert'));
    }
}
