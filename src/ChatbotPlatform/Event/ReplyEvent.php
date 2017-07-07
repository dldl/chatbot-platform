<?php

namespace ChatbotPlatform\Event;

use ChatbotPlatform\Message\AbstractMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class ReplyEvent extends Event
{
    private $reply;
    private $response;

    public function __construct(AbstractMessage $response)
    {
        $this->reply = $response;
    }

    public function setReply(AbstractMessage $reply): void
    {
        $this->reply = $reply;
    }

    public function getReply(): AbstractMessage
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
