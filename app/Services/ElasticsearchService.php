<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;

class ElasticsearchService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();
    }

    public function createIndex(string $index, array $settings = []): array|string
    {
        if ($this->client->indices()->exists(['index' => $index])->asBool()) {
            return "Индекс $index уже существует.";
        }

        return $this->client->indices()->create([
            'index' => $index,
            'body' => $settings
        ])->asArray();
    }

    public function search(string $index, string $query): array
    {
        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^2', 'content']
                    ]
                ]
            ]
        ];

        return $this->client->search($params)->asArray();
    }
}
