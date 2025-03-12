<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', [SearchController::class, 'search']);

Route::get('/test', function () {
    app(App\Services\ElasticsearchService::class)->createIndex('articles', [
        'settings' => [
            'number_of_shards' => 1,
            'number_of_replicas' => 1
        ],
        'mappings' => [
            'properties' => [
                'title' => ['type' => 'text'],
                'content' => ['type' => 'text']
            ]
        ]
    ]);
});
