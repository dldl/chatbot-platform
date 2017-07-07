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

        if ($event->hasResponse()) {
            return;
        }

        $response = $this->handleMessage($message);
        $event->setResponse($response);
    }

    private function handleMessage(SimpleMessage $message): SimpleMessage
    {
        $response = new SimpleMessage(
          $message->getRecipient(),
          $message->getSender(),
          $message->getMessenger(),
          '[dumb] You said "'.$message->getMessage().'"'
        );

        return $response;
    }
}
