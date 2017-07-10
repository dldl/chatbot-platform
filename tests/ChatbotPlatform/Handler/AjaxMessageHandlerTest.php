<?php

namespace Tests\ChatbotPlatform\Handler;

use dLdL\ChatbotPlatform\ChatbotMessengers;
use dLdL\ChatbotPlatform\Event\ReplyEvent;
use dLdL\ChatbotPlatform\Event\RequestEvent;
use dLdL\ChatbotPlatform\Handler\AjaxMessageHandler;
use dLdL\ChatbotPlatform\Message\Interaction;
use dLdL\ChatbotPlatform\Message\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AjaxMessageHandlerTest extends TestCase
{
    public function testSimpleValidRequest()
    {
        $request = $this->getRequestMock();
        $request
          ->expects($this->once())
          ->method('getContentType')
          ->willReturn('json')
        ;

        $request
          ->expects($this->once())
          ->method('getContent')
          ->willReturn($this->getValidContent())
        ;

        $event = new RequestEvent($request);
        $handler = new AjaxMessageHandler();

        $handler->onRequest($event);

        $this->assertTrue($event->hasMessage());

        $message = $event->getMessage();
        $this->assertEquals('Michel', $message->getInteraction()->getSender());
        $this->assertEquals('Albert', $message->getInteraction()->getRecipient());
        $this->assertEquals('Hello!', $message->getInteraction()->getSpeech());
    }

    public function testInvalidContentType()
    {
        $request = $this->getRequestMock();
        $request
          ->expects($this->once())
          ->method('getContentType')
          ->willReturn('xml')
        ;

        $event = new RequestEvent($request);
        $handler = new AjaxMessageHandler();

        $handler->onRequest($event);

        $this->assertNull($event->getMessage());
        $this->assertNull($event->getResponse());
    }

    public function testInvalidContent()
    {
        $request = $this->getRequestMock();
        $request
          ->expects($this->once())
          ->method('getContentType')
          ->willReturn('json')
        ;

        $request
          ->expects($this->once())
          ->method('getContent')
          ->willReturn($this->getInvalidContent())
        ;

        $event = new RequestEvent($request);
        $handler = new AjaxMessageHandler();

        $handler->onRequest($event);

        $this->assertNull($event->getMessage());
        $this->assertNull($event->getResponse());
    }

    public function testSupportedReply()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');
        $message->setInteraction(new Interaction('Michel', 'Albert', 'Hello!'));

        $event = new ReplyEvent($message);
        $handler = new AjaxMessageHandler();

        $handler->onReply($event);

        $this->assertTrue($event->hasResponse());
        $this->assertEquals($this->getValidContent(), $event->getResponse()->getContent());
    }

    public function testVoidReply()
    {
        $message = new Message(ChatbotMessengers::AJAX, '12345');

        $event = new ReplyEvent($message);
        $handler = new AjaxMessageHandler();

        $handler->onReply($event);

        $this->assertFalse($event->hasResponse());
    }

    private function getRequestMock()
    {
        $request = $this
          ->getMockBuilder(Request::class)
          ->disableOriginalConstructor()
          ->getMock()
        ;

        return $request;
    }

    private function getValidContent()
    {
        return json_encode([
          'message' => 'Hello!',
          'sender' => 'Michel',
          'recipient' => 'Albert',
          'discussion_id' => '12345'
        ]);
    }

    private function getInvalidContent()
    {
        return json_encode([
          'conversation' => 'Hello!',
          'sender' => 'Michel',
          'recipient' => 'Albert',
          'discuss' => '12345'
        ]);
    }
}
