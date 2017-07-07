<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\SimpleMessage;
use dLdL\ChatbotPlatform\MessageActionInterface;

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
