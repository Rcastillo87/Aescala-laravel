<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Carbon\Carbon;

class Festivos extends Model
{
    use HasFactory;

    protected $table = 'dias_festivos';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'name',
        'comentario'
    ];

    /**
     * Descarga y guarda los festivos de un año específico.
     *
     * @param int $year
     * @return void
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function festivos($year)
    {
        try {
            $existenFestivos = self::whereYear('date', $year)->exists();
            if ($existenFestivos) {
                return;
            }

            $response = Http::get("https://api-colombia.com/api/v1/holiday/year/{$year}");

            // Verifica que la respuesta fue exitosa
            if ($response->successful()) {
                $festivos = $response->json();

                foreach ($festivos as $festivo) {
                    // Guarda cada festivo, evitando duplicados si quieres
                    $fecha = Carbon::parse($festivo['date']);
                    self::updateOrCreate(
                        ['date' => $fecha], // condición de búsqueda
                        ['name' => $festivo['name']]  // datos a actualizar o crear
                    );
                }
            } else {
                throw new \Exception('No se pudo obtener los datos de festivos.');
            }
        } catch (RequestException $e) {
            // Puedes manejar errores de conexión aquí
            throw new \Exception('Error al conectar con el servicio de festivos: ' . $e->getMessage());
        }
    }

    public function calcularFechaFin($fechaInicio, $dias)
    {
        $fecha = Carbon::parse($fechaInicio);
        $diasTrabajados = 0;

        // Cargar festivos de la base de datos
        $festivos = self::pluck('date')->map(function ($date) {
            return Carbon::parse($date)->toDateString();
        })->toArray();

        while (true) {
            $diaSemana = $fecha->dayOfWeek; // 0=Domingo, 6=Sábado
            $esFestivo = in_array($fecha->toDateString(), $festivos);

            if ($diaSemana == Carbon::SUNDAY || $esFestivo) {
                // Domingo o festivo: no se cuenta
            } elseif ($diaSemana == Carbon::SATURDAY) {
                $diasTrabajados = $diasTrabajados + 0.5;
            } else {
                $diasTrabajados++;
            }

            if ($diasTrabajados >= $dias) {
                break;
            }

            $fecha->addDay();
        }

        return $fecha->toDateString();
    }

    public function contarDiasHabiles($fechaInicio, $fechaFin, $festivos, $id = null)
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $diasHabiles = 0;
        $arrNolab = [];
        if ($id) {
            $arrNolab = DiasNoLaboralos::where('id_proyecto', $id)->pluck('dia')->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })->toArray();
        }

        if (is_null($festivos)) {
            $festivos = self::pluck('date')->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })->toArray();
        }

        while ($inicio->lte($fin)) {
            $diaSemana = $inicio->dayOfWeek;
            $esFestivo = in_array($inicio->toDateString(), (array) $festivos);
            $esDiaNolab = in_array($inicio->toDateString(), (array) $arrNolab);

            if ($diaSemana == Carbon::SUNDAY || $esFestivo || $esDiaNolab) {
                // No cuenta
            } elseif ($diaSemana == Carbon::SATURDAY) {
                $diasHabiles = $diasHabiles + 0.5;
            } else {
                $diasHabiles++;
            }
            $inicio->addDay();
        }

        return $diasHabiles;
    }
}
