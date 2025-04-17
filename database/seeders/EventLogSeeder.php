<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventLog;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class EventLogSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            EventLog::create([
                'fecha_evento' => $faker->dateTimeBetween('-1 year', 'now'),
                'descripcion' => $faker->sentence,
                'tipo_evento' => $faker->randomElement(['api', 'formulario']),
                'origen' => $faker->ipv4,
            ]);
        }
    }
}
