<?php

namespace dLdL\ChatbotPlatform\Event;

use dLdL\ChatbotPlatform\Message\Message;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ReplyEvent extends Event
{
    private $reply;
    private $response;

    public function __construct(Message $response)
    {
        $this->reply = $response;
    }

    public function setReply(Message $reply): void
    {
        $this->reply = $reply;
    }

    public function getReply(): Message
    {
        return $this->reply;
    }

    public function hasResponse()
    {
        return $this->response !== null;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
