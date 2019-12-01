<?php

declare(strict_types=1);

namespace App\Command;

use App\Parser\FifaIndexParser;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ParseFifaIndex extends Command
{

    private FifaIndexParser $parser;

    public function __construct(FifaIndexParser $parser)
    {
        $this->parser = $parser;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setName('parse:fifa_index');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $teams = $this->parser->parse();
        $sql = 'INSERT INTO teams (id, name, league, rating) VALUES ';
        $values = [];
        foreach ($teams as $team) {
            $uuid = (Uuid::uuid4())->toString();
            $name = str_replace('\'', '\'\'', $team['name']);
            $league = str_replace('\'', '\'\'', $team['league']);
            $values[] = "('{$uuid}', '{$name}', '{$league}', {$team['rating']})";
        }
        $sql .= implode(',', $values) . ';';
        file_put_contents('teams.sql', $sql);

        return 0;
    }

}
