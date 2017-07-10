<?php

namespace dLdL\ChatbotPlatform\Action;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\Message\EmptyMessage;
use dLdL\ChatbotPlatform\Message\Interaction;
use dLdL\ChatbotPlatform\MessageActionInterface;

class APIAIAction implements MessageActionInterface
{
    const BASE_URL = 'https://api.api.ai/api';

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if ($event->hasReply() || $message->isVoid()) {
            return;
        }

        $response = $this->callApi($message);
        if (null !== $response) {
            $event->setReply($response);
        };
    }

    private function callApi(Message $message): ?Message
    {
        $url = static::BASE_URL.'/query';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
          $this->buildQuery($message->getInteraction()))
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type: application/json; charset utf-8',
          'Authorization: Bearer ' . getenv('API_AI_TOKEN')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $rawReply = json_decode(curl_exec($ch), true);

        curl_close($ch);

        if ($rawReply['status']['code'] != 200 || $rawReply['result']['action'] === 'input.unknown') {
            return null;
        }

        $reply = (new Message($message->getMessenger(), $message->getDiscussionId()))
            ->setInteraction(
              new Interaction(
                $message->getInteraction()->getRecipient(),
                $message->getInteraction()->getSender(),
                $rawReply['result']['speech']
              )
            )
        ;

        return $reply;
    }

    private function buildQuery(Message $message): array
    {
        return [
            'query' => $message->getInteraction()->getSpeech(),
            'lang' => 'fr',
            'sessionId' => $message->getDiscussionId(),
        ];
    }
}
