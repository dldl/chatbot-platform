<?php

namespace ChatbotPlatform;

use ChatbotPlatform\Event\RequestEvent;
use ChatbotPlatform\Event\ResponseEvent;

interface MessageHandlerInterface
{
    public function onRequest(RequestEvent $event): void;

    public function onResponse(ResponseEvent $event): void;
}
