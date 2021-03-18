<?php declare(strict_types=1);

use Matmper\DiscordWebhook;
use PHPUnit\Framework\TestCase;

class DiscordWebhookTest extends TestCase
{
    private $webhook = 'https://discord.com/api/webhooks/123/1234';
    private $webhookName = 'Test Bot';
    private $webhookEnv = 'testing';
    private $webhookProject = 'php-dicord-webhook';

    /**
     * @covers Matmper\DiscordWebpshook::__construct
     * @covers Matmper\DiscordWebhook::send
     */
    public function testSend()
    {
        $sendWebhook = new DiscordWebhook(
            $this->webhook,
            $this->webhookName,
            $this->webhookEnv,
            $this->webhookProject
        );

        $send = $sendWebhook->send('This is a test message', 'success');

        $this->assertNotNull($send);
        $this->assertTrue($send);
    }
}
