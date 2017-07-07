<?php

namespace dLdL\ChatbotPlatform;

use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;

interface MessageHandlerInterface
{
    public function onRequest(RequestEvent $event): void;

    public function onReply(ReplyEvent $event): void;
}
