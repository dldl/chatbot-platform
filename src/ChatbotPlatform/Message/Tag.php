<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A tag can be attached to a message. This file contains tags that are used
 * by pre-configured actions and handlers. You may use your own.
 */
final class Tag
{
    /**
     * Read tag means a given message has been read.
     */
    const TAG_READ = 'READ';

    /**
     * Echo tags are copies from the last sent message to acknowledge
     * receipt.
     */
    const TAG_ECHO = 'ECHO';

    /**
     * Async get tags are used to ask for potential responses on a discussion.
     */
    const TAG_ASYNC_GET = 'ASYNC_GET';

    /**
     * Async save tags are used to save tagged messages for later access.
     */
    const TAG_ASYNC_SAVE = 'ASYNC_SAVE';
}
