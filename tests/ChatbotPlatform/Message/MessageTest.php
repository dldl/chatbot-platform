<?php

namespace Tests\ChatbotPlatform\Message;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\FlagBag;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testEmptyMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');

        $this->assertTrue($message->isEmpty());
        $this->assertSame('', $message->getContent());
        $this->assertFalse($message->getFlagBag()->hasAny());
    }

    public function testFlaggedMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->getFlagBag()->add(FlagBag::FLAG_READ);

        $this->assertTrue($message->isEmpty());
        $this->assertTrue($message->getFlagBag()->hasAny());
        $this->assertTrue($message->getFlagBag()->has(FlagBag::FLAG_READ));
    }

    public function testInteractionMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->setContent('Hello!');

        $this->assertFalse($message->isEmpty());
        $this->assertFalse($message->getFlagBag()->hasAny());
        $this->assertEquals('Michel', $message->getSender());
        $this->assertEquals('Albert', $message->getRecipient());
        $this->assertEquals('Hello!', $message->getContent());
    }
}
