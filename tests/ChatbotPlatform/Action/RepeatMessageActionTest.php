<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Note;
use PHPUnit\Framework\TestCase;

class RepeatMessageActionTest extends TestCase
{
    public function testOnMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->setContent('Hello!');
        $event = new MessageEvent($message);

        $action = new RepeatMessageAction();
        $action->onMessage($event);

        $this->assertTrue($event->hasReply());
        $this->assertEquals('Albert', $event->getReply()->getSender());
        $this->assertEquals('You\'ve just written "Hello!".', $event->getReply()->getContent());
    }
}
