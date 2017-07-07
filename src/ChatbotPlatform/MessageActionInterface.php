<?php

namespace dLdL\ChatbotPlatform;

use dLdL\ChatbotPlatform\Event\MessageEvent;

interface MessageActionInterface
{
    public function onMessage(MessageEvent $event): void;
}
