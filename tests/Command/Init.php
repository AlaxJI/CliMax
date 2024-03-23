<?php

namespace CliMaxTest\Command;

use CliMax\BaseCommand;
use CliMax\Command\ICommand;

/**
 * Description of Init
 *
 * @author alaxji
 */
class Init extends BaseCommand
{
    public function run(array $arguments, \CliMax\Controller $cliController): int
    {
        print 'run Init command ' . PHP_EOL
            . "\n    with enveronment [" . var_export($cliController->getEnvironment(), true) . ']' . PHP_EOL
            . "\t    and arguments [" . implode(', ', $arguments) . ']' . PHP_EOL;

        return self::RETURN_STOP_WORK;
    }

    public function getDescription(array $aliases, ?string $argLinker): string
    {
        return 'description of Init command';
    }
}
