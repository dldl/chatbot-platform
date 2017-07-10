<?php

namespace Tests\dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Action\AsynchronousMessageAction;
use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Helper\DatabaseHelper;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use PHPUnit\Framework\TestCase;

class AsynchronousMessageActionTest extends TestCase
{
    private $database;

    protected function setUp()
    {
        parent::setUp();

        $this->database = new DatabaseHelper();
        $this->database->getPDO()->exec('TRUNCATE TABLE message');
    }

    public function testAdd()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Michel', 'Albert');
        $message->setContent('Hello!');
        $message->setNotification(new Notification(Notification::NOTIFICATION_ASYNC));
        $event = new MessageEvent($message);

        $action = new AsynchronousMessageAction($this->database);
        $action->onMessage($event);

        $this->assertNull($event->getReply());

        return $action;
    }

    /**
     * @depends testAdd
     */
    public function testRemove(AsynchronousMessageAction $action)
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345', 'Albert', 'Michel');
        $message->setNotification(new Notification(Notification::NOTIFICATION_ASYNC));
        $event = new MessageEvent($message);

        $action->onMessage($event);
        $reply = $event->getReply();

        $this->assertTrue($event->hasReply());
        $this->assertEquals('Michel', $reply->getSender());
        $this->assertEquals('Albert', $reply->getRecipient());
        $this->assertEquals('12345', $reply->getDiscussionId());
    }
}
