<?php

declare(strict_types=1);

namespace Matmper;

use Matmper\Contracts\Webhook;
use Matmper\Enums\MessageType;
use Matmper\Services\HttpClient;

class DiscordWebhook implements Webhook
{
	/**
	 * Message type
	 *
	 * @var array<string, string>
	 */
	protected $type; // is of type array{name: string, color: string}

	/**
	 * Set request message
	 *
	 * @var mixed
	 */
	protected $message = '';

	/**
	 * Application url
	 *
	 * @var string
	 */
	protected $appUrl;

	/**
	 * Webhook request payload
	 *
	 * @var string
	 */
	private $payload;

	/**
	 * Webhook discord full url
	 *
	 * @var string
	 */
	private $webhookUrl;

    public function __construct(string $id = '', string $token = '')
    {
		$this->setClient($id, $token);

		$this->setAppUrl();

    	$this->type(MessageType::DEFAULT);
    }

	/**
	 * Set a message type color (Default: #3498db)
	 * Options: success, danger, warning
	 *
	 * @param MessageType::SUCCESS|MessageType::WARNING|MessageType::DANGER|MessageType::DEFAULT $type
	 * @return self
	 * @throws \Matmper\Exceptions\MessageTypeNotFoundException
	 */
	public function type(string $type): self
	{
		switch ($type) {
	        case MessageType::SUCCESS:
	            $this->type = ['name' => MessageType::SUCCESS, 'color' => '2ecc71'];
	        	break;
	        case MessageType::DANGER:
	            $this->type = ['name' => MessageType::DANGER, 'color' => 'e74c3c'];
	        	break;
	        case MessageType::WARNING:
	            $this->type = ['name' => MessageType::WARNING, 'color' => 'f1c40f'];
	        	break;
			case MessageType::DEFAULT:
				$this->type = ['name' => MessageType::DEFAULT, 'color' => '3498db'];
				break;
	        default:
	            throw new \Matmper\Exceptions\MessageTypeNotFoundException("{$type}", HttpClient::HTTP_NOT_FOUND);
	    }

		return $this;
	}

	/**
	 * Set message to send into webhook request
	 *
	 * @param mixed $message
	 * @return self
	 */
	public function message($message): self
	{
		$this->message = is_array($message) || is_object($message)
			? json_encode($message)
			: (string) $message;

		return $this;
	}

    /**
     * Send a new webhook message request
     *
     * @return object
	 * @throws \Matmper\Exceptions\DiscordWebhookException
     */
	public function send(): object
	{
		try {
			$this->setRequestPayload();

			$curl = new HttpClient($this->webhookUrl);

			$curl->setopt(CURLOPT_RETURNTRANSFER, true);
			$curl->setopt(CURLOPT_ENCODING, '');
			$curl->setopt(CURLOPT_MAXREDIRS, 3);
			$curl->setopt(CURLOPT_TIMEOUT, $this->env('DISCORD_WEBHOOK_TIMEOUT', 5));
			$curl->setopt(CURLOPT_FOLLOWLOCATION, true);
			$curl->setopt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			$curl->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
			$curl->setopt(CURLOPT_POSTFIELDS, $this->payload);
			$curl->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

			$response = $curl->execute();
			$response = json_decode($response);
		} catch (\Throwable $th) {
			throw new \Matmper\Exceptions\DiscordWebhookException($th->getMessage(), $th->getCode(), $th);
		}

		return $response;
	}

	/**
	 * Create a json payload webhook request
	 *
	 * @return void
	 */
	private function setRequestPayload(): void
	{
		$this->payload = (string) json_encode([
	        'username' => $this->env('DISCORD_WEBHOOK_BOT_NAME', 'Webhook BOT'),
	        'tts' => false,
	        'embeds' => [
	            [
	                'description' => substr($this->message, 0, 2000),
	                'color' => hexdec($this->type['color']),
	                'footer' => [
	                    'text' => $this->env('APP_NAME', 'no application'),
	                ],
	                'author' => [
	                    'name' => 'Discord Webhook',
	                ],
	                'fields' => [
	                    [
	                        'name' => 'env',
	                        'value' => $this->env('APP_ENV', 'discord-webhook'),
	                        'inline' => true
	                    ],
	                    [
	                        'name' => 'date',
	                        'value' => date('d.m.Y \| H:i:sO'),
	                        'inline' => true
	                    ],
	                    [
	                        'name' => 'origin',
	                        'value' => $this->appUrl,
	                        'inline' => false
	                    ],
	                ]

	            ]
	        ]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

    /**
     * Define client to curl (webhook url)
     *
     * @param string $id
     * @param string $token
	 * @return void
	 * @throws \Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException
     */
	private function setClient(string $id, string $token): void
	{
		$id = !empty($id) ? $id : $this->env('DISCORD_WEBHOOK_ID');

		if (empty($id)) {
			throw new \Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException('DISCORD_WEBHOOK_ID');
		}

		$token = !empty($token) ? $token : $this->env('DISCORD_WEBHOOK_TOKEN');

		if (empty($token)) {
			throw new \Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException('DISCORD_WEBHOOK_TOKEN');
		}

		$host = $this->env('DISCORD_WEBHOOK_HOST', 'https://discord.com');

		$this->webhookUrl = str_replace(
			['{host}', '{id}', '{token}'],
			[$host, $id, $token],
			'{host}/api/webhooks/{id}/{token}?wait=1'
		);
	}

	/**
	 * Set current application url
	 *
	 * @return void
	 */
	private function setAppUrl(): void
	{
		$appUrl = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
	    $appUrl .= !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
	    $appUrl .= !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

	    $this->appUrl = $appUrl;
	}

	/**
	 * Get environment variable value
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 * @throws \Matmper\Exceptions\EnvironmentNotFoundException
	 */
	private function env(string $name, $default = null)
	{
		try {
			$env = !empty(getenv($name)) ? getenv($name) : null;

			if ($env) {
				return $env;
			}

			$env = !empty($_ENV[$name]) ? $_ENV[$name] : null;

			if ($env) {
				return $env;
			}

			$env = function_exists('env') ? env($name) : null; // or interface_exists()

			if ($env) {
				return $env;
			}
		} catch (\Throwable $th) {
			throw new \Matmper\Exceptions\EnvironmentNotFoundException($name, HttpClient::HTTP_NOT_FOUND, $th);
		}

		return $default;
	}
}
