<?php

namespace CliMaxTest\Command;

use CliMax\BaseCommand;
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
        echo 'run Config command'.PHP_EOL;

        return -2;
    }

    public function getDescription($aliases, $argLinker)
    {
        return 'description of Config command';
    }
}
