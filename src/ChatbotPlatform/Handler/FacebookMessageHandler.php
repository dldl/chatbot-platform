<?php

namespace ChatbotPlatform\Handler;

use ChatbotPlatform\ChatbotMessengers;
use ChatbotPlatform\Event\RequestEvent;
use ChatbotPlatform\Event\ReplyEvent;
use ChatbotPlatform\Exception\MessageParsingException;
use ChatbotPlatform\Message\AbstractMessage;
use ChatbotPlatform\Message\EmptyMessage;
use ChatbotPlatform\Message\NotificationMessage;
use ChatbotPlatform\Message\SimpleMessage;
use ChatbotPlatform\MessageHandlerInterface;
use pimax\FbBotApp;
use pimax\Messages\Message as FacebookMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FacebookMessageHandler implements MessageHandlerInterface
{
    private $bot;

    public function __construct()
    {
        $this->bot = new FbBotApp(getenv('FACEBOOK_MESSENGER_TOKEN'));
    }

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

        if ($reply->getMessenger() !== ChatbotMessengers::FACEBOOK || !$reply instanceof SimpleMessage) {
            return;
        }

        $rawReply = $this->handleResponse($reply);
        $event->setResponse($rawReply);
    }

    private function handleRequest(Request $request): AbstractMessage
    {
        if ($request->getContentType() !== 'json') {
            throw new MessageParsingException('Only json is supported');
        }

        $rawMessage = json_decode($request->getContent(), true);

        if (!isset($rawMessage['entry']) || !isset($rawMessage['entry'][0]['messaging'])) {
            throw new MessageParsingException('It does not seems to be a Facebook message');
        }

        if (!empty($rawMessage['entry'][0]['messaging'])) {
            return $this->parseMessage($rawMessage['entry'][0]['messaging'][0]);
        }

        return new EmptyMessage(ChatbotMessengers::FACEBOOK);
    }

    private function parseMessage(array $rawMessage)
    {
        if (isset($rawMessage['read'])) {
            $message = new NotificationMessage(NotificationMessage::STATUS_READ, ChatbotMessengers::FACEBOOK);

            return $message;
        }

        if (!isset($rawMessage['message'])) {
            return new EmptyMessage(ChatbotMessengers::FACEBOOK);
        }

        if (isset($rawMessage['message']['is_echo'])) {
            return new NotificationMessage(NotificationMessage::STATUS_ECHO, ChatbotMessengers::FACEBOOK);
        }

        $message = new SimpleMessage(
          $rawMessage['sender']['id'],
          $rawMessage['recipient']['id'],
          ChatbotMessengers::FACEBOOK,
          $rawMessage['message']['text']
        );

        return $message;
    }

    private function handleResponse(SimpleMessage $message): JsonResponse
    {
        $facebookMessage = new FacebookMessage($message->getRecipient(), $message->getMessage());

        return new JsonResponse($this->bot->send($facebookMessage));
    }
}
