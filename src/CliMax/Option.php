<?php

namespace CliMax;

class Option
{
    private string $key = '';
    private array $aliases = [];
    private array $arguments = [];
    /**
     * Опция, которая пришла в запросе
     * @var string
     */
    private string $token = '';

    public function __construct(string $key, array $aliases = [], array $arguments = [], string $token = '')
    {
        $this->key = $key;
        $this->aliases = $aliases;
        $this->arguments = $arguments;
        $this->token = $token;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getToken(): string
    {
        return $this->token;
    }


}
