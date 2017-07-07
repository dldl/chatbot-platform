<?php

namespace ChatbotPlatform\Message;

abstract class AbstractMessage
{
    private $messenger;

    public function __construct(string $messenger)
    {
        $this->messenger = $messenger;
    }

    public function getMessenger(): string
    {
        return $this->messenger;
    }
}
