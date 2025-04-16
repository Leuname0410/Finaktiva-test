<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLog extends Model
{
    use SoftDeletes;

    protected $table = 'event_logs';

    protected $fillable = [
        'fecha_evento',
        'descripcion',
        'tipo_evento',
        'origen',
    ];

    protected $dates = [
        'fecha_evento',
        'deleted_at',
    ];
}
