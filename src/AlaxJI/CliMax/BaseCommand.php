<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CliMax;

use CliMax\Command\ICommand;

/**
 * Description of BaseCommand
 *
 * @author alaxji
 */
abstract class BaseCommand implements ICommand
{
    public function getAllowsMultipleUse()
    {
        return false;
    }

    public function getArgumentType()
    {
        return ICommand::ARG_NONE;
    }

    public function getUsage($aliases, $argLinker)
    {
        // calculate arg linker string
        switch ($this->getArgumentType()) {
            case ICommand::ARG_NONE:
                $argLinker = null;
                break;
            default:
                $argLinker = "{$argLinker}<arg>";
                break;
        }
        $cmd = null;
        foreach ($aliases as $alias) {
            if ($cmd) {
                $cmd .= " / ";
            }
            $cmd .= "{$alias}{$argLinker}";
        }

        $description = $this->getDescription($aliases, $argLinker);
        if ($description) {
            $cmd .= "\n  {$description}\n";
        }

        return $cmd;
    }

    public function getDescription($aliases, $argLinker)
    {
        return isset($this->description) ? $this->description : null;
    }
}
