<?php

use Mockery as m;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\MemoryStorage;
use Xinax\LaravelGettext\Testing\Adapter\TestAdapter;
use Xinax\LaravelGettext\Testing\BaseTestCase;
use Xinax\LaravelGettext\Translators\Symfony;

class LaravelGettextTest extends BaseTestCase
{
    /**
     * Base app path
     */
    protected string $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';

    protected Symfony $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $testConfig = include __DIR__ . '/../config/config.php';

        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $fileSystem = new FileSystem($config->get(), app_path(), storage_path());

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $fileSystem,
            new MemoryStorage($config->get())
        );

        $this->translator = $translator;
    }

    public function testAdapter(): void
    {
        $testConfig = include __DIR__ . '/../config/config.php';
        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
        $this->assertInstanceOf(TestAdapter::class, $adapter);
    }

    /**
     * Test setting locale.
     */
    public function testSetLocale(): void
    {
        $response = $this->translator->setLocale('en_US');

        $this->assertEquals('en_US', $response);
    }

    /**
     * Test getting locale.
     * It should receive locale from mocked config.
     */
    public function testGetLocale(): void
    {
        $response = $this->translator->getLocale();

        $this->assertEquals('en_US', $response);
    }

    public function testIsLocaleSupported(): void
    {
        $this->assertTrue($this->translator->isLocaleSupported('en_US'));
    }

    /**
     * Test dumping locale to string
     */
    public function testToString(): void
    {
        $response = $this->translator->__toString();

        $this->assertEquals('en_US', $response);
    }

    public function testGetEncoding(): void
    {
        $response = $this->translator->getEncoding();
        $this->assertNotEmpty($response);
        $this->assertEquals('UTF-8', $response);
    }

    public function testSetEncoding(): void
    {
        $response = $this->translator->setEncoding('UTF-8');
        $this->assertNotEmpty($response);
        $this->assertInstanceOf(Symfony::class, $response);
    }

    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }
}
