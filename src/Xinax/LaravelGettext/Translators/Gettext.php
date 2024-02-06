<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Xinax\LaravelGettext\Translators;

use Exception;
use RuntimeException;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\LocaleNotSupportedException;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

/**
 * Class implemented by the php-gettext module translator
 * @package Xinax\LaravelGettext\Translators
 */
class Gettext extends BaseTranslator
{
    protected string $encoding;
    protected string $locale;
    protected array  $categories;
    protected string $domain;

    /**
     * @throws LocaleNotSupportedException
     */
    public function __construct(Config           $config,
                                AdapterInterface $adapter,
                                FileSystem       $fileSystem,
                                Storage          $storage)
    {
        parent::__construct($config, $adapter, $fileSystem, $storage);

        // General domain
        $this->domain = $this->storage->getDomain();

        // Encoding is set from configuration
        $this->encoding = $this->storage->getEncoding();

        // Categories are set from configuration
        $this->categories = $this->configuration->getCategories();

        // Sets defaults for boot
        $locale = $this->storage->getLocale();

        $this->setLocale($locale);
    }


    /**
     * Sets the current locale code
     * @throws LocaleNotSupportedException
     * @throws Exception
     */
    public function setLocale(string $locale): static
    {
        if (!$this->isLocaleSupported($locale)) {
            throw new LocaleNotSupportedException(
                sprintf('Locale %s is not supported', $locale)
            );
        }

        try {
            $customLocale = $this->configuration->getCustomLocale() ? "C." : $locale . ".";
            $gettextLocale = $customLocale . $this->getEncoding();

            // Update all categories set in config
            foreach ($this->categories as $category) {
                putenv("$category=$gettextLocale");
                setlocale(constant($category), $gettextLocale);
            }

            parent::setLocale($locale);

            // Laravel built-in locale
            if ($this->configuration->isSyncLaravel()) {
                $this->adapter->setLocale($locale);
            }
        }
        catch (Exception $e) {
            $this->locale = $this->configuration->getFallbackLocale();
            $exceptionPosition = $e->getFile() . ":" . $e->getLine();
            throw new Exception($exceptionPosition . $e->getMessage());
        }

        return $this;
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param string|null $locale
     * @return boolean
     */
    public function isLocaleSupported(?string $locale): bool
    {
        if ($locale) {
            return in_array($locale, $this->supportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale
     */
    public function __toString(): string
    {
        return $this->getLocale();
    }


    /**
     * Gets the Current encoding.
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding(string $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     * @return  self
     * @throws  UndefinedDomainException    If domain is not defined
     */
    public function setDomain(string $domain): static
    {
        parent::setDomain($domain);

        $customLocale = $this->configuration->getCustomLocale() ? "/" . $this->getLocale() : "";

        bindtextdomain($domain, $this->fileSystem->getDomainPath() . $customLocale);
        bind_textdomain_codeset($domain, $this->getEncoding());

        $this->domain = textdomain($domain);


        return $this;
    }

    /**
     * Translates a message with gettext
     *
     * @param string $message
     * @return string
     */
    public function translate(string $message): string
    {
        return gettext($message);
    }

    /**
     * Translates a plural message with gettext
     *
     * @param string $singular
     * @param string $plural
     * @param int $count
     *
     * @return string
     */
    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return ngettext($singular, $plural, $count);
    }

    public function translatePluralInline($message, $count): string
    {
        throw new RuntimeException('Not supported by gettext, please use Symfony');
    }
}
