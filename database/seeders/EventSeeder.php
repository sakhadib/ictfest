<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            ['code' => '01', 'name' => 'Inter University Programming Contest (IUPC)', 'min_team_size' => 3, 'max_team_size' => 3, 'rulebook_link' => null, 'is_live' => false],
            ['code' => '02', 'name' => 'Agentic AI Hackathon', 'min_team_size' => 1, 'max_team_size' => 3, 'rulebook_link' => null, 'is_live' => false],
            ['code' => '03', 'name' => 'Datathon', 'min_team_size' => 1, 'max_team_size' => 4, 'rulebook_link' => null, 'is_live' => false],
            ['code' => '04', 'name' => 'Gamejam', 'min_team_size' => 1, 'max_team_size' => 3, 'rulebook_link' => null, 'is_live' => false],
            ['code' => '05', 'name' => 'FIFA', 'min_team_size' => 1, 'max_team_size' => 1, 'rulebook_link' => null, 'is_live' => false],
            ['code' => '06', 'name' => 'Valorant', 'min_team_size' => 5, 'max_team_size' => 7, 'rulebook_link' => null, 'is_live' => false],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['code' => $event['code']],
                $event,
            );
        }
    }
}
