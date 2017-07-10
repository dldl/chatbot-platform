<?php

namespace dLdL\ChatbotPlatform\Exception;

class InvalidMessageException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
