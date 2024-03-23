<?php

namespace CliMax;

use CliMax\Command\ArugumentException;

class EnvironmentOption extends BaseCommand
{
    /**
     * Обязательный параметр, по-умолчанию <code>FALSE</code>
     */
    public const REQUIRED = 'required';
    /**
     * Обязательный агрумент параметра, по-умолчанию <code>FALSE</code>
     */
    public const REQUIRES_ARGUMENT = 'requiresArgument';
    /**
     * Допустимо передавать несколько аргументов, по-умолчанию <code>FALSE</code>
     */
    public const ALLOWS_MULTIPLE_ARGUMENTS = 'allowsMultipleArguments';
    /**
     * Значения аргументов, которые будут подставлены по-умолчанию, по-умолчанию <code>[]</code>
     */
    public const NO_ARGUMENT_VALUE = 'noArgumentValue';
    /**
     * Допустимые значения аргументов, по-умолчанию <code>[]</code>
     */
    public const ALLOWED_VALUES = 'allowedValues';
    /**
     * ..., по-умолчанию <code>[]</code>
     */
    public const ALIASES = 'aliases';
    /**
     * Описание параметра, по-умолчанию <code><пусто></code>
     */
    public const DESCRIPTION = 'description';

    protected $required = false;
    protected $environmentKey;
    protected $requiresArgument;
    protected $allowsMultipleArguments;
    protected $noArgumentValue;
    protected $allowedValues;
    protected $aliases = [];
    protected $description = null;

    public function __construct($environmentKey, $opts = array())
    {
        $this->environmentKey = $environmentKey;
        $opts = array_merge([
            'required' => false,
            'requiresArgument' => false,
            'allowsMultipleArguments' => false,
            'noArgumentValue' => [],
            'allowedValues' => [],
            'aliases' => [],
            ], $opts);
        $this->required = $opts[static::REQUIRED];
        $this->requiresArgument = $opts['requiresArgument'];
        $this->allowsMultipleArguments = $opts['allowsMultipleArguments'];
        $this->noArgumentValue = $opts['noArgumentValue'];
        $this->allowedValues = $opts['allowedValues'];
        $this->aliases = $opts['aliases'];
        if (!empty($opts['description'])) {
            $this->description = $opts['description'];
        }
    }

    public function run(array $arguments, Controller $cliController): int
    {
        // argument checks
        if ($this->requiresArgument && count($arguments) === 0) {
            throw new ArugumentException("Argument required.");
        }
        if (!$this->allowsMultipleArguments && count($arguments) > 1) {
            throw new ArugumentException("Only one argument accepted.");
        }

        if (count($arguments) === 0 && $this->noArgumentValue) {
            $arguments = array($this->noArgumentValue);
        }

        if ($this->allowedValues) {
            $badArgs = array_diff($arguments, $this->allowedValues);
            if (count($badArgs) > 0) {
                throw new ArugumentException("Invalid argument(s): " . join(', ', $badArgs));
            }
        }

        $cliController->setEnvironment($this->environmentKey, new Option($this->environmentKey, $this->aliases, $arguments, $this->token));

        return self::RETURN_CONTINUE_WORK;
    }

    public function getAlias(): ?string
    {
        return $this->aliases[0] ?? null;
    }

    public function getKey(): ?string
    {
        return $this->environmentKey;
    }
}
