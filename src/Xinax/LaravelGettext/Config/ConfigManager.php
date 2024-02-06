<?php

namespace Xinax\LaravelGettext\Config;

use Illuminate\Support\Facades\Config;
use Xinax\LaravelGettext\Adapters\LaravelAdapter;
use Xinax\LaravelGettext\Config\Models\Config as ConfigModel;
use Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use Xinax\LaravelGettext\Storages\SessionStorage;

class ConfigManager
{
    protected ConfigModel $config;

    /**
     * Package configuration route (published)
     */
    const DEFAULT_PACKAGE_CONFIG = 'laravel-gettext';

    /**
     * @param array|null $config
     * @throws RequiredConfigurationKeyException
     */
    public function __construct(array $config = null)
    {
        if ($config) {
            $this->config = $this->generateFromArray($config);
        }
        else {
            $this->config = new ConfigModel;
        }
    }

    /**
     * Get new instance of the ConfigManager
     *
     * @param null $config
     * @return static
     * @throws RequiredConfigurationKeyException
     */
    public static function create($config = null): static
    {
        if (is_null($config)) {
            // Default package configuration file (published)
            $config = Config::get(static::DEFAULT_PACKAGE_CONFIG);
        }

        return new static($config);
    }

    /**
     * Get the config model
     *
     * @return ConfigModel
     */
    public function get(): ConfigModel
    {
        return $this->config;
    }

    /**
     * Creates a configuration container and checks the required fields
     *
     * @param array $config
     * @return ConfigModel
     * @throws RequiredConfigurationKeyException
     */
    protected function generateFromArray(array $config): ConfigModel
    {
        $requiredKeys = [
            'locale',
            'fallback-locale',
            'encoding'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RequiredConfigurationKeyException(
                    sprintf('Unconfigured required value: %s', $key)
                );
            }
        }

        $container = new ConfigModel();

        $id = isset($config['session-identifier']) ? (string)$config['session-identifier'] : 'laravel-gettext-locale';

        $adapter = isset($config['adapter']) ? (string)$config['adapter'] : LaravelAdapter::class;

        $storage = isset($config['storage']) ? (string)$config['storage'] : SessionStorage::class;

        $container->setLocale((string)$config['locale'])
            ->setSessionIdentifier($id)
            ->setEncoding((string)$config['encoding'])
            ->setCategories((array)($config['categories'] ?? ['LC_ALL']))
            ->setFallbackLocale((string)$config['fallback-locale'])
            ->setSupportedLocales((array)$config['supported-locales'])
            ->setDomain((string)$config['domain'])
            ->setTranslationsPath((string)$config['translations-path'])
            ->setProject((string)$config['project'])
            ->setTranslator((string)$config['translator'])
            ->setSourcePaths((array)$config['source-paths'])
            ->setSyncLaravel((bool)$config['sync-laravel'])
            ->setAdapter($adapter)
            ->setStorage($storage);

        if (array_key_exists('relative-path', $config)) {
            $container->setRelativePath((string)$config['relative-path']);
        }

        if (array_key_exists("custom-locale", $config)) {
            $container->setCustomLocale((bool)$config['custom-locale']);
        }

        if (array_key_exists("keywords-list", $config)) {
            $container->setKeywordsList((array)$config['keywords-list']);
        }

        if (array_key_exists("handler", $config)) {
            $container->setHandler((string)$config['handler']);
        }

        return $container;
    }
}
