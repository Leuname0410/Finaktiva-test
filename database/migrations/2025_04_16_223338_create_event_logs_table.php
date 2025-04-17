<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLogsTable extends Migration
{
    public function up()
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_evento');
            $table->text('descripcion');
            $table->enum('tipo_evento', ['api', 'formulario']);
            $table->string('origen')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo_evento');
            $table->index('fecha_evento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_logs');
    }
}
