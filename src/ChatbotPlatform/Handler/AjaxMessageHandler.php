<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Exception\MessageParsingException;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Interaction;
use dLdL\ChatbotPlatform\MessageHandlerInterface;
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
        } catch (MessageParsingException $e) {
        }
    }

    public function onReply(ReplyEvent $event): void
    {
        $reply = $event->getReply();

        if ($reply->getMessenger(
          ) !== ChatbotMessengers::AJAX || !$reply instanceof Interaction
        ) {
            return;
        }

        $event->setResponse(new JsonResponse(['reply' => $reply->getSpeech()]));
    }

    private function handleRequest(Request $request): Message
    {
        if ($request->getContentType() !== 'json') {
            throw new MessageParsingException('Only json is supported');
        }

        $rawMessage = json_decode($request->getContent(), true);

        if (!isset($rawMessage['message'])
          || !isset($rawMessage['recipient'])
          || !isset($rawMessage['sender'])
          || !isset($rawMessage['discussion_id'])
        ) {
            throw new MessageParsingException(
              'Ajax required fields not present'
            );
        }

        return (new Message(ChatbotMessengers::AJAX, $rawMessage['discussion_id']))
          ->setInteraction(
            new Interaction(
              $rawMessage['sender'],
              $rawMessage['recipient'],
              $rawMessage['message']
            )
          );
    }
}
