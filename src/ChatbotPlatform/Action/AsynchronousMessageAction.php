<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Helper\DatabaseHelper;
use dLdL\ChatbotPlatform\Message\Tag;
use dLdL\ChatbotPlatform\MessageActionInterface;

/**
 * AsynchronousAjaxMessageAction is a naive implementation for asynchronous responses.
 * It will handle messages with an ASYNC tags by saving them or responding
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
        if ($event->hasReply() || !$message->getTags()->hasAny()) {
            return;
        }

        if ($message->getTags()->has(Tag::TAG_ASYNC_SAVE)) {
            $this->database->saveMessage($message);

            return;
        }

        if ($message->getTags()->has(Tag::TAG_ASYNC_GET)) {
            $reply = $this->database->popReply($message);
            if (null !== $reply) {
                $event->setReply($reply);
            }
        }
    }
}
