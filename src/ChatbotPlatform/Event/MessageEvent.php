<?php

namespace dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\Exception\InvalidMessageException;
use dLdL\ChatbotPlatform\Message\Message;
use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    private $message;
    private $reply;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function hasReply(): bool
    {
        return null !== $this->reply;
    }

    public function setReply(Message $reply): void
    {
        if ($reply->getDiscussion() !== $this->message->getDiscussion()) {
            throw new InvalidMessageException('Reply must have original message discussion ID');
        }

        $this->reply = $reply;
    }

    public function getReply(): ?Message
    {
        return $this->reply;
    }
}
