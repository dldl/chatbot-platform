<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A message is a piece of information sent by a messenger and taking part
 * of a discussion.
 */
class Message
{
    private $messenger;
    private $discussion;
    private $interaction;
    private $notification;

    public function __construct(string $messenger, string $discussion)
    {
        $this->messenger = $messenger;
        $this->discussion = $discussion;
    }

    public function getMessenger(): string
    {
        return $this->messenger;
    }

    public function getDiscussion(): string
    {
        return $this->discussion;
    }

    public function isVoid()
    {
        return $this->interaction === null;
    }

    public function getInteraction(): Interaction
    {
        return $this->interaction;
    }

    public function setInteraction(Interaction $interaction): Message
    {
        $this->interaction = $interaction;

        return $this;
    }

    public function hasNotification()
    {
        return $this->notification !== null;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification): Message
    {
        $this->notification = $notification;

        return $this;
    }
}
