<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Mockery as m;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Xinax\LaravelGettext\Exceptions\FileCreationException;
use Xinax\LaravelGettext\Exceptions\LocaleFileNotFoundException;
use Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Testing\BaseTestCase;

/**
 * Created by PhpStorm.
 * User: shaggyz
 * Date: 17/10/16
 * Time: 15:08
 */
class MultipleDomainTest extends BaseTestCase
{
    /**
     * Base app path
     *
     * @var string
     */
    protected string $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';

    protected FileSystem    $fileSystem;
    protected ConfigManager $configManager;
    protected string|false  $basePath;
    protected string        $storagePath;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->clearFiles();
    }

    /**
     * @throws RequiredConfigurationKeyException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // $testConfig array
        $testConfig = include __DIR__ . '/../config/config.php';
        $this->configManager = ConfigManager::create($testConfig);

        $this->basePath = realpath(__DIR__ . '/..');
        $this->storagePath = realpath(__DIR__ . '/../storage');

        $this->fileSystem = new FileSystem($this->configManager->get(),
            $this->basePath,
            $this->storagePath
        );
    }

    /**
     * Test domain configuration
     */
    public function testDomainConfiguration()
    {
        $expected = [
            'messages',
            'frontend',
            'backend',
        ];

        $result = $this->configManager->get()->getAllDomains();
        $this->assertTrue($result === $expected);
    }

    public function testFrontendDomainPaths()
    {
        $expectedPaths = [
            'controllers',
            'views/frontend'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('frontend');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testBackendDomainPaths()
    {
        $expectedPaths = [
            'views/backend'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('backend');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testDefaultDomainPaths()
    {
        $expectedPaths = [
            'views/messages',
            'views/misc'
        ];

        $actualPaths = $this->configManager->get()->getSourcesFromDomain('messages');
        $this->assertEquals($expectedPaths, $actualPaths);
    }

    public function testNoMissingDomainPaths()
    {
        // config/config.php doesn't contain a domain named `missing`, and should return no records
        $this->assertCount(0, $this->configManager->get()->getSourcesFromDomain('missing'));
    }

    /**
     * View compiler tests
     */
    public function testCompileViews()
    {
        $viewPaths = ['views'];

        $result = $this->fileSystem->compileViews($viewPaths, "frontend");
        $this->assertTrue($result);
    }


    /**
     * Test the update
     * @throws DirectoryNotFoundException
     * @throws FileNotFoundException
     * @throws FileCreationException
     * @throws LocaleFileNotFoundException
     */
    public function testFileSystem()
    {
        // Domain path test
        $domainPath = $this->fileSystem->getDomainPath();

        // Locale path test
        $locale = 'es_AR';
        $localePath = $this->fileSystem->getDomainPath($locale);

        // Create locale test
        $localesGenerated = $this->fileSystem->generateLocales();
        $this->assertTrue($this->fileSystem->checkDirectoryStructure(true));

        $this->assertCount(3, $localesGenerated);
        $this->assertTrue(is_dir($domainPath));
        $this->assertTrue(str_contains($domainPath, 'i18n'));

        foreach ($localesGenerated as $localeGenerated) {
            $this->assertTrue(file_exists($localeGenerated));
        }

        $this->assertTrue(is_dir($localePath));

        // Update locale test
        $this->assertTrue($this->fileSystem->updateLocale($localePath, $locale, "backend"));
    }

    public function testGetRelativePath()
    {
        // dir/
        $from = __DIR__;
        $to = dirname(__DIR__, 2);

        $result = $this->fileSystem->getRelativePath($to, $from);

        // Relative path from base path: unit/
        $this->assertSame("unit/", $result);
    }

    /**
     * Mocker tear-down
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Clear all files generated for testing purposes
     */
    protected function clearFiles(): void
    {
        $dir = __DIR__ . '/../lang/i18n';
        FileSystem::clearDirectory($dir);
    }
}
