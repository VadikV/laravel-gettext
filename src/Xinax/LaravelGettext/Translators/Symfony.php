<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\UndefinedDomainException;
use Xinax\LaravelGettext\FileLoader\Cache\ApcuFileCacheLoader;
use Xinax\LaravelGettext\FileLoader\MoFileLoader;
use Xinax\LaravelGettext\FileSystem;
use Xinax\LaravelGettext\Storages\Storage;

/**
 * Class implemented by Symfony translation component
 *
 * @package Xinax\LaravelGettext\Translators
 */
class Symfony extends BaseTranslator
{
    protected ?SymfonyTranslator $symfonyTranslator = null;
    protected array              $loadedResources   = [];

    public function __construct(Config           $config,
                                AdapterInterface $adapter,
                                FileSystem       $fileSystem,
                                Storage          $storage)
    {
        parent::__construct($config, $adapter, $fileSystem, $storage);

        $this->setLocale($this->storage->getLocale());
        $this->loadLocaleFile();
    }


    /**
     * Translates a message using the Symfony translation component
     *
     * @param string $message
     *
     * @return string
     */
    public function translate(string $message): string
    {
        return $this->symfonyTranslator->trans($message, [], $this->getDomain(), $this->getLocale());
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator(): SymfonyTranslator
    {
        if (isset($this->symfonyTranslator)) {
            return $this->symfonyTranslator;
        }

        return $this->symfonyTranslator = $this->createTranslator();
    }

    /**
     * Set locale overload.
     * Needed to re-build the catalogue when locale changes.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        parent::setLocale($locale);
        $this->getTranslator()->setLocale($locale);
        $this->loadLocaleFile();

        if ($locale != $this->adapter->getLocale()) {
            $this->adapter->setLocale($locale);
        }

        return $this;
    }

    /**
     * Set domain overload.
     * Needed to re-build the catalogue when domain changes.
     *
     *
     * @param String $domain
     *
     * @return $this
     * @throws UndefinedDomainException
     */
    public function setDomain(string $domain): static
    {
        parent::setDomain($domain);

        $this->loadLocaleFile();

        return $this;
    }

    /**
     * Creates a new translator instance
     *
     * @return SymfonyTranslator
     */
    protected function createTranslator(): SymfonyTranslator
    {
        $translator = new SymfonyTranslator($this->configuration->getLocale());
        $translator->setFallbackLocales([$this->configuration->getFallbackLocale()]);
        $translator->addLoader('mo', new ApcuFileCacheLoader(new MoFileLoader()));
        $translator->addLoader('po', new ApcuFileCacheLoader(new PoFileLoader()));

        return $translator;
    }

    /**
     * Translates a plural string
     *
     * @param string $singular
     * @param string $plural
     * @param int $count
     *
     * @return string
     */
    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return $this->symfonyTranslator->trans(
            $count > 1
                ? $plural
                : $singular,
            ['%count%' => $count],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param string $message
     * @param int $count
     *
     * @return string
     */
    public function translatePluralInline(string $message, int $count): string
    {
        return $this->symfonyTranslator->trans(
            $message,
            [
                '%count%' => $count
            ],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * @internal param $translator
     */
    protected function loadLocaleFile(): void
    {
        if (isset($this->loadedResources[$this->getDomain()])
            && isset($this->loadedResources[$this->getDomain()][$this->getLocale()])
        ) {
            return;
        }
        $translator = $this->getTranslator();

        $fileMo = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain(), 'mo');
        if (file_exists($fileMo)) {
            $translator->addResource('mo', $fileMo, $this->getLocale(), $this->getDomain());
        }
        else {
            $file = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain());
            $translator->addResource('po', $file, $this->getLocale(), $this->getDomain());
        }

        $this->loadedResources[$this->getDomain()][$this->getLocale()] = true;
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
     */
    public function __toString(): string
    {
        return $this->getLocale();
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
