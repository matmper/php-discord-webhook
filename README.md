# PHP Discord Webhook
Sample and cleand package to send messages to your discord channel. Easily!

# Dependences
- PHP >= 7.1
- Webhook Link ([Discord Doc](https://support.discord.com/hc/en-us/articles/228383668-Usando-Webhooks))

# Install

Install this repository in your vendor path:

```
composer require matmper/php-discord-webhook
````

# Usage

First step is import the package in your project. Don't forget import you vendor path!
```php
use Matmper\DiscordWebhook;
```

Now you need call the function to send a message
```php
// Load DiscordWebhook Class
$sendWebhook = new DiscordWebhook(
	'https://discord.com/api/webhooks/XYZ/XYZXYZ',
	'bot-name',
	'env-name',
	'project-name'
);
// Send a message
$sendWebhook->send('your message', 'success');
```

**Tip:** If you are using a framework (Laravel, Lumen, CodeIgniter, others) you can create a helper function and put your webhook link and others attributes in your env/config file.

---

## Contribution & Development

This is an open source code, free for distribution and contribution. All contributions will be accepted only with Pull Request and that pass the test and code standardization.

Run composer install in yout development env:
```
composer install --dev --prefer-dist
```

Open ```./tests/DiscordWebhookTest.php``` and change **$webhook** var to your webhook url.

Now you can use ```composer check``` in your terminal.