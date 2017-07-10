<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Note;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\MessageActionInterface;

/**
 * RepeatMessageAction can be used for testing or proof of concepts to ensure
 * messages are received properly.
 */
class RepeatMessageAction implements MessageActionInterface
{
    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if ($event->hasReply() || $message->isVoid()) {
            return;
        }

        $note = $this->getNote($message);
        $reply = new Message(
          $message->getMessenger(),
          $message->getDiscussionId(),
          $message->getNote()->getRecipient()
        );
        $reply->setNote($note);

        $event->setReply($message);
    }

    private function getNote(Message $message): Note
    {
        $note = new Note(
          $message->getSender(),
          'You\'ve just written "'.$message->getNote()->getSpeech().'".'
        );

        return $note;
    }
}
