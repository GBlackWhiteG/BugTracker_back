<?php

namespace Database\Seeders;

use App\Models\Bug;
use App\Services\ElasticsearchService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
           ElasticsearchSeeder::class
        ]);

        $this->indexBugsToElasticsearch();
    }

    private function indexBugsToElasticsearch(): void
    {
        $elasticsearch = app(ElasticsearchService::class);

        Bug::chunk(100, function ($bugs) use ($elasticsearch) {
            foreach ($bugs as $bug) {
                $elasticsearch->index('bugs', $bug->toArray());
            }
        });

    }
}
