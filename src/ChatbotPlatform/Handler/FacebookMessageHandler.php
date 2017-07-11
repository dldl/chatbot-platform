<?php

namespace dLdL\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\Tag;
use dLdL\ChatbotPlatform\MessageHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FacebookMessageHandler implements MessageHandlerInterface
{
    private $token;
    private $endpoint;

    public function __construct()
    {
        $this->token = getenv('FACEBOOK_MESSENGER_TOKEN');
        $this->endpoint = 'https://graph.facebook.com/v2.6/me/messages';
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

        if ($reply->isEmpty() || $reply->getMessenger() !== ChatbotMessengers::FACEBOOK || $reply->getTags()->count() > 1) {
            return;
        }

        $event->setResponse(new JsonResponse($this->sendMessage($reply)));
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
            $message->getTags()->add(Tag::TAG_ECHO);

            return $message;
        }

        if (isset($rawMessage['read'])) {
            $message->getTags()->add(Tag::TAG_READ);

            return $message;
        }

        if (!isset($rawMessage['message'])) {
            return $message;
        }

        $message->setContent($rawMessage['message']['text']);

        return $message;
    }

    private function sendMessage(Message $message): array
    {
        $data['recipient']['id'] = $message->getRecipient();
        $data['message']['text'] = $message->getContent();

        if ($message->getTags()->hasAny()) {
            $data['tag'] = $message->getTags()->all()[0];
        }

        $data = [];
        $data['access_token'] = $this->token;

        $headers = [
          'Content-Type: application/json',
        ];

        $process = curl_init($this->endpoint);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return, true);
    }
}
