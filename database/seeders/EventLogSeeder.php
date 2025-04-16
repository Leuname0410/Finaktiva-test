<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventLog;
use Illuminate\Support\Carbon;

class EventLogSeeder extends Seeder
{
    public function run()
    {
        EventLog::create([
            'fecha_evento' => Carbon::now(),
            'descripcion' => 'Evento generado desde la API de prueba',
            'tipo_evento' => 'api',
            'origen' => '127.0.0.1',
        ]);

        EventLog::create([
            'fecha_evento' => Carbon::now()->subDays(1),
            'descripcion' => 'Evento manual vía formulario',
            'tipo_evento' => 'formulario',
            'origen' => '192.168.1.1',
        ]);
    }
}
