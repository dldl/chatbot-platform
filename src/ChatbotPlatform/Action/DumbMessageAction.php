<?php

namespace ChatbotPlatform\Action;

use ChatbotPlatform\Event\MessageEvent;
use ChatbotPlatform\Message\SimpleMessage;
use ChatbotPlatform\MessageActionInterface;

class DumbMessageAction implements MessageActionInterface
{
    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof SimpleMessage) {
            return;
        }

        if ($event->hasReply()) {
            return;
        }

        $reply = $this->generateReply($message);
        $event->setReply($reply);
    }

    private function generateReply(SimpleMessage $message): SimpleMessage
    {
        $response = new SimpleMessage(
          $message->getRecipient(),
          $message->getSender(),
          $message->getMessenger(),
          '[dumb] You wrote "'.$message->getMessage().'"'
        );

        return $response;
    }
}
