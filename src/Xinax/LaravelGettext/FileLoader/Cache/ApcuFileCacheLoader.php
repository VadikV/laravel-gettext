<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Xinax\LaravelGettext\FileLoader\Cache;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\FileLoader;

class ApcuFileCacheLoader extends FileLoader
{

    public function __construct(private readonly FileLoader $underlyingFileLoader)
    {
    }

    /**
     * @param string $resource
     *
     * @return array
     *
     * @throws InvalidResourceException if stream content has an invalid format
     */
    protected function loadResource(string $resource): array
    {
        if (!extension_loaded('apcu')) {
            return $this->underlyingFileLoader->loadResource($resource);
        }

        return $this->cachedMessages($resource);
    }

    /**
     * Calculate the checksum for the file
     *
     * @param $resource
     *
     * @return string
     */
    private function checksum($resource): string
    {
        return filemtime($resource) . '-' . filesize($resource);
    }

    /**
     * Checksum saved in cache
     *
     * @param string $resource
     *
     * @return string
     */
    private function cacheChecksum(string $resource): string
    {
        return apcu_fetch($resource . '-checksum');
    }

    /**
     * Set the cache checksum
     *
     * @param string $resource
     * @param $checksum
     *
     * @return void
     */
    private function setCacheChecksum(string $resource, $checksum): void
    {
        apcu_store($resource . '-checksum', $checksum);
    }

    /**
     * Return the cached messages
     *
     * @param string $resource
     *
     * @return array
     */
    private function cachedMessages(string $resource): array
    {
        if ($this->cacheChecksum($resource) == ($currentChecksum = $this->checksum($resource))) {
            return apcu_fetch($resource . '-messages');
        }

        $messages = $this->underlyingFileLoader->loadResource($resource);

        apcu_store($resource . '-messages', $messages);
        $this->setCacheChecksum($resource, $currentChecksum);

        return $messages;
    }
}
