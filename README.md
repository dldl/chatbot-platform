# ChatbotPlatform

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
use dLdL\ChatbotPlatform\Action\DumbMessageAction;
use dLdL\ChatbotPlatform\Action\APIAIAction;

require __DIR__.'/vendor/autoload.php';

(new Dotenv())->load(__DIR__.'/.env'); // Requires a .env file at project root to load configuration (see an example on .env.dist file)

$request = Request::createFromGlobals();
$chatbotPlatform = new ChatbotPlatform([
    new FacebookMessageHandler(), // Enables discussion through Facebook messenger (configuration required in .env file)
    new AjaxMessageHandler(), // Enables discussion through basic HTTP requests
], [
    new APIAIAction(), // Enables API.AI support (configuration required in .env file)
    new DumbMessageAction(), // Enables a dumb bot repeating anything you said
]);

$response = $chatbotPlatform->handleRequest($request);
$response->send();
```

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

Response will be returned immediately. Asynchronous responses are not (yet) supported.

## Provided features

ChatbotPlatform can be easily extended. It provides by default two message handlers and two action providers.

### AjaxMessageHandler

This handler can be used to communicate with chatbots using basic http requests. See an example in the previous section.

### FacebookMessageHandler

This handler can be used to communicate through `FacebookMessenger`. It currently supports only basic messages.

### DumbMessageAction

This action is a proof of concept system repeating what user just wrote.

### APIAIAction

This action is forwarding the message to API.AI for message processing and response generation.

## Extensibility

You may add your own message handlers by implementing the `MessageHandlerInterface` and providing them to the `ChatbotPlatform`
instance.

You should also add your own actions by implementing the `MessageActionInterface` to perform custom actions when a message
is received (e.g. generating a response or delegating the task to any external API).
