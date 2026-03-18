<?php

namespace App\Http\Controllers;

use App\Models\Festivos;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        $year  = now()->year;
        $title = 'Calendario ' . $year;

        $modelFestivos = new Festivos();
        try {
            $modelFestivos->festivos($year);
            $modelFestivos->festivos($year + 1);
        } catch (\Exception $e) {
            // Continúa con los festivos que ya estén en BD
        }

        $registros = Festivos::whereYear('date', $year)
            ->get()
            ->keyBy(fn($f) => Carbon::parse($f->date)->toDateString());

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $meses[$m] = $this->resumenMes($year, $m, $registros);
        }

        return view('calendario.index', compact('title', 'year', 'meses'));
    }

    // ─── AJAX: resumen de todos los meses de un año (para navegación) ──
    public function resumenAnio(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        // Intentar descargar festivos si no existen aún
        $modelFestivos = new Festivos();
        try {
            $modelFestivos->festivos($year);
        } catch (\Exception $e) {
            // Silencioso
        }

        $registros = Festivos::whereYear('date', $year)
            ->get()
            ->keyBy(fn($f) => Carbon::parse($f->date)->toDateString());

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $meses[$m] = $this->resumenMes($year, $m, $registros);
        }

        return response()->json($meses);
    }

    // ─── AJAX: detalle de un mes ───────────────────────────────────────
    public function mesDatos(Request $request)
    {
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        $registros = Festivos::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(fn($f) => Carbon::parse($f->date)->toDateString());

        $dias  = [];
        $total = Carbon::create($year, $month)->daysInMonth;

        for ($d = 1; $d <= $total; $d++) {
            $fecha   = Carbon::create($year, $month, $d);
            $dateStr = $fecha->toDateString();
            $dow     = $fecha->dayOfWeek;
            $reg     = $registros->get($dateStr);
            $tipo    = $this->tipoDia($dow, $reg);

            $dias[] = [
                'date'        => $dateStr,
                'day'         => $d,
                'dayName'     => $this->nombreDia($dow),
                'dayOfWeek'   => $dow,
                'tipo'        => $tipo,
                'festivoName' => $reg ? $reg->name : null,
                'festivoId'   => $reg ? $reg->id   : null,
                // isPast: días ANTERIORES a hoy (hoy no se incluye como pasado)
                'isPast'      => $fecha->lt(Carbon::today()),
                'isToday'     => $fecha->isToday(),
                // editable: hoy en adelante, no domingos, no festivos oficiales
                'editable'    => $this->esEditable($dow, $reg, $fecha),
                'vacacion'    => $reg && strtolower($reg->name) === 'vacaciones',
            ];
        }

        $primerDia = Carbon::create($year, $month, 1)->dayOfWeek;

        return response()->json([
            'year'      => $year,
            'month'     => $month,
            'monthName' => $this->nombreMes($month),
            'primerDia' => $primerDia,
            'dias'      => $dias,
        ]);
    }

    // ─── AJAX: marcar día como vacación ───────────────────────────────
    public function marcarVacacion(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $fecha = Carbon::parse($request->date);

        if ($fecha->lt(Carbon::today())) {
            return response()->json(['error' => 'No se pueden modificar días pasados.'], 422);
        }

        $dow = $fecha->dayOfWeek;
        if ($dow === Carbon::SUNDAY) {
            return response()->json(['error' => 'Los domingos no se pueden marcar como vacación.'], 422);
        }

        $reg = Festivos::where('date', $fecha->toDateString())->first();
        if ($reg && strtolower($reg->name) !== 'vacaciones') {
            return response()->json(['error' => 'Este día ya es un festivo oficial.'], 422);
        }
        if ($reg) {
            return response()->json(['message' => 'Ya es vacación.']);
        }

        Festivos::create([
            'date' => $fecha->toDateString(),
            'name' => 'Vacaciones',
        ]);

        return response()->json(['message' => 'Día marcado como vacación.']);
    }

    // ─── AJAX: quitar vacación ─────────────────────────────────────────
    public function quitarVacacion(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $fecha = Carbon::parse($request->date);

        if ($fecha->lt(Carbon::today())) {
            return response()->json(['error' => 'No se pueden modificar días pasados.'], 422);
        }

        $eliminados = Festivos::where('date', $fecha->toDateString())
            ->where('name', 'Vacaciones')
            ->delete();

        if ($eliminados === 0) {
            return response()->json(['error' => 'No se encontró una vacación en esa fecha.'], 422);
        }

        return response()->json(['message' => 'Vacación eliminada correctamente.']);
    }

    // ─── Helpers privados ─────────────────────────────────────────────

    private function resumenMes(int $year, int $month, $registrosAnio): array
    {
        $total      = Carbon::create($year, $month)->daysInMonth;
        $trabajados = 0;
        $sabados    = 0;
        $domingos   = 0;
        $festivos   = 0;   // solo festivos oficiales (NO vacaciones)
        $vacaciones = 0;

        $regMes = $registrosAnio->filter(
            fn($r) => Carbon::parse($r->date)->month === $month
        )->keyBy(fn($r) => Carbon::parse($r->date)->toDateString());

        for ($d = 1; $d <= $total; $d++) {
            $fecha   = Carbon::create($year, $month, $d);
            $dateStr = $fecha->toDateString();
            $dow     = $fecha->dayOfWeek;
            $reg     = $regMes->get($dateStr);

            $tipo = $this->tipoDia($dow, $reg);

            match ($tipo) {
                'trabajado' => $trabajados++,
                'sabado'    => $sabados++,
                'domingo'   => $domingos++,
                'festivo'   => $festivos++,
                'vacacion'  => $vacaciones++,
                default     => null,
            };
        }

        return compact('trabajados', 'sabados', 'domingos', 'festivos', 'vacaciones');
    }

    private function tipoDia(int $dow, $reg): string
    {
        if ($reg) {
            // Vacaciones tienen su propio tipo, distinto de festivo
            if (strtolower($reg->name) === 'vacaciones') return 'vacacion';
            return 'festivo';
        }
        if ($dow === Carbon::SUNDAY)   return 'domingo';
        if ($dow === Carbon::SATURDAY) return 'sabado';
        return 'trabajado';
    }

    private function esEditable(int $dow, $reg, Carbon $fecha): bool
    {
        // Días estrictamente pasados (antes de hoy) → no editables
        if ($fecha->lt(Carbon::today())) return false;
        // Domingos → no editables
        if ($dow === Carbon::SUNDAY) return false;
        // Festivos oficiales (no vacaciones) → no editables
        if ($reg && strtolower($reg->name) !== 'vacaciones') return false;
        return true;
    }

    private function nombreDia(int $dow): string
    {
        return ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$dow];
    }

    private function nombreMes(int $m): string
    {
        return [
            1  => 'Enero',      2  => 'Febrero',   3  => 'Marzo',
            4  => 'Abril',      5  => 'Mayo',       6  => 'Junio',
            7  => 'Julio',      8  => 'Agosto',     9  => 'Septiembre',
            10 => 'Octubre',    11 => 'Noviembre',  12 => 'Diciembre',
        ][$m];
    }
}
