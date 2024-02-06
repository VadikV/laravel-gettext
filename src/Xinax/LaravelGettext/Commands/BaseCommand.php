<?php

namespace Xinax\LaravelGettext\Commands;

use Illuminate\Console\Command;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use Xinax\LaravelGettext\FileSystem;

class BaseCommand extends Command
{
    /**
     * Filesystem helper
     */
    protected FileSystem $fileSystem;

    /**
     * Package configuration data
     */
    protected Config $configuration;

    /**
     * Prepares the package environment for gettext commands
     *
     * @return void
     * @throws RequiredConfigurationKeyException
     */
    protected function prepare(): void
    {
        $configManager = ConfigManager::create();

        $this->fileSystem = new FileSystem(
            $configManager->get(),
            app_path(),
            storage_path()
        );

        $this->configuration = $configManager->get();
    }
}
