<?php

namespace dLdL\ChatbotPlatform\Message;

class EmptyMessage extends AbstractMessage
{
    public function __construct(string $messenger)
    {
        parent::__construct($messenger);
    }
}
