<?php

namespace CliMax;

use CliMax\Command\ArugumentException;

class EnvironmentOption extends BaseCommand
{
    protected $environmentKey;
    protected $requiresArgument;
    protected $allowsMultipleArguments;
    protected $noArgumentValue;
    protected $allowedValues;
    protected $description = null;

    public function __construct($environmentKey, $opts = array())
    {
        $this->environmentKey = $environmentKey;
        $opts = array_merge(array(
            'requiresArgument' => false,
            'allowsMultipleArguments' => false,
            'noArgumentValue' => null,
            'allowedValues' => null,
            ), $opts);
        $this->requiresArgument = $opts['requiresArgument'];
        $this->allowsMultipleArguments = $opts['allowsMultipleArguments'];
        $this->noArgumentValue = $opts['noArgumentValue'];
        $this->allowedValues = $opts['allowedValues'];
        if (!empty($opts['description'])) {
            $this->description = $opts['description'];
        }
    }

    public function run($arguments, Controller $cliController)
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

        if (!is_array($arguments)) {
            throw new Exception("Arguments should be an array but wasn't. Internal fail.");
        }
        if ($this->allowedValues) {
            $badArgs = array_diff($arguments, $this->allowedValues);
            if (count($badArgs) > 0) {
                throw new ArugumentException("Invalid argument(s): " . join(', ', $badArgs));
            }
        }

        // flatten argument to a single value as a convenience for working with the environment data later
        if (count($arguments) === 1) {
            $arguments = $arguments[0];
        }

        $cliController->setEnvironment($this->environmentKey, $arguments);

        return 0;
    }
}
