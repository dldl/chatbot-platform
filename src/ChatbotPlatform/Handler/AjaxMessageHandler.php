<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Tag;
use dLdL\ChatbotPlatform\MessageHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxMessageHandler implements MessageHandlerInterface
{

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getContentType() !== 'json') {
            return;
        }

        $message = $this->parseMessage(json_decode($request->getContent(), true));
        if (null !== $message) {
            $event->setMessage($message);
        }
    }

    private function parseMessage(array $rawMessage): ?Message
    {
        if (!isset($rawMessage['message'])
          || !isset($rawMessage['recipient'])
          || !isset($rawMessage['sender'])
          || !isset($rawMessage['discussion_id'])
        ) {
            return null;
        }

        $message = new Message(
          ChatbotMessengers::AJAX,
          $rawMessage['discussion_id'],
          $rawMessage['sender'],
          $rawMessage['recipient']
        );

        if (isset($rawMessage['tags']) && is_array($rawMessage['tags']) && !empty($rawMessage['tags'])) {
            foreach ($rawMessage['tags'] as $tag) {
                $message->getTags()->add($tag);
            }
        }

        $message->setContent($rawMessage['message']);

        return $message;
    }

    public function onReply(ReplyEvent $event): void
    {
        $reply = $event->getReply();

        if ($reply->getMessenger() !== ChatbotMessengers::AJAX || $reply->isEmpty()) {
            return;
        }

        $event->setResponse(new JsonResponse([
          'message' => $reply->getContent(),
          'sender' => $reply->getSender(),
          'recipient' => $reply->getRecipient(),
          'discussion_id' => $reply->getDiscussionId(),
          'tags' => $reply->getTags()->all()
        ]));
    }
}
