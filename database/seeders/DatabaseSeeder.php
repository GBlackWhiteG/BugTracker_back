<?php

namespace Database\Seeders;

use App\Models\Bug;
use App\Models\User;
use App\Services\ElasticsearchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'nickname' => 'admin123',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@manager.com',
            'nickname' => 'managust',
            'password' => Hash::make('123456'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Developer',
            'email' => 'developer@developer.com',
            'nickname' => 'proger',
            'password' => Hash::make('123456'),
            'role' => 'developer',
        ]);

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
