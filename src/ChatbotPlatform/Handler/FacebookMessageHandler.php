<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Flag;
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

        $message = $this->parseMessage(json_decode($request->getContent(), true));
        if ($message !== null) {
            $event->setMessage($message);
        }
    }

    public function onReply(ReplyEvent $event): void
    {
        $reply = $event->getReply();

        if ($reply->isEmpty() || $reply->getMessenger() !== ChatbotMessengers::FACEBOOK) {
            return;
        }

        $facebookMessage = new FacebookMessage($reply->getRecipient(), $reply->getContent());

        $event->setResponse(new JsonResponse($this->bot->send($facebookMessage)));
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

    private function parseMessage(array $rawMessage): ?Message
    {
        if (!isset($rawMessage['entry']) || !isset($rawMessage['entry'][0]['messaging'])) {
            return null;
        }

        if (empty($rawMessage['entry'][0]['messaging'])) {
            return null;
        }

        $message = new Message(
          ChatbotMessengers::FACEBOOK,
          $rawMessage['sender']['id'],
          $rawMessage['sender']['id'],
          $rawMessage['recipient']['id']
        );

        if (isset($rawMessage['message']['is_echo'])) {
            $message->getFlags()->add(Flag::FLAG_ECHO);

            return $message;
        }

        if (isset($rawMessage['read'])) {
            $message->getFlags()->add(Flag::FLAG_READ);

            return $message;
        }

        if (!isset($rawMessage['message'])) {
            return $message;
        }

        $message->setContent($rawMessage['message']['text']);

        return $message;
    }
}
