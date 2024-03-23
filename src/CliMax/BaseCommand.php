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
    protected $token = '';

    public function setToken(string $token): ICommand
    {
        $this->token = $token;

        return $this;
    }

    public function getAllowsMultipleUse(): bool
    {
        return false;
    }

public function getArgumentType(): string
    {
        return ICommand::ARG_NONE;
    }

    public function getUsage(array $aliases, ?string $argLinker): string
    {
        // calculate arg linker string
        switch ($this->getArgumentType()) {
            case ICommand::ARG_NONE:
                $argLinker = null;
                break;
            default:
                $argLinker = "{$argLinker} <arg>";
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

    public function getDescription(array $aliases, ?string $argLinker): string
    {
        return isset($this->description) ? $this->description : '';
    }
}
