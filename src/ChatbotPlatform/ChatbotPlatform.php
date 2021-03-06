<?php

namespace dLdL\ChatbotPlatform;

use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatbotPlatform
{
    private $dispatcher;

    public function __construct(array $handlers, array $actions)
    {
        $this->dispatcher = new EventDispatcher();

        foreach ($handlers as $handler) {
            $this->dispatcher->addListener(ChatbotEvents::REQUEST, [$handler, 'onRequest']);
            $this->dispatcher->addListener(ChatbotEvents::REPLY, [$handler, 'onReply']);
        }

        foreach ($actions as $action) {
            $priority = 0;
            if (is_array($action)) {
                $priority = $action[1];
                $action = $action[0];
            }

            $this->dispatcher->addListener(ChatbotEvents::MESSAGE, [$action, 'onMessage'], $priority);
        }
    }

    public function handleRequest(Request $request): Response
    {
        $requestEvent = new RequestEvent($request);
        $this->dispatcher->dispatch(ChatbotEvents::REQUEST, $requestEvent);

        if ($requestEvent->hasResponse()) {
            return $requestEvent->getResponse();
        }

        if (!$requestEvent->hasMessage()) {
            return $this->buildJsonResponse(
              ['error' => 'No message handler found for current request.'],
              Response::HTTP_BAD_REQUEST
            );
        }

        $messageEvent = new MessageEvent($requestEvent->getMessage());
        $this->dispatcher->dispatch(ChatbotEvents::MESSAGE, $messageEvent);

        if (!$messageEvent->hasReply()) {
            return $this->buildJsonResponse(
              ['notice' => 'No reply generated by any action.'],
              Response::HTTP_OK
            );
        }

        $replyEvent = new ReplyEvent($messageEvent->getReply());
        $this->dispatcher->dispatch(ChatbotEvents::REPLY, $replyEvent);

        if (!$replyEvent->hasResponse()) {
            return $this->buildJsonResponse(
              ['error' => 'No handler able to handle generated response.'],
              Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $replyEvent->getResponse();
    }

    private function buildJsonResponse($content, int $status = Response::HTTP_OK)
    {
        $response = new Response(json_encode($content), $status);
        $response->headers->add([
            'content-type' => 'application/json',
        ]);

        return $response;
    }
}
