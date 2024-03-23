<?php

namespace CliMaxTest\Command;

use CliMax\BaseCommand;
use CliMaxTest\Environment;

/**
 * Description of Init
 *
 * @author alaxji
 */
class Config extends BaseCommand
{
    public function __construct()
    {
        ;
    }

    public function run(array $arguments, \CliMax\Controller $cliController): int
    {
        $appendText = '';
        if ($cliController->hasEnvironment(Environment::VERBOSE)) {
            $appendText = ' with verbose ';
        }
        echo 'run Config command' . $appendText . PHP_EOL;

        return self::RETURN_CONTINUE_WORK;
    }

    public function getDescription(array $aliases, ?string $argLinker): string
    {
        return 'description of Config command';
    }
}
