<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Command\ParseFifaIndex;
use App\Parser\FifaIndexParser;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;


$containerBuilder = new ContainerBuilder();
$containerBuilder->set(FifaIndexParser::class, new FifaIndexParser(new Crawler(), HttpClient::create()));

$application = new Application();

try {
    /** @var FifaIndexParser $parser */
    $parser = $containerBuilder->get(FifaIndexParser::class);
    $application->add(new ParseFifaIndex($parser));
    $application->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
