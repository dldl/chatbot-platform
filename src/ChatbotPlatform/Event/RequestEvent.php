<?php

namespace dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\Message\Message;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestEvent extends Event
{
    private $request;
    private $message;
    private $response;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function hasMessage()
    {
        return $this->message !== null;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;

        $this->stopPropagation();
    }

    public function hasResponse()
    {
        return $this->response !== null;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;

        $this->stopPropagation();
    }
}
