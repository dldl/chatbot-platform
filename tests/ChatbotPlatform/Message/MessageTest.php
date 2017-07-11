<?php

namespace Tests\ChatbotPlatform\Message;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Tag;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testEmptyMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');

        $this->assertTrue($message->isEmpty());
        $this->assertSame('', $message->getContent());
        $this->assertFalse($message->getTags()->hasAny());
    }

    public function testTaggedMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->getTags()->add(Tag::TAG_READ);

        $this->assertTrue($message->isEmpty());
        $this->assertTrue($message->getTags()->hasAny());
        $this->assertTrue($message->getTags()->has(Tag::TAG_READ));
    }

    public function testInteractionMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->setContent('Hello!');

        $this->assertFalse($message->isEmpty());
        $this->assertFalse($message->getTags()->hasAny());
        $this->assertEquals('Michel', $message->getSender());
        $this->assertEquals('Albert', $message->getRecipient());
        $this->assertEquals('Hello!', $message->getContent());
    }
}
