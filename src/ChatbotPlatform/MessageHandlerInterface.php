<?php

namespace ChatbotPlatform;

use ChatbotPlatform\Event\RequestEvent;
use ChatbotPlatform\Event\ReplyEvent;

interface MessageHandlerInterface
{
    public function onRequest(RequestEvent $event): void;

    public function onReply(ReplyEvent $event): void;
}
