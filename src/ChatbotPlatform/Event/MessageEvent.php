<?php

namespace ChatbotPlatform\Event;

use ChatbotPlatform\Message\AbstractMessage;
use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    private $message;
    private $response;

    public function __construct(AbstractMessage $message)
    {
        $this->message = $message;
    }

    public function getMessage(): AbstractMessage
    {
        return $this->message;
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }

    public function setResponse(AbstractMessage $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): AbstractMessage
    {
        return $this->response;
    }
}
