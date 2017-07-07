<?php

namespace ChatbotPlatform\Handler;

use ChatbotPlatform\ChatbotMessengers;
use ChatbotPlatform\Event\RequestEvent;
use ChatbotPlatform\Event\ReplyEvent;
use ChatbotPlatform\Exception\MessageParsingException;
use ChatbotPlatform\Message\AbstractMessage;
use ChatbotPlatform\Message\SimpleMessage;
use ChatbotPlatform\MessageHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxMessageHandler implements MessageHandlerInterface
{
    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        try {
            $message = $this->handleRequest($request);
            $event->setMessage($message);
        } catch (MessageParsingException $e) {}
    }

    public function onReply(ReplyEvent $event): void
    {
        $reply = $event->getReply();

        if ($reply->getMessenger() !== ChatbotMessengers::AJAX || !$reply instanceof SimpleMessage) {
            return;
        }

        $event->setResponse(new JsonResponse(['reply' => $reply->getMessage()]));
    }

    private function handleRequest(Request $request): AbstractMessage
    {
        if ($request->getContentType() !== 'json') {
            throw new MessageParsingException('Only json is supported');
        }

        $rawMessage = json_decode($request->getContent(), true);

        if (!isset($rawMessage['message'])
          || !isset($rawMessage['recipient'])
          || !isset($rawMessage['sender'])) {
            throw new MessageParsingException('Ajax required fields not present');
        }

        return new SimpleMessage(
          $rawMessage['sender'],
          $rawMessage['recipient'],
          ChatbotMessengers::AJAX,
          $rawMessage['message']
        );
    }
}
