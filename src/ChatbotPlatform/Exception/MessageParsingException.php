<?php

namespace dLdL\ChatbotPlatform\Exception;

class MessageParsingException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
