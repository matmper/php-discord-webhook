<?php

declare(strict_types=1);

namespace Matmper;

use Exception;
use Matmper\Enums\MessageType;
use Matmper\Exceptions\DiscordWebhookException;
use Matmper\Exceptions\EnvironmentNotFoundException;
use Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException;

class DiscordWebhook
{
	/**
	 * Message type (success | warning | danger | default)
	 *
	 * @var array
	 */
	protected $type;

	/**
	 * Set request message
	 *
	 * @var string
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
	 * @var array
	 */
	private $payload;

	/**
	 * Webhook discord full url
	 *
	 * @var string
	 */
	private $webhookUrl;

    public function __construct()
	{
		$this->setClient();
		$this->setAppUrl();
    	$this->type(MessageType::DEFAULT);
    }

	/**
	 * Set a message type color (Default: #3498db)
	 * Options: success, danger, warning
	 *
	 * @param string $type
	 * @return self
	 * @throws Exception
	 */
	public function type(string $type): self
	{
		switch (strtolower($type)) {
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
	            throw new Exception('Discord Webhook: message type not found: ' . $type);
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
		if (is_array($message) || is_object($message)) {
			$this->message = json_encode($this->message);
		} else {
			$this->message = (string) $message;
		}

		return $this;
	}

    /**
     * Send a request to defined webhook
     *
     * @return object
	 * @throws \Matmper\Exceptions\DiscordWebhookException
     */
	public function send(): object
	{
		try {
			$this->setRequestPayload();

			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => $this->webhookUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => $this->env('DISCORD_WEBHOOK_TIMEOUT', 5),
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($this->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/json',
				],
			]);

			$response = curl_exec($curl);

			curl_close($curl);

			$response = json_decode($response);
		} catch (\Throwable $th) {
			throw new DiscordWebhookException($th->getMessage(), $th->getCode(), $th);
		}
		
		return $response;
	}

	/**
	 * Create a payload webhook request
	 *
	 * @return void
	 */
	private function setRequestPayload(): void
	{
		$this->payload = [
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
	    ];
	}

	/**
	 * Define client to curl (webhook url)
	 *
	 * @return void
	 * @throws \Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException
	 */
	private function setClient(): void
	{
		$channel = $this->env('DISCORD_WEBHOOK_ID');

		if (empty($channel)) {
			throw new EnvironmentVariableCannotBeEmptyException('DISCORD_WEBHOOK_ID');
		}

		$token = $this->env('DISCORD_WEBHOOK_TOKEN');

		if (empty($token)) {
			throw new EnvironmentVariableCannotBeEmptyException('DISCORD_WEBHOOK_TOKEN');
		}

		$host = $this->env('DISCORD_WEBHOOK_HOST', 'https://discord.com');

		$this->webhookUrl = str_replace(
			['{host}', '{channel}', '{token}'],
			[$host, $channel, $token],
			'{host}/api/webhooks/{channel}/{token}?wait=1'
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

			$env = function_exists('env') ? env($name) : null;
	
			if ($env) {
				return $env;
			}
		} catch (\Throwable $th) {
			throw new EnvironmentNotFoundException($name, 404, $th);
		}

		return $default;
	}
}
