# PHP Discord Webhook

Sample and cleand package to send messages to your discord channel. Easily!

<img width="604" alt="image" src="https://github.com/matmper/php-discord-webhook/assets/8351960/08ffca66-82d4-466b-b41d-f1802502786e">

# Dependences
- PHP ^7.1 | ^8.0
- Webhook Link ([Discord Doc](https://support.discord.com/hc/en-us/articles/228383668-Usando-Webhooks))

# Install & Usage

Install this repository in with composer:

```bash
$ composer require matmper/php-discord-webhook
````

Configure application envs values:
```bash
# REQUIRED (https://discord.com/api/webhooks/{ID}/{TOKEN})
DISCORD_WEBHOOK_ID=
DISCORD_WEBHOOK_TOKEN=

# OPTIONAL (DEFAULT VALUE)
APP_NAME="no application"
APP_ENV="undefined"
DISCORD_WEBHOOK_BOT_NAME="Webhook BOT"
```

Sent a new message:
```php
use Matmper\Enums\MessageType;

$sendWebhook = new \Matmper\DiscordWebhook();

// default message
$sendWebhook->message('message')->send();

// typed message
$sendWebhook->type(MessageType::SUCCESS)->message('message')->send();
$sendWebhook->type(MessageType::WARNING)->message('message')->send();
$sendWebhook->type(MessageType::DANGER)->message('message')->send();
```

To send type values, you can use the enum `Matmper\Enums\MessageType`.

| Value | Enum | Color |
|--|--|--|
| success | MessageType::SUCCESS | #2ecc71 |
| warning | MessageType::WARNING | #e74c3c |
| danger | MessageType::DANGER | #f1c40f |
| default | MessageType::DEFAULT | #3498db |

---

## Contribution & Development

This is an open source code, free for distribution and contribution.
All contributions will be accepted only with Pull Request and that pass the test and code standardization.

Run composer install in yout development and create env file
```bash
$ composer install --dev --prefer-dist
$ cp ./tests/.env.example .env
```
Touch `./tests/.env` and configure envs values. Execute ```$ composer check```!