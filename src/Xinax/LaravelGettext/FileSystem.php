<?php namespace Xinax\LaravelGettext;

use FilesystemIterator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\View\Compilers\BladeCompiler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Xinax\LaravelGettext\Exceptions\FileCreationException;
use Xinax\LaravelGettext\Exceptions\LocaleFileNotFoundException;

class FileSystem
{
    /**
     * Package configuration model
     */
    protected Config $configuration;

    /**
     * File system base path
     * All paths will be relative to this
     */
    protected string $basePath;

    /**
     * Storage path for file generation
     */
    protected string $storagePath;

    /**
     * Storage directory name for view compilation
     */
    protected string $storageContainer;

    /**
     * The folder name in which the language files are stored
     */
    protected string $folderName;

    public function __construct(Config $config, string $basePath, string $storagePath)
    {
        $this->configuration = $config;
        $this->basePath = $basePath;
        $this->storagePath = $storagePath;
        $this->storageContainer = "framework";
        $this->folderName = 'i18n';
    }

    /**
     * Build views in order to parse php files
     *
     * @param array $viewPaths
     * @param string $domain
     * @return bool
     * @throws DirectoryNotFoundException
     * @throws FileCreationException
     * @throws FileNotFoundException
     */
    public function compileViews(array $viewPaths, string $domain): bool
    {
        // Check the output directory
        $targetDir = $this->storagePath . DIRECTORY_SEPARATOR . $this->storageContainer;

        if (!file_exists($targetDir)) {
            $this->createDirectory($targetDir);
        }

        // Domain separation
        $domainDir = $targetDir . DIRECTORY_SEPARATOR . $domain;
        $this->clearDirectory($domainDir);
        $this->createDirectory($domainDir);

        foreach ($viewPaths as $path) {
            $path = $this->basePath . DIRECTORY_SEPARATOR . $path;

            if (!$realPath = realPath($path)) {
                throw new Exceptions\DirectoryNotFoundException("Failed to resolve $path, please check that it exists");
            }

            $fs = new \Illuminate\Filesystem\Filesystem();
            $files = $fs->allFiles($realPath);

            $compiler = new BladeCompiler($fs, $domainDir);

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $compiler->setPath($filePath);

                $contents = $compiler->compileString($fs->get($filePath));

                $compiledPath = $compiler->getCompiledPath($compiler->getPath());

                $fs->put(
                    $compiledPath . '.php',
                    $contents
                );
            }
        }

