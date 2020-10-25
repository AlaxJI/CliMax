<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CliMax\Command;

use CliMax\Controller;

/**
 * Description of Command
 *
 * @author alaxji
 */
interface ICommand
{
    const ARG_NONE = 'none';
    const ARG_OPTIONAL = 'optional';
    const ARG_REQUIRED = 'required';

    public function run($arguments, Controller $cliController);
    public function getUsage($aliases, $argLinker);
    public function getDescription($aliases, $argLinker);
    public function getArgumentType();
    public function getAllowsMultipleUse();
}
