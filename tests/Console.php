<?php

namespace CliMaxTest;

use CliMax\Controller;
use CliMaxTest\Command\Init;
use CliMaxTest\Command\Config;
use CliMaxTest\Environment;

/**
 * Description of Controller
 *
 * @author alaxji
 */
class Console extends Controller
{
    protected function __construct($opts = array())
    {
        parent::__construct($opts);
        $this
            ->addEnvironmentFlagWithExactlyOneArgument(
                Environment::FILE,
                array('-f', '--file'),
                array('description' => 'decaription of file option',)
            )
            ->addEnvironmentFlagSetsValue(
                Environment::VERBOSE,
                false,
                array('-v', '--verbose'),
                array('description' => 'decaription of verbose option',)
            )
            ->addCommand(new Init(), array('init'))
            ->addCommand(new Config(), 'config');
    }
}
