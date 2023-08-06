<?php declare(strict_types=1);

namespace Tests;

use Matmper\Enums\MessageType;

class EnumTest extends TestCase
{
    /**
     * @test
     * @covers Enums\MessageType
     * @dataProvider messageTypeEnumProvider
     */
    public function test_message_type_enum(string $type)
    {
        $type = strtoupper($type);
        $constant = !empty(constant("\Matmper\Enums\MessageType::{$type}"));
        $this->assertTrue($constant);
    }

    /**
     * Provider
     *
     * @return array
     */
    public static function messageTypeEnumProvider(): array
    {
        return [
            [MessageType::SUCCESS],
            [MessageType::WARNING],
            [MessageType::DANGER],
            [MessageType::DEFAULT],
        ];
    }
}
