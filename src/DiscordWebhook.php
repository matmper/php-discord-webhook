<?php

namespace Matmper;

use Exception;

class DiscordWebhook
{
    private $webhook;
    private $webhookName;
    private $webhookEnv;
    private $webhookProject;

    /**
     * constructor
     * @param string $appSecret
     * @param string $context
     * @param int $expire
     * @param int $renew
     * @return void
     */
    public function __construct(
        string $webhook,
        string $webhookName = 'Bot Webhook',
        string $webhookEnv = 'Undefined Env',
        string $webhookProject = 'Undefined Project'
    ) {
    	$this->webhook = $webhook;
	    $this->webhookName = $webhookName;
	    $this->webhookEnv = $webhookEnv;
	    $this->webhookProject = $webhookProject;
    }

    /**
     * Send a webhook message to Discord Channel
     * @param string $message your custom message
     * @param string $type message type [success, danger, warning]
     * @return bool
     */
	public function send(
		string $message,
		?string $type = null
	): bool {
	    switch ($type) {
	        case 'success':
	            $color = '2ecc71';
	        	break;
	        case 'danger':
	            $color = 'e74c3c';
	        	break;
	        case 'warning':
	            $color = 'f1c40f';
	        	break;
	        default:
	            $color = '3498db';
	        	break;
	    }

	    $message = (string) is_iterable($message) ? json_encode($message) : $message;

	    $webhookName = (string) $this->webhookName;
	    $webhookEnv = (string) $this->webhookEnv;
	    $webhookProject = (string) $this->webhookProject;

	    $webhookUrl = '';
	    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
	    	$webhookUrl .= $_SERVER['HTTP_HOST'];
	    }
	    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
	    	$webhookUrl .= $_SERVER['REQUEST_URI'];
	    }
	    if ($webhookUrl) {
	    	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
	    	$webhookUrl = $protocol . $webhookUrl;
	    }

	    $webhookData = json_encode([
	        "username" => $webhookName,
	        "tts" => false,
	        "embeds" => [
	            [
	                "description" => substr($message, 0, 2000),
	                "color" => hexdec($color),
	                "footer" => [
	                    "text" => $webhookProject,
	                ],
	                "author" => [
	                    "name" => "Discord Webhook",
	                ],
	                "fields" => [
	                    [
	                        "name" => "Env",
	                        "value" => $this->webhookEnv,
	                        "inline" => true
	                    ],
	                    [
	                        "name" => "Date",
	                        "value" => date('d/m/Y - H:i:s'),
	                        "inline" => true
	                    ],
	                    [
	                        "name" => "Url",
	                        "value" => $webhookUrl,
	                        "inline" => false
	                    ],
	                ]

	            ]
	        ]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

	    return $this->sendCurl($webhookData);
	}

    /**
     * Private function to send data webhook
     * @param string $message your custom message
     * @return bool
     */
	private function sendCurl(
		string $webhookData
	): bool {
		$webhookUrl = $this->webhook;

		if (filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
		    $ch = curl_init();
		    curl_setopt_array($ch, [
		        CURLOPT_URL => $webhookUrl,
		        CURLOPT_POST => true,
		        CURLOPT_SSL_VERIFYHOST => false,
		        CURLOPT_SSL_VERIFYPEER => false,
		        CURLOPT_POSTFIELDS => $webhookData,
		        CURLOPT_HTTPHEADER => [
		            "Content-Type: application/json"
		        ]
		    ]);
		    $response = curl_exec($ch);
		    curl_close($ch);
		    if ($response) {
		    	return true;
		    } else {
		    	throw new Exception('Error to send webhook message', 500);
		    }
		} else {
			throw new Exception('Your webhook url is invalid', 400);
		}

		return false;
	}
}
