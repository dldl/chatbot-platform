<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Exception\MessageParsingException;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Note;
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

        if ($reply->getMessenger() !== ChatbotMessengers::AJAX || $reply->isVoid()) {
            return;
        }

        $event->setResponse(new JsonResponse([
          'message' => $reply->getNote()->getSpeech(),
          'sender' => $reply->getSender(),
          'recipient' => $reply->getNote()->getRecipient(),
          'discussion_id' => $reply->getDiscussionId(),
        ]));
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

        $message = new Message(
          ChatbotMessengers::AJAX,
          $rawMessage['discussion_id'],
          $rawMessage['sender']
        );

        $note = new Note(
          $rawMessage['recipient'],
          $rawMessage['message']
        );
        $message->setNote($note);

        return $message;
    }
}
