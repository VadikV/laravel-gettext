<?php namespace Xinax\LaravelGettext\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Xinax\LaravelGettext\LaravelGettextServiceProvider;

class BaseTestCase extends TestCase
{
    /**
     * Base app path
     *
     * @var string
     */
    protected string $appPath = '';

    /**
     * Instantiates the laravel environment.
     *
     * @return mixed
     */
    public function createApplication(): mixed
    {
        // relative path in package folder
        if (!$this->appPath) {
            return null;
        }

        $app = require $this->appPath;
        $app->make(Kernel::class)->bootstrap();

        $app->register(LaravelGettextServiceProvider::class);

        return $app;
    }
}