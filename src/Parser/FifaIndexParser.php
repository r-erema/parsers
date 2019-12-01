<?php

declare(strict_types=1);

namespace App\Parser;

use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FifaIndexParser implements ParserInterface
{

    private Crawler $crawler;
    private HttpClientInterface $httpClient;

    public function __construct(Crawler $crawler, HttpClientInterface $httpClient)
    {
        $this->crawler = $crawler;
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    public function parse(): array
    {
        $i = 1;
        $result = [];
        while (
            ($response = $this->httpClient->request('GET', "https://www.fifaindex.com/ru/teams/{$i}/"))
            && $response->getStatusCode() === 200
        ) {
            $html = $response->getContent();
            $this->crawler->clear();
            $this->crawler->add($html);

            $this->crawler->filter('table.table.table-striped.table-teams > tbody > tr')->each(static function (Crawler $node) use (&$result) {
                $nodeName = $node->filter('[data-title="Имя"]');
                $nodeLeague = $node->filter('[data-title="Лига"]');
                $nodeRating = $node->filter('[data-title="Рейтинг команды"]');
                if ($nodeName->count() && $nodeLeague->count() && $nodeRating->count()) {
                    $rating = $nodeRating->filter('i.fas.fa-star')->count();
                    $rating += 0.5 * $nodeRating->filter('i.fa-star-half-alt')->count();
                    $result[] = [
                        'name' => $nodeName->text(),
                        'league' => $nodeLeague->text(),
                        'rating' => $rating
                    ];
                }

            });

            usleep(random_int(100000, 999999));
            $i++;
        }
        return $result;
    }

}
