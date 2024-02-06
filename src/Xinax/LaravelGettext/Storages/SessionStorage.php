<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 03/02/17
 * Time: 10:08 AM
 */

namespace Xinax\LaravelGettext\Storages;

use Illuminate\Support\Facades\Session;
use Xinax\LaravelGettext\Config\Models\Config;

class SessionStorage implements Storage
{
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
        return $this->sessionGet('domain', $this->configuration->getDomain());
    }

    /**
     * @param String $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): static
    {
        $this->sessionSet('domain', $domain);

        return $this;
    }

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getLocale(): string
    {
        return $this->sessionGet('locale', $this->configuration->getLocale());
    }

    /**
     * @param String $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->sessionSet('locale', $locale);

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
     * Return a value from session with an optional default
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    protected function sessionGet($key, $default = null): mixed
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;

        return Session::get($token, $default);
    }

    /**
     * Sets a value in session session
     *
     * @param $key
     * @param $value
     * @return SessionStorage
     */
    protected function sessionSet($key, $value): static
    {
        $token = $this->configuration->getSessionIdentifier() . "-" . $key;
        Session::put($token, $value);

        return $this;
    }

    /**
     * Getter for locale
     *
     * @return String
     */
    public function getEncoding(): string
    {
        return $this->sessionGet('encoding', $this->configuration->getEncoding());
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->sessionSet('encoding', $encoding);

        return $this;
    }
}