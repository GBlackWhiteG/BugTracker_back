<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use Elastic\Elasticsearch\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query', '');

        $filters = [
            'criticality' => $request->query('criticality'),
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'created_at' => $request->query('created_at'),
        ];

        $results = $this->elasticsearch->search('bugs', $query, $filters);

        return response()->json(['data' => $results]);
    }
}

