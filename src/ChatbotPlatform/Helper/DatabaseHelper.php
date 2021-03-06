<?php

namespace dLdL\ChatbotPlatform\Helper;

use dLdL\ChatbotPlatform\Message\Message;

class DatabaseHelper
{
    /**
     * @var \PDO $PDO
     */
    private $PDO;

    public function __construct()
    {
        $this->connect();
        $this->createSchema();
    }

    private function connect(): void
    {
        $this->PDO = new \PDO('sqlite:'.__DIR__.'/../../../messages.sqlite3');
    }

    private function createSchema(): void
    {
        $schema = $this->PDO->exec("SELECT name FROM sqlite_master WHERE type='table' AND name='message'");

        if ($schema !== 0) {
            return;
        }

        $this->PDO->exec("CREATE TABLE message(
            discussion_id VARCHAR(255),
            sender VARCHAR(30),
            recipient VARCHAR(30),
            content TEXT
        )");
    }

    public function getPDO()
    {
        return $this->PDO;
    }

    public function saveMessage(Message $message): void
    {
        $sth = $this->PDO->prepare('INSERT INTO message VALUES (?, ?, ?, ?)');
        $sth->execute([
          $message->getDiscussionId(),
          $message->getSender(),
          $message->getRecipient(),
          $message->getContent()
        ]);
    }

    public function popReply(Message $message): ?Message
    {
        $sth = $this->PDO->prepare('SELECT * FROM message WHERE recipient = ? AND discussion_id = ?');
        $sth->execute([$message->getSender(), $message->getDiscussionId()]);

        $messages = $sth->fetchAll();

        if (count($messages) === 0) {
            return null;
        }

        $content = '';
        for ($i = 0; $i < count($messages); ++$i) {
            $content .= $messages[$i]['content'];

            if ($i < count($messages) - 1) {
                $content .= "\n";
            }
        }

        $reply = new Message(
          $message->getMessenger(),
          $message->getDiscussionId(),
          $messages[0]['sender'],
          $messages[0]['recipient']
        );

        $reply->setContent($content);

        $sth = $this->PDO->prepare('DELETE FROM message WHERE recipient = ?');
        $sth->execute([$message->getSender()]);

        return $reply;
    }
}
