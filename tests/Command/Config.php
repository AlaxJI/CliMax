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

    public function run($arguments, \CliMax\Controller $cliController)
    {
        $appendText = '';
        if ($cliController->hasEnvironment(Environment::VERBOSE)) {
            $appendText = ' with verbose ';
        }
        echo 'run Config command' . $appendText . PHP_EOL;

        return -2;
    }

    public function getDescription($aliases, $argLinker)
    {
        return 'description of Config command';
    }
}
