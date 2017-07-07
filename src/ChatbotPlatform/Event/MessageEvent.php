<?php

namespace dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\Message\AbstractMessage;
use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    private $message;
    private $reply;

    public function __construct(AbstractMessage $message)
    {
        $this->message = $message;
    }

    public function getMessage(): AbstractMessage
    {
        return $this->message;
    }

    public function hasReply(): bool
    {
        return null !== $this->reply;
    }

    public function setReply(AbstractMessage $reply): void
    {
        $this->reply = $reply;
    }

    public function getReply(): AbstractMessage
    {
        return $this->reply;
    }
}
