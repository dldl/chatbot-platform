<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A notification is aimed to share a specific event.
 */
class Notification
{
    const NOTIFICATION_READ = 'notification.read';
    const NOTIFICATION_ECHO = 'notification.echo';

    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
