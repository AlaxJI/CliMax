<?php

namespace CliMax\Command;

use CliMax\Controller;

/**
 * Description of Command
 *
 * @author alaxji
 */
interface ICommand
{
    public const ARG_NONE = 'none';
    public const ARG_OPTIONAL = 'optional';
    public const ARG_REQUIRED = 'required';
    public const RETURN_CONTINUE_WORK = 0;
    public const RETURN_STOP_WORK = -2;

    public function setToken(string $token): ICommand;

    /**
     * Запуск команды на выполнение
     * @param array $arguments
     * @param Controller $cliController
     * @return int Код выхода. Если не равно <code><b>0</b></code> – выполнение команд прекращается.
     */
    public function run(array $arguments, Controller $cliController): int;

    public function getUsage(array $aliases, ?string $argLinker): string;

    public function getDescription(array $aliases, ?string $argLinker): string;

    public function getArgumentType(): string;

    public function getAllowsMultipleUse(): bool;
}
