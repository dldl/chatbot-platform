<?php

namespace Tests\ChatbotPlatform;

use dLdL\ChatbotPlatform\ChatbotPlatform;
use dLdL\ChatbotPlatform\Event\MessageEvent;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Message\Message;
use dLdL\ChatbotPlatform\MessageActionInterface;
use dLdL\ChatbotPlatform\MessageHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChatbotPlatformTest extends TestCase
{
    /**
     * This test checks if the main mechanisms if the platform are working:
     *   - receiving a message containing "Hello",
     *   - appending " to the" and " world!" through two prioritized actions,
     *   - responding the generated reply.
     */
    public function testHandleRequest()
    {
        $chatbotPlatform = new ChatbotPlatform(
          $this->getMockedHandlers(), $this->getMockedActions()
        );

        $request = Request::create('http://example.com', Request::METHOD_POST, [], [], [], [], $this->getRequestContent());

        $response = $chatbotPlatform->handleRequest($request);

        $this->assertEquals($this->getExpectedResponseContent(), $response->getContent());
    }

    private function getRequestContent(): string
    {
        return json_encode([
          'message' => 'Hello',
          'sender' => 'Michel',
          'recipient' => 'Albert',
          'discussion_id' => '12345',
        ]);
    }

    private function getExpectedResponseContent(): string
    {
        return json_encode([
          'message' => 'Hello to the world!',
          'sender' => 'Albert',
          'recipient' => 'Michel',
          'discussion_id' => '12345',
        ]);
    }

    private function getMockedHandlers(): array
    {
        $wrongHandler = $this->getMockBuilder(MessageHandlerInterface::class)->getMock();
        $wrongHandler
          ->expects($this->once())
          ->method('onRequest')
          ->willReturn(null)
        ;
        $wrongHandler
          ->expects($this->once())
          ->method('onReply')
          ->willReturn(null)
        ;

        $rightHandler = $this->getMockBuilder(MessageHandlerInterface::class)->getMock();
        $rightHandler
          ->expects($this->once())
          ->method('onRequest')
          ->will($this->returnCallback(function (RequestEvent $event) {
              $rawMessage = json_decode($event->getRequest()->getContent(), true);
              $message = new Message(
                'messenger.test',
                $rawMessage['discussion_id'],
                $rawMessage['sender'],
                $rawMessage['recipient']
              );

              $message->setContent($rawMessage['message']);

              $event->setMessage($message);
          }))
        ;
        $rightHandler
          ->expects($this->once())
          ->method('onReply')
          ->will($this->returnCallback(function (ReplyEvent $event) {
              $event->setResponse(new JsonResponse([
                'message' => $event->getReply()->getContent(),
                'sender' => $event->getReply()->getSender(),
                'recipient' => $event->getReply()->getRecipient(),
                'discussion_id' => $event->getReply()->getDiscussionId(),
              ]));
          }));

        return [
            $wrongHandler, $rightHandler
        ];
    }


    private function getMockedActions(): array
    {
        $firstAction = $this->getMockBuilder(MessageActionInterface::class)->getMock();
        $firstAction
          ->expects($this->once())
          ->method('onMessage')
          ->will($this->returnCallback(function (MessageEvent $event) {
              $message = $event->getMessage();

              $reply = new Message(
                $message->getMessenger(),
                $message->getDiscussionId(),
                $message->getRecipient(),
                $message->getSender()
              );

              $reply->setContent($event->getMessage()->getContent().' to the');

              $event->setReply($reply);
          }))
        ;

        $secondAction = $this->getMockBuilder(MessageActionInterface::class)->getMock();
        $secondAction
          ->expects($this->once())
          ->method('onMessage')
          ->will($this->returnCallback(function (MessageEvent $event) {
              $message = $event->getReply();
              $message->setContent($message->getContent().' world!');

              $event->setReply($message);
          }))
        ;

        return [
            [$firstAction, 100], [$secondAction, 10]
        ];
    }
}
