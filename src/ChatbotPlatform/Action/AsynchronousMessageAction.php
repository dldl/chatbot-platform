<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Helper\DatabaseHelper;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use dLdL\ChatbotPlatform\MessageActionInterface;

/**
 * AsynchronousAjaxMessageAction is a naive implementation for asynchronous responses.
 * It will handle messages with an ASYNC notification by saving them or responding
 * (if message is void) with saved messages.
 */
class AsynchronousMessageAction implements MessageActionInterface
{
    private $database;

    public function __construct(DatabaseHelper $database)
    {
        $this->database = $database;
    }

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if ($event->hasReply()
          || !$message->hasNotification()
          || !$message->getNotification()->getType() === Notification::NOTIFICATION_ASYNC) {
            return;
        }

        if (!$message->isEmpty()) {
            $this->database->addMessage($message);

            return;
        }

        $reply = $this->database->popReply($message);
        if (null !== $reply) {
            $event->setReply($message);
        }
    }
}
