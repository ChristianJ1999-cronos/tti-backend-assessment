<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PatientSeeder;
use Database\Seeders\InstrumentSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\AnswerSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        $this->call([
            PatientSeeder::class,
            InstrumentSeeder::class,
            QuestionSeeder::class,
            SubmissionSeeder::class,
            AnswerSeeder::class
        ]);

    }
}