        return true;
    }

    /**
     * Constructs and returns the full path to the translation files
     *
     * @param string|null $append
     * @return string
     */
    public function getDomainPath(string $append = null): string
    {
        $path = [
            $this->basePath,
            $this->configuration->getTranslationsPath(),
            $this->folderName,
        ];

        if (!is_null($append)) {
            $path[] = $append;
        }

        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Creates a configured .po file on $path
     * If PHP are not able to create the file the content will be returned instead
     *
     * @param string $path
     * @param string $locale
     * @param string $domain
     * @param bool $write
     * @return int|string
     * @throws DirectoryNotFoundException
     * @throws FileCreationException
     * @throws FileNotFoundException
     */
    public function createPOFile(string $path, string $locale, string $domain, bool $write = true): int|string
    {
        $project = $this->configuration->getProject();
        $timestamp = date("Y-m-d H:iO");
        $translator = $this->configuration->getTranslator();
        $encoding = $this->configuration->getEncoding();

        $relativePath = $this->configuration->getRelativePath();

        $keywords = implode(';', $this->configuration->getKeywordsList());

        $template = 'msgid ""' . "\n";
        $template .= 'msgstr ""' . "\n";
        $template .= '"Project-Id-Version: ' . $project . '\n' . "\"\n";
        $template .= '"POT-Creation-Date: ' . $timestamp . '\n' . "\"\n";
        $template .= '"PO-Revision-Date: ' . $timestamp . '\n' . "\"\n";
        $template .= '"Last-Translator: ' . $translator . '\n' . "\"\n";
        $template .= '"Language-Team: ' . $translator . '\n' . "\"\n";
        $template .= '"Language: ' . $locale . '\n' . "\"\n";
        $template .= '"MIME-Version: 1.0' . '\n' . "\"\n";
        $template .= '"Content-Type: text/plain; charset=' . $encoding . '\n' . "\"\n";
        $template .= '"Content-Transfer-Encoding: 8bit' . '\n' . "\"\n";
        $template .= '"X-Generator: Poedit 1.5.4' . '\n' . "\"\n";
        $template .= '"X-Poedit-KeywordsList: ' . $keywords . '\n' . "\"\n";
        $template .= '"X-Poedit-Basepath: ' . $relativePath . '\n' . "\"\n";
        $template .= '"X-Poedit-SourceCharset: ' . $encoding . '\n' . "\"\n";

        // Source paths
        $sourcePaths = $this->configuration->getSourcesFromDomain($domain);

        // Compiled views on paths
        if (count($sourcePaths)) {

            // View compilation
            $this->compileViews($sourcePaths, $domain);
            $sourcePaths[] = $this->getStorageForDomain($domain);

            $i = 0;

            foreach ($sourcePaths as $sourcePath) {
                $template .= '"X-Poedit-SearchPath-' . $i . ': ' . $sourcePath . '\n' . "\"\n";
                $i++;
            }

        }

        if (!$write) {
            return $template . "\n";
        }

        // File creation
        $file = fopen($path, "w");
        $result = fwrite($file, $template);
        fclose($file);

        return $result;
    }

    /**
     * Validate if the directory can be created
     *
     * @param string $path
     * @throws FileCreationException
     */
    protected function createDirectory(string $path): void
    {
        if (!file_exists($path) && !mkdir($path)) {
            throw new FileCreationException(
                "Can't create the directory: {$path}"
            );
        }
    }

    /**
     * Adds a new locale directory + .po file
     *
     * @param String $localePath
     * @param String $locale
     * @throws DirectoryNotFoundException
     * @throws FileCreationException
     * @throws FileNotFoundException
     */
    public function addLocale(string $localePath, string $locale): void
    {
        $data = array(
            $localePath,
            "LC_MESSAGES"
        );

        if (!file_exists($localePath)) {
            $this->createDirectory($localePath);
        }

        if ($this->configuration->getCustomLocale()) {
            $data[1] = 'C';

            $gettextPath = implode(DIRECTORY_SEPARATOR, $data);
            if (!file_exists($gettextPath)) {
                $this->createDirectory($gettextPath);
            }

            $data[2] = 'LC_MESSAGES';
        }

        $gettextPath = implode(DIRECTORY_SEPARATOR, $data);
        if (!file_exists($gettextPath)) {
            $this->createDirectory($gettextPath);
        }


        // File generation for each domain
        foreach ($this->configuration->getAllDomains() as $domain) {
            $data[3] = $domain . ".po";

            $localePOPath = implode(DIRECTORY_SEPARATOR, $data);

            if (!$this->createPOFile($localePOPath, $locale, $domain)) {
                throw new FileCreationException(
                    "Can't create the file: {$localePOPath}"
                );
            }
        }

    }

    /**
     * Update the .po file headers by domain
     * (mainly source-file paths)
     *
     * @param  $localePath
     * @param  $locale
     * @param  $domain
     * @return bool
     * @throws DirectoryNotFoundException
     * @throws FileCreationException
     * @throws FileNotFoundException
     * @throws LocaleFileNotFoundException
     */
    public function updateLocale($localePath, $locale, $domain): bool
    {
        $data = [
            $localePath,
            "LC_MESSAGES",
            $domain . ".po",
        ];

        if ($this->configuration->getCustomLocale()) {
            $customLocale = array('C');
            array_splice($data, 1, 0, $customLocale);
        }

        $localePOPath = implode(DIRECTORY_SEPARATOR, $data);

        if (!file_exists($localePOPath) || !$localeContents = file_get_contents($localePOPath)) {
            throw new LocaleFileNotFoundException(
                "Can't read {$localePOPath} verify your locale structure"
            );
        }

        $newHeader = $this->createPOFile(
            $localePOPath,
            $locale,
            $domain,
            false
        );

        // Header replacement
        $localeContents = preg_replace('/^([^#])+:?/', $newHeader, $localeContents);

        if (!file_put_contents($localePOPath, $localeContents)) {
            throw new LocaleFileNotFoundException(
                "Can't write on {$localePOPath}"
            );
        }

        return true;
    }

    /**
     * Return the relative path from a file or directory to anothe
     *
     * @param string $from
     * @param string $to
     * @return string
     * @author Laurent Goussard
     */
    public function getRelativePath(string $from, string $to): string
    {
        // Compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            if ($dir !== $to[$depth]) {
                // Number of remaining directories
                $remaining = count($from) - $depth;

                if ($remaining > 1) {
                    // Add traversals up to first matching directory
                    $padLength = (count($relPath) + $remaining - 1) * -1;

                    $relPath = array_pad(
                        $relPath,
                        $padLength,
                        '..'
                    );

                    break;
                }

                $relPath[0] = './' . $relPath[0];
            }

            array_shift($relPath);
        }

        return implode('/', $relPath);
    }

    /**
     * Checks the required directory
     * Optionally checks each local directory, if $checkLocales is true
     *
     * @param bool $checkLocales
     * @return bool
     * @throws DirectoryNotFoundException
     */
    public function checkDirectoryStructure(bool $checkLocales = false): bool
    {
        // Application base path
        if (!file_exists($this->basePath)) {
            throw new Exceptions\DirectoryNotFoundException(
                "Missing root path directory: {$this->basePath}, check the 'base-path' key in your configuration."
            );
        }

        // Domain path
        $domainPath = $this->getDomainPath();

        // Translation files domain path
        if (!file_exists($domainPath)) {
            throw new Exceptions\DirectoryNotFoundException(
                "Missing base required directory: {$domainPath}, remember to run 'artisan gettext:create' the first time"
            );
        }

        if (!$checkLocales) {
            return true;
        }

        foreach ($this->configuration->getSupportedLocales() as $locale) {
            // Default locale is not needed
            if ($locale == $this->configuration->getLocale()) {
                continue;
            }

            $localePath = $this->getDomainPath($locale);

            if (!file_exists($localePath)) {
                throw new Exceptions\DirectoryNotFoundException(
                    "Missing locale required directory: {$locale}, maybe you forgot to run 'artisan gettext:update'"
                );
            }
        }

        return true;
    }

    /**
     * Creates the localization directories and files by domain
     *
     * @return array
     * @throws DirectoryNotFoundException
     * @throws FileCreationException
     * @throws FileNotFoundException
     */
    public function generateLocales(): array
    {
        // Application base path
        if (!file_exists($this->getDomainPath())) {
            $this->createDirectory($this->getDomainPath());
        }
        $localePaths = [];

        // Locale directories
        foreach ($this->configuration->getSupportedLocales() as $locale) {
            $localePath = $this->getDomainPath($locale);

            if (!file_exists($localePath)) {
                // Locale directory is created
                $this->addLocale($localePath, $locale);

                $localePaths[] = $localePath;

            }
        }

        return $localePaths;
    }


    /**
     * Gets the package configuration model.
     *
     * @return Config
     */
    public function getConfiguration(): Config
    {
        return $this->configuration;
    }

    /**
     * Set the package configuration model
     *
     * @param Config $configuration
     * @return $this
     */
    public function setConfiguration(Config $configuration): static
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Get the filesystem base path
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Set the filesystem base path
     *
     * @param string $basePath
     * @return $this
     */
    public function setBasePath(string $basePath): static
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Get the storage path
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * Set the storage path
     *
     * @param string $storagePath
     * @return $this
     */
    public function setStoragePath(string $storagePath): static
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    /**
     * Get the full path for domain storage directory
     *
     * @param string $domain
     * @return String
     */
    public function getStorageForDomain(string $domain): string
    {
        $domainPath = $this->storagePath .
            DIRECTORY_SEPARATOR .
            $this->storageContainer .
            DIRECTORY_SEPARATOR .
            $domain;

        return $this->getRelativePath($this->basePath, $domainPath);
    }

    /**
     * Removes the directory contents recursively
     *
     * @param string $path
     * @return null|boolean
     */
    public static function clearDirectory(string $path): ?bool
    {
        if (!file_exists($path)) {
            return null;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            // if the file isn't a .gitignore file we should remove it.
            if ($fileinfo->getFilename() !== '.gitignore') {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
        }

        // since the folder now contains a .gitignore we can't remove it
        //rmdir($path);
        return true;
    }

    /**
     * Get the folder name
     *
     * @return string
     */
    public function getFolderName(): string
    {
        return $this->folderName;
    }

    /**
     * Set the folder name
     *
     * @param string $folderName
     */
    public function setFolderName(string $folderName): void
    {
        $this->folderName = $folderName;
    }

    /**
     * Returns the full path for a .po/.mo file from its domain and locale
     *
     * @param string $locale
     * @param string $domain
     * @param string $type
     *
     * @return string
     */
    public function makeFilePath(string $locale, string $domain, string $type = 'po'): string
    {
        $filePath = implode(
            DIRECTORY_SEPARATOR, [
                $locale,
                'LC_MESSAGES',
                $domain . "." . $type
            ]
        );

        return $this->getDomainPath($filePath);
    }

}
