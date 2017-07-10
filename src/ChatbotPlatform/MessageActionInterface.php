<?php

namespace dLdL\ChatbotPlatform;

use dLdL\ChatbotPlatform\Event\MessageEvent;

/**
 * A message action is run when a message has been received, identified and
 * parsed by a specific handler.
 *
 * An action can do anything with the message, such as saving it, sending it to
 * a webservice, ...
 *
 * An action can also set a reply for the message. It will be dispatched to the
 * handlers to be transferred, logged, ...
 */
interface MessageActionInterface
{
    /**
     * This method is triggered when a message has been parsed by a message
     * handler. The action may generate a reply or perform any other action.
     *
     * @param MessageEvent $event
     */
    public function onMessage(MessageEvent $event): void;
}
