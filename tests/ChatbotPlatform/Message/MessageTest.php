<?php

namespace Tests\ChatbotPlatform\Message;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Flag;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testEmptyMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');

        $this->assertTrue($message->isEmpty());
        $this->assertSame('', $message->getContent());
        $this->assertFalse($message->getFlags()->hasAny());
    }

    public function testFlaggedMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->getFlags()->add(Flag::FLAG_READ);

        $this->assertTrue($message->isEmpty());
        $this->assertTrue($message->getFlags()->hasAny());
        $this->assertTrue($message->getFlags()->has(Flag::FLAG_READ));
    }

    public function testInteractionMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->setContent('Hello!');

        $this->assertFalse($message->isEmpty());
        $this->assertFalse($message->getFlags()->hasAny());
        $this->assertEquals('Michel', $message->getSender());
        $this->assertEquals('Albert', $message->getRecipient());
        $this->assertEquals('Hello!', $message->getContent());
    }
}
