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

    private $note;
    private $notification;

    public function __construct(string $messenger, string $discussionId, string $sender)
    {
        $this->messenger = $messenger;
        $this->discussionId = $discussionId;
        $this->sender = $sender;
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

    public function isVoid()
    {
        return $this->note === null;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function setNote(Note $note): Message
    {
        $this->note = $note;

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
