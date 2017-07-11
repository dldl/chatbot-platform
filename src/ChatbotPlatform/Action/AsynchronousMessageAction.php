<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Helper\DatabaseHelper;
use dLdL\ChatbotPlatform\Message\FlagBag;
use dLdL\ChatbotPlatform\MessageActionInterface;

/**
 * AsynchronousAjaxMessageAction is a naive implementation for asynchronous responses.
 * It will handle messages with an ASYNC flags by saving them or responding
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
        if ($event->hasReply() || !$message->getFlagBag()->hasAny()) {
            return;
        }

        if ($message->getFlagBag()->has(FlagBag::FLAG_ASYNC_SAVE)) {
            $this->database->saveMessage($message);

            return;
        }

        if ($message->getFlagBag()->has(FlagBag::FLAG_ASYNC_GET)) {
            $reply = $this->database->popReply($message);
            if (null !== $reply) {
                $event->setReply($reply);
            }
        }
    }
}
