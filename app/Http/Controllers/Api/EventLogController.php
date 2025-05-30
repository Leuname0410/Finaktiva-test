<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventLogRequest;

use Illuminate\Http\Request;
use App\Models\EventLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventLogController extends Controller
{
    public function indexDefault()
    {
        $logs = EventLog::orderBy('id', 'desc')->take(30)->get();
        return response()->json($logs);
    }

    public function index(Request $request)
    {
        $tipoEvento = $request->input('tipo_evento', 'api');
        $fechaInicio = $request->input('fecha_inicio', now()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        $query = EventLog::query()->orderByDesc('id');

        if (!in_array(strtolower($tipoEvento), ['all'])) {
            $query->where('tipo_evento', $tipoEvento);
        }

        if ($fechaInicio) {
            $query->whereDate('fecha_evento', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('fecha_evento', '<=', $fechaFin);
        }

        $logs = $query->paginate(30);

        return response()->json([
            'total' => $logs->total(),
            'per_page' => $logs->perPage(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'data' => $logs->items()
        ]);
    }


    public function store(StoreEventLogRequest $request)
    {

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

    public function destroy($id)
    {
        try {

            $validator = Validator::make(['id' => $id], [
                'id' => 'required|numeric|integer|min:1|max:18446744073709551615'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'ID de evento inválido'], 400);
            }

            $log = EventLog::findOrFail($id);
            $log->delete();

            return response()->json(['mensaje' => 'Evento eliminado correctamente'], 200);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar evento: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo eliminar el evento'], 500);
        }
    }

}
