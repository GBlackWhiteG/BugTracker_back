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

    public function index(string $index, array $data): void
    {
        $params = [
            'index' => $index,
            'id' => $data['id'],
            'body' => $data
        ];

        $this->client->index($params);
    }

    public function search(string $index, string $query, array $filters = []): array
    {
        $mustQuery = $query ? [
            'bool' => [
                'should' => [
                    [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^2', 'description'],
                        ]
                    ],
                    [
                        'wildcard' => [
                            'title' => [
                                'value' => "*$query*",
                                'boost' => 2.0
                            ]
                        ]
                    ],
                    [
                        'wildcard' => [
                            'description' => [
                                'value' => "*$query*"
                            ]
                        ]
                    ]
                ],
                'minimum_should_match' => 1
            ]
        ] : [
            'match_all' => new \stdClass()
        ];

        $filterConditions = [];

        if (!empty($filters['criticality'])) {
            $filterConditions[] = ['term' => ['criticality' => $filters['criticality']]];
        }
        if (!empty($filters['status'])) {
            $filterConditions[] = ['term' => ['status' => $filters['status']]];
        }
        if (!empty($filters['priority'])) {
            $filterConditions[] = ['term' => ['priority' => $filters['priority']]];
        }

        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $mustQuery,
                        'filter' => $filterConditions
                    ]
                ],
                'sort' => [
                    'created_at' => ['order' => $filters['created_at'] ?? 'desc']
                ]
            ]
        ];

        return $this->client->search($params)->asArray();
    }
}
