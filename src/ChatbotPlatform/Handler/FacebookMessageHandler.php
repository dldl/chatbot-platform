<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Exception\MessageParsingException;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Notification;
use dLdL\ChatbotPlatform\MessageHandlerInterface;
use pimax\FbBotApp;
use pimax\Messages\Message as FacebookMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        if ($this->isChallenge($request)) {
            $event->setResponse($this->getChallengeResponse($request));

            return;
        }

        if ($request->getContentType() !== 'json') {
            return;
        }

        try {
            $message = $this->handleRequest($request);
            $event->setMessage($message);
        } catch (MessageParsingException $e) {}
    }

    public function onReply(ReplyEvent $event): void
    {
        $reply = $event->getReply();

        if ($reply->isEmpty() || $reply->getMessenger() !== ChatbotMessengers::FACEBOOK) {
            return;
        }

        $rawReply = $this->sendMessage($reply);
        $event->setResponse($rawReply);
    }

    private function isChallenge(Request $request): bool
    {
        $challenge = $request->query->get('hub_challenge');
        $token = $request->query->get('hub_verify_token');
        $mode = $request->query->get('hub_mode');

        return $challenge && $token && $mode == 'subscribe';
    }

    private function getChallengeResponse(Request $request): Response
    {
        $token = $request->query->get('hub_verify_token');

        if (getenv('FACEBOOK_CHALLENGE_TOKEN') === $token) {
            $challenge = $request->query->get('hub_challenge');
        } else {
            $challenge = 'invalid token';
        }

        return new Response($challenge);
    }

    private function handleRequest(Request $request): Message
    {
        $rawMessage = json_decode($request->getContent(), true);

        if (!isset($rawMessage['entry']) || !isset($rawMessage['entry'][0]['messaging'])) {
            throw new MessageParsingException('It does not seems to be a Facebook message');
        }

        if (!empty($rawMessage['entry'][0]['messaging'])) {
            return $this->parseMessage($rawMessage['entry'][0]['messaging'][0]);
        }

        throw new MessageParsingException('Unsupported Facebook message');
    }

    private function parseMessage(array $rawMessage): Message
    {
        $message = new Message(
          ChatbotMessengers::FACEBOOK,
          'fb'.$rawMessage['sender']['id'],
          $rawMessage['sender']['id'],
          $rawMessage['recipient']['id']
        );

        if (isset($rawMessage['message']['is_echo'])) {
            $message->setNotification(new Notification(Notification::NOTIFICATION_ECHO));

            return $message;
        }

        if (isset($rawMessage['read'])) {
            $message->setNotification(new Notification(Notification::NOTIFICATION_READ));

            return $message;
        }

        if (!isset($rawMessage['message'])) {
            return $message;
        }

        $message->setContent($rawMessage['message']['text']);

        return $message;
    }

    private function sendMessage(Message $message): JsonResponse
    {
        $facebookMessage = new FacebookMessage($message->getRecipient(), $message->getContent());

        return new JsonResponse($this->bot->send($facebookMessage));
    }
}
