<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A message is a piece of information sent on a messenger by a sender
 * and taking part of a discussion.
 */
class Message
{
    private $messenger;
    private $discussionId;
    private $sender;
    private $recipient;

    private $content;
    private $notification;

    public function __construct(string $messenger, string $discussionId, string $sender, string $recipient)
    {
        $this->messenger = $messenger;
        $this->discussionId = $discussionId;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->content = '';
    }

    public function getMessenger(): string
    {
        return $this->messenger;
    }

    public function getDiscussionId(): string
    {
        return $this->discussionId;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function isEmpty()
    {
        return $this->content === '';
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Message
    {
        $this->content = $content;

        return $this;
    }

    public function hasNotification()
    {
        return $this->notification !== null;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification): Message
    {
        $this->notification = $notification;

        return $this;
    }
}
