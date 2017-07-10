<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A notification is aimed to share a specific event.
 */
class Notification
{
    /**
     * Read notifications means a given message has been read.
     */
    const NOTIFICATION_READ = 'notification.read';

    /**
     * Echo notifications are copies from the last sent message to acknowledge
     * receipt.
     */
    const NOTIFICATION_ECHO = 'notification.echo';

    /**
     * Async notifications are used to ask for potential responses (in naive
     * implementations).
     */
    const NOTIFICATION_ASYNC_AJAX = 'notification.async';

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
