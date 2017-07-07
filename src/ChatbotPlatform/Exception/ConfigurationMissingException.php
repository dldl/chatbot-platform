<?php

namespace ChatbotPlatform\Exception;

class ConfigurationMissingException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
