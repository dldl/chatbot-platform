<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use dLdL\ChatbotPlatform\MessageActionInterface;

/**
 * AsynchronousAjaxMessageAction is a naive implementation for asynchronous responses.
 * It will handle messages with an ASYNC_AJAX notification by saving them or responding
 * (if message is void) with saved messages.
 */
class AsynchronousAjaxMessageAction implements MessageActionInterface
{
    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message->hasNotification()
          || $message->getNotification()->getType() !== Notification::NOTIFICATION_ASYNC_AJAX) {
            return;
        }

        if (!$message->isVoid()) {
            $this->saveMessage($message);

            return;
        }

        $message = $this->retrieveMessages($message->getDiscussionId(), $message->getSender());

        if (null !== $message) {
            $event->setReply($message);
        }
    }

    private function retrieveMessages(string $discussionId, string $sender): ?Message
    {
        return null;
    }

    private function saveMessage(Message $message): void
    {
        return;
    }
}
