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
        $query = $request->get('query');

        $query = "Laravel";

        if (!$query) {
            return response()->json(['error' => 'Введите поисковый запрос'], 400);
        }

        $results = $this->elasticsearch->search('articles', $query);

        return response()->json($results);
    }
}

