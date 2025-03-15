<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ElasticsearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $host = env('ELASTICSEARCH_HOST');

        Http::delete("$host/bugs");

        Http::put("$host/bugs", [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0
            ],
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text'],
                    'description' => ['type' => 'text'],
                    'steps' => ['type' => 'text'],
                    'criticality' => ['type' => 'keyword'],
                    'status' => ['type' => 'keyword'],
                    'priority' => ['type' => 'keyword'],
                    'responsible_user_id' => ['type' => 'integer'],
                    'user_id' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ]
            ]
        ]);
    }
}
