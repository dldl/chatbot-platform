<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A flag can be attached to a message. This file contains flags that are used
 * by pre-configured actions and handlers. You may use your own.
 */
final class Flag
{
    /**
     * Read flag means a given message has been read.
     */
    const FLAG_READ = 'flag.read';

    /**
     * Echo flags are copies from the last sent message to acknowledge
     * receipt.
     */
    const FLAG_ECHO = 'flag.echo';

    /**
     * Async get flags are used to ask for potential responses on a discussion.
     */
    const FLAG_ASYNC_GET = 'flag.async.get';

    /**
     * Async save flag are used to save flagged messages for later access.
     */
    const FLAG_ASYNC_SAVE = 'flag.async.save';
}
