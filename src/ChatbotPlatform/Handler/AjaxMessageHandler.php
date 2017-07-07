<?php

namespace ChatbotPlatform\Handler;

use ChatbotPlatform\ChatbotMessengers;
use ChatbotPlatform\Event\RequestEvent;
use ChatbotPlatform\Event\ResponseEvent;
use ChatbotPlatform\Exception\MessageParsingException;
use ChatbotPlatform\Message\AbstractMessage;
use ChatbotPlatform\Message\SimpleMessage;
use ChatbotPlatform\MessageHandlerInterface;
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

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response->getMessenger() !== ChatbotMessengers::AJAX || !$response instanceof SimpleMessage) {
            return;
        }

        $event->setRawResponse([
          'response' => $response->getMessage(),
        ]);
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
