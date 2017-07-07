<?php

namespace ChatbotPlatform\Event;

use ChatbotPlatform\Message\AbstractMessage;
use Symfony\Component\EventDispatcher\Event;

class ResponseEvent extends Event
{
    private $response;
    private $rawResponse;

    public function __construct(AbstractMessage $response)
    {
        $this->response = $response;
    }

    public function setResponse(AbstractMessage $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): AbstractMessage
    {
        return $this->response;
    }

    public function hasRawResponse()
    {
        return $this->rawResponse !== null;
    }

    public function setRawResponse(array $rawResponse): void
    {
        $this->rawResponse = $rawResponse;
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }
}
