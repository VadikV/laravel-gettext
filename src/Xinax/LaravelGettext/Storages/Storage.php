<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 03/02/17
 * Time: 10:10 AM
 */

namespace Xinax\LaravelGettext\Storages;

use Xinax\LaravelGettext\Config\Models\Config;

interface Storage
{
    /**
     * Getter for domain
     *
     * @return String
     */
    public function getDomain(): string;

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): static;

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getLocale(): string;

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static;

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getEncoding(): string;

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): static;

    /**
     * Getter for configuration
     *
     * @return Config
     */
    public function getConfiguration(): Config;
}