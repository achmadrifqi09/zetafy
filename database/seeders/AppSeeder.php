<?php

namespace Database\Seeders;

use App\Models\App;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App::create([
            'name' => 'App 1',
            'app_key' => Str::random(255)
        ]);
    }
}
