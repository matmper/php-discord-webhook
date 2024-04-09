<?php declare(strict_types=1);

namespace Tests;

use Matmper\DiscordWebhook;
use Matmper\Enums\MessageType;

class DiscordWebhookTest extends TestCase
{
    /**
     * @test
     * @covers Matmper\DiscordWebhook::__construct
     * @covers Matmper\DiscordWebhook::type
     * @covers Matmper\DiscordWebhook::message
     * @covers Matmper\DiscordWebhook::send
     */
    public function test_send_type_success(): void
    {
        $sendWebhook = new DiscordWebhook();

        $message = $this->faker->text(255);
        $send = $sendWebhook->type(MessageType::SUCCESS)->message($message)->send();

        $this->assertNotEmpty($send);
        $this->assertGreaterThan(0, $send->id);
        $this->assertGreaterThan(0, $send->webhook_id);
    }
}
