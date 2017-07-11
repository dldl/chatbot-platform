<?php

namespace dLdL\ChatbotPlatform\Message;

/**
 * A flag bag contains flags that can be attached to a message.
 */
class FlagBag
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

    private $flags;

    public function __construct()
    {
        $this->flags = [];
    }

    public function has(string ...$flags): bool
    {
        foreach ($flags as $flag) {
            if (!in_array($flag, $this->flags)) {
                return false;
            }
        }

        return true;
    }

    public function hasAny(): bool
    {
        return count($this->flags) > 0;
    }

    public function add(string $flag): void
    {
        $this->flags[$flag] = $flag;
    }

    public function remove(string $flag): void
    {
        unset($this->flags[$flag]);
    }
}
