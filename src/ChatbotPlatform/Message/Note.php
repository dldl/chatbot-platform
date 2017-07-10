<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * An note is generally a sentence, addressed to a recipient.
 */
class Note
{
    private $recipient;
    private $speech;

    public function __construct(string $recipient, string $speech)
    {
        $this->recipient = $recipient;
        $this->speech = $speech;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getSpeech(): string
    {
        return $this->speech;
    }
}
