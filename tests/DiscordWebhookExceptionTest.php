<?php declare(strict_types=1);

namespace Tests;

use Matmper\DiscordWebhook;

class DiscordWebhookExceptionTest extends TestCase
{
    /**
     * @test
     * @covers \Matmper\Exceptions\MessageTypeNotFoundException
     * @covers Matmper\DiscordWebhook::__construct
     * @covers Matmper\DiscordWebhook::type
     */
    public function test_discord_webhook_message_type_not_found_exception(): void
    {
        $this->expectException(\Matmper\Exceptions\MessageTypeNotFoundException::class);

        $sendWebhook = new DiscordWebhook();
        $sendWebhook->type($this->faker->word());
    }

    /**
     * @test
     * @covers \Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException
     * @covers Matmper\DiscordWebhook::__construct
     */
    public function test_environment_variable_cannot_be_empty_exception(): void
    {
        $env = getenv('DISCORD_WEBHOOK_ID');
        putenv('DISCORD_WEBHOOK_ID=');

        $this->expectException(\Matmper\Exceptions\EnvironmentVariableCannotBeEmptyException::class);

        try {
            new DiscordWebhook();
            putenv("DISCORD_WEBHOOK_ID={$env}");
        } catch (\Throwable $th) {
            putenv("DISCORD_WEBHOOK_ID={$env}");
            throw $th;
        }
    }

    /**
     * @test
     * @covers \Matmper\Exceptions\DiscordWebhookException
     * @covers Matmper\DiscordWebhook::__construct
     * @covers Matmper\DiscordWebhook::message
     * @covers Matmper\DiscordWebhook::send
     */
    public function test_discord_webhook_exception(): void
    {
        $env = getenv('DISCORD_WEBHOOK_HOST');
        putenv('DISCORD_WEBHOOK_HOST=WRONG');

        $this->expectException(\Matmper\Exceptions\DiscordWebhookException::class);

        try {
            $sendWebhook = new DiscordWebhook();
            $sendWebhook->message($this->faker->text())->send();
            putenv("DISCORD_WEBHOOK_HOST={$env}");
        } catch (\Throwable $th) {
            putenv("DISCORD_WEBHOOK_HOST={$env}");
            throw $th;
        }
    }
}
