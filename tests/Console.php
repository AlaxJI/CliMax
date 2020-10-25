<?php
namespace CliMaxTest;

use CliMax\Controller;
use CliMaxTest\Command\Init;
use CliMaxTest\Command\Config;
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
            ->addEnvironmentFlagSetsValue(
                'verbose',
                false,
                array('-v', '--verbose'),
                array('description' => 'decaription of verbose option',)
            )
            ->addCommand(new Init(), array('init'))
            ->addCommand(new Config(), 'config');
    }
}
