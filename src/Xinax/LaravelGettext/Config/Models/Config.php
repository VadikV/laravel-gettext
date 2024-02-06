<?php

namespace Xinax\LaravelGettext\Config\Models;

use InvalidArgumentException;

class Config
{
    /**
     * Session identifier
     */
    protected string $sessionIdentifier = 'laravel-gettext-locale';

    /**
     * Charset encoding for files
     */
    protected string $encoding = 'UTF-8';

    /**
     * Full ISO Locale (en_EN)
     */
    protected string $locale = 'en_US';

    /**
     * Locale categories
     */
    protected array $categories = ['LC_ALL'];

    /**
     * Fallback locale
     */
    protected string $fallbackLocale = 'en_US';

    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en_US'];

    /**
     * Gettext domain
     */
    protected string $domain = 'messages';

    /**
     * Path to translation files
     */
    protected string $translationsPath = '../resources/lang';

    /**
     * Project identifier
     */
    protected string $project = '';

    /**
     * Translator contact data
     */
    protected string $translator = '';

    /**
     * Source paths
     */
    protected array $sourcePaths = [];

    /**
     * Sync with laravel locale
     */
    protected bool $syncLaravel = true;

    /**
     * The adapter class used to sync with laravel locale
     */
    protected string $adapter = '';


    /**
     * The storage class used store the current locale information
     */
    protected string $storage = '';

    /**
     * Custom locale name
     * Used when needed locales are unavalilable
     */
    protected bool $customLocale = false;

    /**
     * Default relative path
     */
    protected string $relativePath = '../../../../../app';

    /**
     * Poedit keywords list
     */
    protected array $keywordsList = [];

    /**
     * Core translation handler
     */
    protected string $handler = 'symfony';

    /**
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    /**
     * @param string $path
     * @return void
     */
    public function setRelativePath(string $path): void
    {
        $this->relativePath = $path;
    }

    /**
     * @return string
     */
    public function getSessionIdentifier(): string
    {
        return $this->sessionIdentifier;
    }

    /**
     * @param string $sessionIdentifier
     * @return $this
     */
    public function setSessionIdentifier(string $sessionIdentifier): static
    {
        $this->sessionIdentifier = $sessionIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets categories
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Sets categories
     *
     * @param array $categories
     *
     * @return self
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string $fallbackLocale
     *
     * @return $this
     */
    public function setFallbackLocale(string $fallbackLocale): static
    {
        $this->fallbackLocale = $fallbackLocale;

        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * @param array $supportedLocales
     *
     * @return $this
     */
    public function setSupportedLocales(array $supportedLocales): static
    {
        $this->supportedLocales = $supportedLocales;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationsPath(): string
    {
        return $this->translationsPath;
    }

    /**
     * @param string $translationsPath
     *
     * @return $this
     */
    public function setTranslationsPath(string $translationsPath): static
    {
        $this->translationsPath = $translationsPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     *
     * @return $this
     */
    public function setProject(string $project): static
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslator(): string
    {
        return $this->translator;
    }

    /**
     * @param string $translator
     *
     * @return $this
     */
    public function setTranslator(string $translator): static
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return array
     */
    public function getSourcePaths(): array
    {
        return $this->sourcePaths;
    }

    /**
     * @param array $sourcePaths
     *
     * @return $this
     */
    public function setSourcePaths(array $sourcePaths): static
    {
        $this->sourcePaths = $sourcePaths;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSyncLaravel(): bool
    {
        return $this->syncLaravel;
    }

    /**
     * Gets the Sync with laravel locale.
     *
     * @return bool
     */
    public function getSyncLaravel(): bool
    {
        return $this->syncLaravel;
    }

    /**
     * @param boolean $syncLaravel
     *
     * @return $this
     */
    public function setSyncLaravel(bool $syncLaravel): static
    {
        $this->syncLaravel = $syncLaravel;

        return $this;
    }

    /**
     * Gets the adapter class.
     *
     * @return string
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     *
     * @return $this
     */
    public function setAdapter(string $adapter): static
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Getter for storage
     *
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     *
     * @return $this
     */
    public function setStorage(string $storage): static
    {
        $this->storage = $storage;

        return $this;
    }


    /**
     * Return an array with all domain names
     *
     * @return array
     */
    public function getAllDomains(): array
    {
        $domains = [$this->domain]; // add the default domain

        foreach ($this->sourcePaths as $domain => $paths) {
            if (is_array($paths)) {
                $domains[] = $domain;
            }
        }

        return array_unique($domains);
    }

    /**
     * Return all routes for a single domain
     *
     * @param $domain
     *
     * @return array
     */
    public function getSourcesFromDomain($domain): array
    {
        // grab any paths wrapped in $domain
        $explicitPaths = array_key_exists($domain, $this->sourcePaths)
            ? $this->sourcePaths[$domain]
            : [];

        // if we're not including the default domain, return what we have so far
        if ($this->domain != $domain) {
            return $explicitPaths;
        }

        // otherwise, grab all the default domain paths
        // and merge them with paths wrapped in $domain
        return array_reduce(
            $this->sourcePaths,
            function ($carry, $path) {
                if (!is_array($path)) {
                    $carry[] = $path;
                }

                return $carry;
            },
            $explicitPaths
        );
    }

    /**
     * Gets C locale setting.
     *
     * @return boolean
     */
    public function getCustomLocale(): bool
    {
        return $this->customLocale;
    }

    /**
     * Sets if will use C locale structure.
     *
     * @param bool $customLocale
     * @return self
     */
    public function setCustomLocale(bool $customLocale): static
    {
        $this->customLocale = $customLocale;

        return $this;
    }

    /**
     * Gets the Poedit keywords list.
     *
     * @return string[]
     */
    public function getKeywordsList(): array
    {
        return !empty($this->keywordsList)
            ? $this->keywordsList
            : ['_'];
    }

    /**
     * Sets the Poedit keywords list.
     *
     * @param string[] $keywordsList the keywords list
     *
     * @return self
     */
    public function setKeywordsList(array $keywordsList): static
    {
        $this->keywordsList = $keywordsList;

        return $this;
    }

    /**
     * Sets the handler type. Also check for valid handler name
     *
     * @param string $handler
     *
     * @return $this
     */
    public function setHandler(string $handler): static
    {
        if (!in_array($handler, [
            'symfony',
            'gettext',
        ])
        ) {
            throw new InvalidArgumentException("Handler '$handler' is not supported'");
        }

        $this->handler = $handler;

        return $this;
    }

    /**
     * Returns the handler name
     *
     * @return string
     */
    public function getHandler(): string
    {
        return !empty($this->handler)
            ? $this->handler
            : 'symfony';
    }
}
