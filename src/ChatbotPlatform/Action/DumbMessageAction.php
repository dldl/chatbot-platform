<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Interaction;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\MessageActionInterface;

class DumbMessageAction implements MessageActionInterface
{
    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if ($event->hasReply() || $message->isVoid()) {
            return;
        }

        $interaction = $this->generateInteraction($message->getInteraction());
        $reply = new Message($message->getMessenger(), $message->getDiscussionId());
        $reply->setInteraction($interaction);

        $event->setReply($message);
    }

    private function generateInteraction(Interaction $message): Interaction
    {
        $interaction = new Interaction(
          $message->getRecipient(),
          $message->getSender(),
          '[dumb] You wrote "'.$message->getSpeech().'"'
        );

        return $interaction;
    }
}
