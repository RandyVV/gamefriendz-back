<?php

namespace App\Message;

class RunImportGamesCommand
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

}