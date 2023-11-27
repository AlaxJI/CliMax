<?php

namespace CliMax;

use CliMax\Command\ArugumentException;
use Exception;

/**
 * Description of Controller
 *
 * @author alaxji
 */
class Controller
{
    protected static $instances;

    const OPT_RETURN_INSTEAD_OF_EXIT = 'returnInsteadOfExit';
    const OPT_SLIENT = 'silent';
    const ERR_USAGE = -1;

    protected $commandMap = array();
    protected $usageCommands = array();
    protected $required = array();
    protected $defaultCommand = null;
    protected $defaultCommandAlwaysRuns = false;
    protected $environment = array();
    protected $description = '';
    /**
     * @var string The character linking a command flag to its argument. Default null (ie whitespace).
     */
    protected $argLinker = null;

    protected function __construct($opts = array())
    {
        $this->environment = $_ENV;
        $this->options = array_merge(array(
            self::OPT_RETURN_INSTEAD_OF_EXIT => false,
            self::OPT_SLIENT => false,
            ), $opts);
    }

    protected function __clone()
    {

    }

    /**
     *
     * @param array $opts
     * @return \CliMax\Controller
     */
    public static function create($opts = array())
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class($opts);
        }
        return self::$instances[$class];
    }

    public function mergeEnvironment($env, $opts = array())
    {
        if (!is_array($env)) {
            throw new Exception("Array required.");
        }

        if (isset($opts['overwrite']) && $opts['overwrite']) {
            $this->environment = array_merge($this->environment, $env);
        } else {
            $this->environment = array_merge($env, $this->environment);
        }

        return $this;
    }

    public function setEnvironment($key, $value = null)
    {
        if (is_array($key)) {
            if ($value !== null) {
                throw new Exception("When calling setEnvironment() with an array, only 1 parameter is accepted.");
            }
            $this->environment = $key;
        } else {
            $this->environment[$key] = $value;
        }

        return $this;
    }

    public function getEnvironment($key = null)
    {
        if ($key) {
            if (!array_key_exists($key, $this->environment)) {
                return null;
            }

            return $this->environment[$key];
        }

        return $this->environment;
    }

    public function hasEnvironment($key)
    {
        return array_key_exists($key, $this->environment);
    }

    public function addCommand($CliMaxCommand, $aliases = array())
    {
        if (!($CliMaxCommand instanceof \CliMax\Command\ICommand)) {
            throw new Exception('\CliMax\Command\ICommand required.');
        }

        if (!is_array($aliases)) {
            $aliases = array($aliases);
        }

        if (count($aliases) === 0) {
            throw new Exception('addCommand() requires at least one alias.');
        }

        foreach ($aliases as $alias) {
            if (isset($this->commandMap[$alias])) {
                throw new Exception('Command ' . get_class($this->commandMap[$alias]) . ' has already been registered for alias ' . $alias . '.');
            }
            $this->commandMap[$alias] = $CliMaxCommand;
        }
        $this->usageCommands[] = array('aliases' => $aliases, 'command' => $CliMaxCommand);

        return $this;
    }

    public function addEnvironmentFlagWithExactlyOneArgument($key, ?array $aliases = null, $opts = [])
    {
        if (is_null($aliases)) {
            $aliases = ['--' . $key];
        }
        $opts = array_merge($opts, [
            'requiresArgument' => true, // requiresArgument should always win
            'aliases' => $aliases
        ]);
        $environmentOption = new EnvironmentOption($key, $opts);
        $hash = spl_object_hash($environmentOption);
        if ($opts[EnvironmentOption::REQUIRED] ?? false) {
            $this->required[$hash] = [
                'option' => $environmentOption,
                'isAvaible' => false,
            ];
        }
        $this->addCommand($environmentOption, $aliases);

        return $this;
    }

    public function addEnvironmentFlagSetsValue($key, $flagSetsValue, $aliases = null, $opts = array())
    {
        if (is_null($aliases)) {
            $aliases = ['--' . $key];
        }
        // these values always win
        $opts = array_merge($opts, [
            'requiresArgument' => false,
            'noArgumentValue' => $flagSetsValue,
            'aliases' => $aliases
        ]);
        $environmentOption = new EnvironmentOption($key, $opts);
        $hash = spl_object_hash($environmentOption);
        if ($opts[EnvironmentOption::REQUIRED] ?? false) {
            $this->required[$hash] = [
                'option' => $environmentOption,
                'isAvaible' => false,
            ];
        }
        $this->addCommand($environmentOption, $aliases);

        return $this;
    }

    public function setDefaultCommand($CliMaxCommand, $opts = array())
    {
        if ($this->defaultCommand) {
            throw new Exception('A default command has already been registered.');
        }

        $this->defaultCommand = $CliMaxCommand;
        $this->defaultCommandAlwaysRuns = (isset($opts['alwaysRuns']) && $opts['alwaysRuns']);

        return $this;
    }

    public function run($argv, $argc, $opts = array())
    {
        // Shift script name from arguments
        array_shift($argv);

        $result = 0;
        $commands = array();
        $previousCommand = null;

        // convert argv stack into processable list
        $cmd = null;
        $cmdToken = null;
        $args = array();
        $defaultCommandArguments = array();
        while (true) {
            $token = array_shift($argv);
            //print "processing '{$token}'\n";
            if (is_null($token)) {    // reached end
                if ($cmd) {   // push last command
                    $commands[] = array('command' => $cmd, 'arguments' => $args, 'token' => $cmdToken);
                    $hash = spl_object_hash($cmd);
                    if (isset($this->required[$hash])) {
                        $this->required[$hash]['isAvaible'] = true;
                    }
                    $cmd = null;
                    $args = array();
                }
                if ($this->defaultCommand && (count($commands) === 0 || $this->defaultCommandAlwaysRuns)) {
                    //print "adding default command\n";
                    if (count($commands) >= 1) {
                        $args = $defaultCommandArguments;
                    }
                    $commands[] = array('command' => $this->defaultCommand, 'arguments' => $args, 'token' => '<default>');
                }
                break;
            }

            $nextCmd = $this->commandForToken($token);
            if ($nextCmd) {
                if ($cmd) {
                    $commands[] = array('command' => $cmd, 'arguments' => $args, 'token' => $cmdToken);
                    $hash = spl_object_hash($cmd);
                    if (isset($this->required[$hash])) {
                        $this->required[$hash]['isAvaible'] = true;
                    }
                } else {     // stash original set of arguments away for use with defaultCommand as needed
                    $defaultCommandArguments = $args;
                }
                $cmd = $nextCmd;
                $cmdToken = $token;
                $args = array();
            } else {
                $args[] = $token;
            }
        }

        if (count($commands) === 0) {
            return $this->usage();
        }

        // run commands
        $currentCommand = null;
        try {
            foreach ($this->required as $hash => $requireItem) {
                if (!$requireItem['isAvaible']) {
                    throw new ArugumentException("Missing required option '" . $requireItem['option']->getAlias() . "'");
                }
            }
            foreach ($commands as $key => $command) {
                $currentCommand = $command;
                //print "Calling " . get_class($command['command']) . "::run(" . join(', ', $command['arguments']) . ")";
                $cmdCallback = array($command['command'], 'run');
                if (!is_callable($cmdCallback)) {
                    throw new Exception("Not callable: " . var_export($cmdCallback, true));
                }
                $result = call_user_func_array($cmdCallback, array($command['arguments'], $this));
                if (is_null($result)) {
                    throw new Exception("Command " . get_class($command['command']) . " returned null.");
                }
                if ($result !== 0) {
                    break;
                }
            }
        } catch (ArugumentException $e) {
            $this->options[self::OPT_SLIENT] || fwrite(STDERR, "Error processing " . ($currentCommand['token'] ?? '') . ": {$e->getMessage()}\n");
            $result = -2;
        } catch (Exception $e) {
            $this->options[self::OPT_SLIENT] || fwrite(STDERR, get_class($e) . ": {$e->getMessage()}\n{$e->getTraceAsString()}\n");
            $result = -1;
        }

        if ($this->options['returnInsteadOfExit']) {
            return $result;
        }
        exit($result);
    }

    public function usage()
    {
        if (!empty($this->description)) {
            print $this->description . "\n------\n";
        }
        print "Usage:\n------\n";
        foreach ($this->usageCommands as $usageInfo) {
            print $usageInfo['command']->getUsage($usageInfo['aliases'], $this->argLinker) . "\n";
        }
        if ($this->options['returnInsteadOfExit']) {
            return self::ERR_USAGE;
        }
        exit(self::ERR_USAGE);
    }

    // returns the CLImaxCommand or null if not a command switch
    final protected function commandForToken($token)
    {
        if (isset($this->commandMap[$token])) {
            return $this->commandMap[$token];
        }

        return null;
    }
}
