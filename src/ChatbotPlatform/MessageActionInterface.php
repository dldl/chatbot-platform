<?php

namespace ChatbotPlatform;

use ChatbotPlatform\Event\MessageEvent;

interface MessageActionInterface
{
    public function onMessage(MessageEvent $event): void;
}
