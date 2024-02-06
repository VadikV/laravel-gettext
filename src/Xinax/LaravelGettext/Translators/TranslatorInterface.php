<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

interface TranslatorInterface
{

    /**
     * Initializes the module translator
     *
     * @param Config $config
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     *
     * @param Storage $storage
     */
    public function __construct(Config           $config,
                                AdapterInterface $adapter,
                                FileSystem       $fileSystem,
                                Storage          $storage);

    /**
     * Sets the current locale code
     */
    public function setLocale(string $locale);

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale(): string;

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param string|null $locale
     * @return boolean
     */
    public function isLocaleSupported(?string $locale): bool;

    /**
     * Returns supported locales
     *
     * @return string[]
     */
    public function supportedLocales(): array;

    /**
     * Return the current locale
     *
     * @return mixed
     */
    public function __toString(): string;

    /**
     * Gets the Current encoding.
     *
     * @return string
     */
    public function getEncoding(): string;

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     *
     * @return self
     */
    public function setEncoding(string $encoding): static;

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     *
     * @return  self
     * @throws  UndefinedDomainException If domain is not defined
     */
    public function setDomain(string $domain): static;

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain(): string;

    /**
     * Translates a single message
     *
     * @param string $message
     * @return string
     */
    public function translate(string $message): string;

    /**
     * Translates a plural string
     *
     * @param string $singular
     * @param string $plural
     * @param int $count
     *
     * @return mixed
     */
    public function translatePlural(string $singular, string $plural, int $count): string;

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param string $message
     * @param int $count
     *
     * @return string
     */
    public function translatePluralInline(string $message, int $count): string;
}
