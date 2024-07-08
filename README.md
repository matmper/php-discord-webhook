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

*Applications without env files support, we recommend [using version 1.x](https://github.com/matmper/php-discord-webhook/tree/1.1.0)*

```bash
# REQUIRED (https://discord.com/api/webhooks/{ID}/{TOKEN})
DISCORD_WEBHOOK_ID=
DISCORD_WEBHOOK_TOKEN=

# OPTIONAL (DEFAULT VALUE)
APP_NAME="no application"
APP_ENV="undefined"
DISCORD_WEBHOOK_BOT_NAME="Webhook BOT"
```

Send a message:
```php
$sendWebhook = new \Matmper\DiscordWebhook();
$sendWebhook->message('message')->send();
```

Set custom Discord ID and Token and send a message:
```php
$sendWebhook = new \Matmper\DiscordWebhook('DISCORD_ID', 'DISCORD_TOKEN');
$sendWebhook->message('message')->send();
```

Send a typed message:
```php
use Matmper\Enums\MessageType;
$sendWebhook->type(MessageType::SUCCESS)->message('message')->send();
$sendWebhook->type(MessageType::WARNING)->message('message')->send();
$sendWebhook->type(MessageType::DANGER)->message('message')->send();
```

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
Edit `./tests/.env` and configure envs values. Execute ```$ composer check```.
