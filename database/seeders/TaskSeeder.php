<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\Task::create([
        'user_id' => 1,   // assuming a user with ID 1 exists
        'title' => 'Learn Laravel',
        'description' => 'Practice Eloquent ORM and migrations',
        'completed' => 1,
    ]);
}
}
