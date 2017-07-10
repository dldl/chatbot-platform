<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * An interaction is a speech, generally a sentence, sent from a sender to
 * a recipient.
 */
class Interaction
{
    private $sender;
    private $recipient;
    private $speech;

    public function __construct(string $sender, string $recipient, string $speech)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->speech = $speech;
    }

    public function getSender(): string
    {
        return $this->sender;
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
