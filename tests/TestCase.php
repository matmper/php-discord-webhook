<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as TestCaseParent;

class TestCase extends TestCaseParent
{
    /**
     * @var Faker\Factory
     */
    protected $faker;

    /**
     * Set up tests
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
    }

    /**
     * Tear down tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @param Application $app
     * @return void
     */
    protected function createApplication(): void
    {
        $this->faker = \Faker\Factory::create();

        if (file_exists($file = __DIR__ . DIRECTORY_SEPARATOR .  '.env')) {
            foreach (parse_ini_file($file) as $key => $value) {
                putenv("{$key}={$value}");
            }
        }
    }
}
