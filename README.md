# ChatbotPlatform

[![Build Status](https://travis-ci.org/dldl/chatbot-platform.svg?branch=master)](https://travis-ci.org/dldl/chatbot-platform)

**This platform is not heavy tested and is a proof-of-concept.** Any contributions are welcomed.

ChatbotPlatform is a PHP library allowing to build a multiple chatbot platform with multiple
actions providers and multiple sources.

The current implementation allows basic Ajax interactions or Facebook Messenger discussions. It can be extended easily.

## Installation

Install the library using Composer:

```sh
composer require dldl/chatbot-platform
```

## Basic usage

ChatbotPlatform may be used as a standalone project or integrated into existing applications
using any framework or CMS.

For a basic standalone usage, create an `index.php` file with the following code:

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;
use dLdL\ChatbotPlatform\ChatbotPlatform;
use dLdL\ChatbotPlatform\Handler\FacebookMessageHandler;
use dLdL\ChatbotPlatform\Handler\AjaxMessageHandler;
use dLdL\ChatbotPlatform\Action\RepeatMessageAction;
use dLdL\ChatbotPlatform\Action\APIAIAction;

require __DIR__.'/vendor/autoload.php';

(new Dotenv())->load(__DIR__.'/.env'); // Requires a .env file at project root to load configuration (see an example on .env.dist file)

$request = Request::createFromGlobals();
$chatbotPlatform = new ChatbotPlatform([
    new FacebookMessageHandler(), // Enables discussion through Facebook messenger (configuration required in .env file)
    new AjaxMessageHandler(), // Enables discussion through basic HTTP requests
], [
    [new APIAIAction(), 10], // Enables API.AI support (configuration required in .env file)
    new RepeatMessageAction(), // Enables a bot repeating anything you said (useful for testing)
]);

$response = $chatbotPlatform->handleRequest($request);
$response->send();
```

As you can see, you may pass an action instance or an array with the action instance and the priority it should hold.

You can then start a server redirecting to this file, and send requests from `Facebook` or an
`Ajax` script.

For `Facebook` usage, please refer to [Facebook developers](https://developers.facebook.com/docs/messenger-platform) documentation.
For `Ajax` support, you must send *POST* requests to your server with the following body:

```json
{
	"message": "Hello world!",
	"sender": "sender-id",
	"recipient": "recipient-id",
	"discussion_id": "12345"
}
```

Reply will be returned immediately. Asynchronous replies are not (yet) supported.

## Provided features

ChatbotPlatform can be easily extended. It provides by default some message handlers and action providers.

See `ChatbotPlatform/Handler` for available message handlers. See `ChatbotPlatform/Action` for available actions.

## Extensibility

You may add your own message handlers by implementing the `MessageHandlerInterface` and providing them to the `ChatbotPlatform`
instance.

You should also add your own actions by implementing the `MessageActionInterface` to perform custom actions when a message
is received (e.g. generating a reply or delegating the task to any external API).
