<?php namespace Xinax\LaravelGettext\Translators;

use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

abstract class BaseTranslator implements TranslatorInterface
{
    public function __construct(protected readonly Config           $configuration,
                                protected readonly AdapterInterface $adapter,
                                protected readonly FileSystem       $fileSystem,
                                protected readonly Storage          $storage)
    {
    }

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale(): string
    {
        return $this->storage->getLocale();
    }

    /**
     * Sets and stores on session the current locale code
     *
     * @param string $locale
     *
     * @return BaseTranslator
     */
    public function setLocale(string $locale): static
    {
        if ($locale == $this->storage->getLocale()) {
            return $this;
        }

        $this->storage->setLocale($locale);

        return $this;
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param string|null $locale
     *
     * @return bool
     */
    public function isLocaleSupported(?string $locale): bool
    {
        if ($locale) {
            return in_array($locale, $this->configuration->getSupportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale
     *
     * @return String
     */
    public function __toString(): string
    {
        return $this->getLocale();
    }

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding(): string
    {
        return $this->storage->getEncoding();
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     *
     * @return self
     */
    public function setEncoding(string $encoding): static
    {
        $this->storage->setEncoding($encoding);

        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     *
     * @return  self
     * @throws  UndefinedDomainException    If domain is not defined
     */
    public function setDomain(string $domain): static
    {
        if (!in_array($domain, $this->configuration->getAllDomains())) {
            throw new UndefinedDomainException("Domain '$domain' is not registered.");
        }

        $this->storage->setDomain($domain);

        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain(): string
    {
        return $this->storage->getDomain();
    }


    /**
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales(): array
    {
        return $this->configuration->getSupportedLocales();
    }

}