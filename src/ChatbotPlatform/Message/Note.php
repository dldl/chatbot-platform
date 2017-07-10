<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * An note is generally a sentence, addressed to a recipient.
 */
class Note
{
    private $recipient;
    private $content;

    public function __construct(string $recipient, string $content)
    {
        $this->recipient = $recipient;
        $this->content = $content;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
