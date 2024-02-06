<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 03/02/17
 * Time: 10:08 AM
 */

namespace Xinax\LaravelGettext\Storages;


use Xinax\LaravelGettext\Config\Models\Config;

class MemoryStorage implements Storage
{
    protected ?string $domain   = null;
    protected ?string $locale   = null;
    protected ?string $encoding = null;

    public function __construct(private readonly Config $configuration)
    {
    }

    /**
     * Getter for domain
     *
     * @return String
     */
    public function getDomain(): string
    {
        return $this->domain ?: $this->configuration->getDomain();
    }

    /**
     * @param String $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getLocale(): string
    {
        return $this->locale ?: $this->configuration->getLocale();
    }

    /**
     * @param String $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Getter for configuration
     *
     * @return Config
     */
    public function getConfiguration(): Config
    {
        return $this->configuration;
    }

    /**
     * Getter for encoding
     *
     * @return String
     */
    public function getEncoding(): string
    {
        return $this->encoding ?: $this->configuration->getEncoding();
    }

    /**
     * @param String $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->encoding = $encoding;

        return $this;
    }


}
