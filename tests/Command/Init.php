<?php

namespace CliMaxTest\Command;

use CliMax\BaseCommand;
/**
 * Description of Init
 *
 * @author alaxji
 */
class Init extends BaseCommand
{
    public function __construct()
    {
        ;
    
    }
    public function run($arguments, \CliMax\Controller $cliController)
    {
        print 'run Init command ' . PHP_EOL
            . "\n    with enveronment [" . implode(', ', array_keys($cliController->getEnvironment())) . ']' . PHP_EOL
            . "\t    and arguments [" . implode(', ', $arguments) . ']' . PHP_EOL;
        return -1;
    }

    public function getDescription($aliases, $argLinker)
    {
        return 'description of Init command';
    }
}
