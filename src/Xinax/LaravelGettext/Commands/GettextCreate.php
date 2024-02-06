<?php

namespace Xinax\LaravelGettext\Commands;

use Exception;
use Xinax\LaravelGettext\Exceptions\RequiredConfigurationKeyException;

class GettextCreate extends BaseCommand
{

    protected $name = 'gettext:create';

    protected $description =
        'Generates the initial directories and files for laravel-gettext.';

    /**
     * @throws RequiredConfigurationKeyException
     */
    public function handle(): int
    {
        $this->prepare();

        // Directories created counter
        $dirCreations = 0;

        try {
            // Locales
            $localesGenerated = $this->fileSystem->generateLocales();

            foreach ($localesGenerated as $localePath) {
                $this->comment(sprintf("Locale directory created (%s)", $localePath));
                $dirCreations++;
            }

            $this->info("Finished");

            $msg = "The directory structure is right. No directory creation were needed.";

            if ($dirCreations) {
                $msg = $dirCreations . " directories has been created.";
            }

            $this->info($msg);

        }
        catch (Exception $e) {
            $this->error($e->getFile() . ":" . $e->getLine() . " - " . $e->getMessage());
        }

        return 0;
    }
}
