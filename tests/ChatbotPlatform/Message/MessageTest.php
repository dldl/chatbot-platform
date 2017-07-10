<?php

namespace Tests\ChatbotPlatform\Message;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Message\Note;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testVoidMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel');

        $this->assertTrue($message->isVoid());
        $this->assertNull($message->getNote());
        $this->assertNull($message->getNotification());
    }

    public function testNotificationMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel');
        $message->setNotification(new Notification(Notification::NOTIFICATION_READ));

        $this->assertTrue($message->isVoid());
        $this->assertTrue($message->hasNotification());
        $this->assertEquals(Notification::NOTIFICATION_READ, $message->getNotification()->getType());
    }

    public function testInteractionMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel');
        $message->setNote(new Note('Albert', 'Hello!'));

        $this->assertFalse($message->isVoid());
        $this->assertFalse($message->hasNotification());
        $this->assertEquals('Michel', $message->getSender());
        $this->assertEquals('Albert', $message->getNote()->getRecipient());
        $this->assertEquals('Hello!', $message->getNote()->getContent());
    }
}
