<?php

namespace ChatbotPlatform\Action;

use ChatbotPlatform\Event\MessageEvent;
use ChatbotPlatform\Message\AbstractMessage;
use ChatbotPlatform\Message\EmptyMessage;
use ChatbotPlatform\Message\SimpleMessage;
use ChatbotPlatform\MessageActionInterface;

class APIAIAction implements MessageActionInterface
{
    const BASE_URL = 'https://api.api.ai/api';

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof SimpleMessage) {
            return;
        }

        $response = $this->callApi($message);

        if (!$response instanceof EmptyMessage) {
            $event->setReply($response);
        };
    }

    private function callApi(SimpleMessage $message): AbstractMessage
    {
        $url = static::BASE_URL.'/query';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->buildQuery($message)));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type: application/json; charset utf-8',
          'Authorization: Bearer ' . getenv('API_AI_TOKEN')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $rawReply = json_decode(curl_exec($ch), true);

        curl_close($ch);

        if ($rawReply['status']['code'] != 200 || $rawReply['result']['action'] === 'input.unknown') {
            return new EmptyMessage($message->getMessenger());
        }

        $reply = new SimpleMessage(
          $message->getRecipient(),
          $message->getSender(),
          $message->getMessenger(),
          $rawReply['result']['speech']
        );

        return $reply;
    }

    private function buildQuery(SimpleMessage $message): array
    {
        return [
            'query' => $message->getMessage(),
            'lang' => 'fr',
            'sessionId' => $message->getSender(),
        ];
    }
}
