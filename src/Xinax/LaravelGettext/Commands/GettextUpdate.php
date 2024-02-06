<?php

namespace Xinax\LaravelGettext\Commands;

use Exception;
use Symfony\Component\Console\Input\InputOption;
use Xinax\LaravelGettext\Exceptions\DirectoryNotFoundException;
use Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;

class GettextUpdate extends BaseCommand
{

    protected $name = 'gettext:update';

    protected $description = 'Update PO files (when you modify configuration).';

    /**
     * @throws RequiredConfigurationKeyException
     */
    public function handle(): int
    {
        $this->prepare();

        $domainPath = $this->fileSystem->getDomainPath();
        $fileSystem = $this->fileSystem;

        try {
            // Translation files base path
            if (!file_exists($domainPath)) {
                throw new DirectoryNotFoundException(
                    "You need to call gettext:create (No locale directory)"
                );
            }

            $count = [
                'added'   => 0,
                'updated' => 0,
            ];

            $domains = $this->configuration->getAllDomains();

            foreach ($this->configuration->getSupportedLocales() as $locale) {
                $localePath = $this->fileSystem->getDomainPath($locale);

                // Create new locale
                if (!file_exists($localePath)) {
                    $this->fileSystem->addLocale($localePath, $locale);
                    $this->comment("New locale was added: $locale ($localePath)");

                    $count['added']++;

                    continue;
                }

                // Domain by command line argument
                if ($this->option('domain')) {
                    $domains = [$this->option('domain')];
                }

                // Update by domain(s)
                foreach ($domains as $domain) {
                    $fileSystem->updateLocale(
                        $localePath,
                        $locale,
                        $domain
                    );

                    $this->comment(
                        sprintf(
                            "PO file for locale: %s/%s updated successfully",
                            $locale,
                            $domain
                        )
                    );

                    $count['updated']++;
                }
            }

            $this->info("Finished");

            // Number of locales created
            if ($count['added'] > 0) {
                $this->info(sprintf('%s new locales were added.', $count['added']));
            }


            // Number of locales updated
            if ($count['updated'] > 0) {
                $this->info(sprintf('%s locales updated.', $count['updated']));
            }

        }
        catch (Exception $e) {
            $this->error($e->getFile() . ":" . $e->getLine() . " = " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'domain',
                '-d',
                InputOption::VALUE_OPTIONAL,
                'Update files only for this domain',
                null,
            ]
        ];
    }
}
