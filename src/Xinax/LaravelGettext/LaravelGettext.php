<?php

namespace Xinax\LaravelGettext;

use Xinax\LaravelGettext\Composers\LanguageSelector;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Xinax\LaravelGettext\Translators\TranslatorInterface;

class LaravelGettext
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $gettext)
    {
        $this->translator = $gettext;
    }

    /**
     * Get the current encoding
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->translator->getEncoding();
    }

    /**
     * Set the current encoding
     *
     * @param string $encoding
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->translator->setEncoding($encoding);
        return $this;
    }

    /**
     * Gets the Current locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * Set current locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        if ($locale != $this->getLocale()) {
            $this->translator->setLocale($locale);
        }

        return $this;
    }

    /**
     * Get the language portion of the locale
     * (ex. en_GB returns en)
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocaleLanguage(string $locale = null): ?string
    {
        if (is_null($locale)) {
            $locale = $this->getLocale();
        }

        $localeArray = explode('_', $locale);

        if (!isset($localeArray[0])) {
            return null;
        }

        return $localeArray[0];
    }

    /**
     * Get the language selector object
     *
     * @param array $labels
     * @return LanguageSelector
     */
    public function getSelector(array $labels = []): LanguageSelector
    {
        return LanguageSelector::create($this, $labels);
    }

    /**
     * Sets the current domain
     *
     * @param string $domain
     * @return $this
     * @throws UndefinedDomainException
     */
    public function setDomain(string $domain): static
    {
        $this->translator->setDomain($domain);
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->translator->getDomain();
    }

    /**
     * Translates a message with the current handler
     *
     * @param string $message
     * @return string
     */
    public function translate(string $message): string
    {
        return $this->translator->translate($message);
    }

    /**
     * Translates a plural string with the current handler
     *
     * @param string $singular
     * @param string $plural
     * @param int $count
     * @return string
     */
    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return $this->translator->translatePlural($singular, $plural, $count);
    }

    /**
     * Returns the translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * Sets the translator
     *
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator): static
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Returns supported locales
     *
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return $this->translator->supportedLocales();
    }

    /**
     * Indicates if given locale is supported
     *
     * @param string|null $locale
     * @return bool
     */
    public function isLocaleSupported(?string $locale): bool
    {
        return $this->translator->isLocaleSupported($locale);
    }
}
