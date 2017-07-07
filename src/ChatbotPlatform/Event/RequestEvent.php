<?php

namespace ChatbotPlatform\Event;

use ChatbotPlatform\Message\AbstractMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class RequestEvent extends Event
{
    private $request;
    private $message;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getMessage(): ?AbstractMessage
    {
        return $this->message;
    }

    public function setMessage(AbstractMessage $message): void
    {
        $this->message = $message;

        $this->stopPropagation();
    }
}
