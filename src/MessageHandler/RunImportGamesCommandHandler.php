<?php

namespace App\MessageHandler;

use App\Command\ImportGamesCommand;
use App\Message\RunImportGamesCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RunImportGamesCommandHandler
{
    private $command;

    public function __construct(ImportGamesCommand $command)
    {
        $this->command = $command;
    }

    public function __invoke(RunImportGamesCommand $config)
    {
        // on récupère les options depuis la configuration de la tâche
        $options = $config->getOptions();

        // on crée l'objet pour passer les options à la commande
        $input = new ArrayInput([
            '--limit' => $options['limit'],
            '--offset' => $options['offset'],
            '--max' => $options['max']
        ]);

        // on crée l'objet qui permettra à la commande d'écrire ce qu'elle veut nous dire
        $output = new BufferedOutput();

        // on lance la commande
        $this->command->run($input, $output);
    }
}