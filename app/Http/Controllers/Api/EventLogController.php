<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventLogController extends Controller
{
    public function index()
    {
        try {
            $logs = EventLog::orderBy('id', 'desc')->paginate(30);
            return response()->json($logs);
        } catch (\Throwable $e) {
            Log::error('Error al consultar logs: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los eventos'], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_evento' => 'required|date',
            'descripcion' => 'required|string',
            'tipo_evento' => 'required|in:api,formulario',
            'origen' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 422);
        }

        try {
            $log = EventLog::create([
                'fecha_evento' => Carbon::parse($request->fecha_evento),
                'descripcion' => $request->descripcion,
                'tipo_evento' => $request->tipo_evento,
                'origen' => $request->origen ?? $request->ip(),
            ]);

            return response()->json(['mensaje' => 'Evento registrado', 'data' => $log], 201);
        } catch (\Throwable $e) {
            Log::error('Error al registrar evento: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo registrar el evento'], 500);
        }
    }
}
