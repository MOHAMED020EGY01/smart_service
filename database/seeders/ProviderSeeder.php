<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 providers with locations
        User::factory()
            ->count(20)
            ->provider()
            ->has(Location::factory(), 'location')
            ->create();
    }
}
