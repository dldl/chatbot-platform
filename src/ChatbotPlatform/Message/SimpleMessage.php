<?php

namespace dLdL\ChatbotPlatform\Message;

class SimpleMessage extends AbstractMessage
{
    private $sender;
    private $recipient;
    private $message;

    public function __construct(string $sender, string $recipient, string $messenger, string $message)
    {
        parent::__construct($messenger);

        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->message = $message;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
