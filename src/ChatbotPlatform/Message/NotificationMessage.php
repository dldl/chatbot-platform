<?php

namespace ChatbotPlatform\Message;

class NotificationMessage extends AbstractMessage
{
    const STATUS_READ = 'status.read';
    const STATUS_ECHO = 'status.echo';

    private $status;

    public function __construct(string $status, string $messenger)
    {
        parent::__construct($messenger);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
