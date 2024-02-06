<?php

namespace Xinax\LaravelGettext\Adapters;

use Illuminate\Support\Facades\App;

class LaravelAdapter implements AdapterInterface
{
    /**
     * Set current locale
     *
     * @param string $locale
     * @return bool
     */
    public function setLocale(string $locale): bool
    {
        App::setLocale(substr($locale, 0, 2));
        return true;
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get the application path
     *
     * @return string
     */
    public function getApplicationPath(): string
    {
        return App::path();
    }
}
