<?php

namespace Xinax\LaravelGettext\Adapters;

interface AdapterInterface
{
    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Sets the locale on the adapter
     *
     * @param string $locale
     * @return boolean
     */
    public function setLocale(string $locale): bool;

    /**
     * Get the application path
     *
     */
    public function getApplicationPath(): string;
}
