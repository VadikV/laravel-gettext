<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 18-02-23
 * Time: 11:50
 */

namespace Xinax\LaravelGettext\Testing\Adapter;

use Xinax\LaravelGettext\Adapters\AdapterInterface;

class TestAdapter implements AdapterInterface
{
    private string $locale = 'en_US';

    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets the locale on the adapter
     *
     * @param string $locale
     *
     * @return boolean
     */
    public function setLocale(string $locale): bool
    {
        $this->locale = $locale;

        return true;
    }

    /**
     * Get the application path
     *
     * @return string
     */
    public function getApplicationPath(): string
    {
        return app_path();
    }
}
