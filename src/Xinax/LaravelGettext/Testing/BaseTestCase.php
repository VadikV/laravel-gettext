<?php namespace Xinax\LaravelGettext\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Xinax\LaravelGettext\LaravelGettextServiceProvider;

class BaseTestCase extends TestCase
{
    /**
     * Base app path
     */
    protected string $appPath = '';

    /**
     * Instantiates the laravel environment.
     *
     * @return mixed
     */
    #[\Override]
    public function createApplication(): mixed
    {
        $app = $this->appPath && file_exists($this->appPath)
            ? require $this->appPath
            : $this->createPackageTestApplication();

        $app->make(Kernel::class)->bootstrap();

        $app['config']->set('laravel-gettext', require $this->getPackageRoot() . '/tests/config/config.php');

        $app->register(LaravelGettextServiceProvider::class);

        return $app;
    }

    protected function createPackageTestApplication(): Application
    {
        $root = $this->getPackageRoot();
        $bootstrapPath = $root . '/tests/bootstrap';
        $bootstrapCachePath = $bootstrapPath . '/cache';

        if (!is_dir($bootstrapCachePath)) {
            mkdir($bootstrapCachePath, 0777, true);
        }

        $app = Application::configure($root)
            ->withKernels()
            ->create();

        $app->useBootstrapPath($bootstrapPath);
        $app->useAppPath($root . '/tests');
        $app->useStoragePath($root . '/tests/storage');
        $app->useLangPath($root . '/tests/lang');

        return $app;
    }

    protected function getPackageRoot(): string
    {
        return dirname(__DIR__, 4);
    }
}
