<?php

namespace Tests\ChatbotPlatform\Message;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Message\Interaction;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testVoidMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');

        $this->assertTrue($message->isVoid());
        $this->assertNull($message->getInteraction());
        $this->assertNull($message->getNotification());
    }

    public function testNotificationMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');
        $message->setNotification(new Notification(Notification::NOTIFICATION_READ));

        $this->assertTrue($message->isVoid());
        $this->assertTrue($message->hasNotification());
        $this->assertEquals(Notification::NOTIFICATION_READ, $message->getNotification()->getType());
    }

    public function testInteractionMessage()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');
        $message->setInteraction(new Interaction('michel', 'albert', 'Hello!'));

        $this->assertFalse($message->isVoid());
        $this->assertFalse($message->hasNotification());
        $this->assertEquals('michel', $message->getInteraction()->getSender());
        $this->assertEquals('albert', $message->getInteraction()->getRecipient());
        $this->assertEquals('Hello!', $message->getInteraction()->getSpeech());
    }
}
