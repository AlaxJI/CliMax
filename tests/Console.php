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
    protected function __construct($opts = [])
    {
        parent::__construct($opts);
        $this
            ->addEnvironmentFlagWithExactlyOneArgument(
                Environment::FILE,
                ['-f', '--file'],
                ['description' => 'decaription of file option',]
            )
            ->addEnvironmentFlagSetsValue(
                Environment::VERBOSE,
                false,
                ['-v', '--verbose'],
                ['description' => 'decaription of verbose option',]
            )
            ->addCommand(new Init(), ['init'])
            ->addCommand(new Config(), 'config');
    }
}
