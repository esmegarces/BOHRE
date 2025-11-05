<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\GrupoSemestre;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use TCPDF;

class BoletaController extends Controller
{
    public function obtenerSemestresAlumno($idAlumno)
    {
        try {
            $alumno = Alumno::with([
                'persona',
                'grupo_semestres.semestre',
                'grupo_semestres.grupo',
                'especialidads'
            ])->findOrFail($idAlumno);

            $semestres = $alumno->grupo_semestres->map(function ($gs) use ($alumno) {
                $totalCalificaciones = Calificacion::whereHas('clase', function ($q) use ($gs) {
                    $q->where('idGrupoSemestre', $gs->id);
                })
                    ->where('idAlumno', $alumno->id)
                    ->count();

                return [
                    'idGrupoSemestre' => $gs->id,
                    'semestre' => $gs->semestre->numero,
                    'grupo' => $gs->grupo->prefijo,
                    'grupoSemestre' => $gs->grupo->prefijo . '-' . $gs->semestre->numero,
                    'periodo' => sprintf(
                        '%02d-%02d / %02d-%02d',
                        $gs->semestre->diaInicio,
                        $gs->semestre->mesInicio,
                        $gs->semestre->diaFin,
                        $gs->semestre->mesFin
                    ),
                    'totalCalificaciones' => $totalCalificaciones,
                    'tieneCalificaciones' => $totalCalificaciones > 0
                ];
            })->sortBy('semestre')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'alumno' => [
                        'id' => $alumno->id,
                        'nia' => $alumno->nia,
                        'nombre' => $alumno->persona->nombre . ' ' .
                            $alumno->persona->apellidoPaterno . ' ' .
                            $alumno->persona->apellidoMaterno,
                        'curp' => $alumno->persona->curp,
                        'situacion' => $alumno->situacion,
                        'especialidad' => $alumno->especialidads->first()?->nombre
                    ],
                    'semestres' => $semestres
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener semestres: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarBoleta($idAlumno, $idGrupoSemestre)
    {
        try {
            $anioActual = Carbon::now()->year;

            $alumno = Alumno::with(['persona', 'especialidads'])->findOrFail($idAlumno);
            $grupoSemestre = GrupoSemestre::with(['grupo', 'semestre'])
                ->findOrFail($idGrupoSemestre);

            $calificaciones = Calificacion::where('idAlumno', $idAlumno)
                ->whereHas('clase', function ($q) use ($idGrupoSemestre, $anioActual) {
                    $q->where('idGrupoSemestre', $idGrupoSemestre)
                        ->where('anio', $anioActual);
                })
                ->with(['clase.asignatura', 'clase.especialidad'])
                ->get();

            $materiasComunes = $calificaciones->filter(fn($cal) => $cal->clase->idEspecialidad === null)
                ->sortBy('clase.asignatura.nombre');

            $materiasEspecialidad = $calificaciones->filter(fn($cal) => $cal->clase->idEspecialidad !== null)
                ->sortBy('clase.asignatura.nombre');

            $promedioGeneral = $calificaciones->avg(function ($cal) {
                return ($cal->momento1 + $cal->momento2 + $cal->momento3) / 3;
            });

            // Crear PDF extendido con header y footer personalizados
            $pdf = new BoletaPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

            // Pasar datos al PDF para usar en header/footer
            $pdf->setDatosAlumno($alumno);
            $pdf->setAnio($anioActual);

            $pdf->SetCreator('SICEP');
            $pdf->SetAuthor('Secretaría de Educación - Gobierno de Puebla');
            $pdf->SetTitle('Boleta de Calificaciones');
            $pdf->SetSubject('Consulta de Calificaciones');

            // Configurar márgenes (ajustar para dar espacio al header y footer)
            $pdf->SetMargins(10, 45, 10); // Top margin más grande para el header
            $pdf->SetAutoPageBreak(true, 55); // Bottom margin para el footer

            $pdf->AddPage();

            // Construir contenido (sin header ni footer en el HTML)
            $html = $this->construirHTMLBoleta(
                $alumno,
                $grupoSemestre,
                $materiasComunes,
                $materiasEspecialidad,
                $promedioGeneral,
                $anioActual
            );

            $pdf->writeHTML($html, true, false, true, false, '');

            $nombreArchivo = "boleta_{$alumno->nia}_sem{$grupoSemestre->semestre->numero}.pdf";

            return response($pdf->Output($nombreArchivo, 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar boleta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function construirHTMLBoleta($alumno, $grupoSemestre, $materiasComunes, $materiasEspecialidad, $promedioGeneral, $anio)
    {
        $especialidad = $alumno->especialidads->first();
        $cicloEscolar = $anio . ' - ' . ($anio + 1);

        $html = '
    <style>
        * { font-family: Arial, Helvetica, sans-serif; }
        body { margin: 0; padding: 0; }
        table { border-collapse: collapse; width: 100%; }

        .section-title {
            font-weight: bold;
            font-size: 10pt;
            margin: 8px 0 5px 0;
            text-align: center;
            padding: 5px;
        }

        .subsection-title {
            font-weight: bold;
            font-size: 9pt;
            margin: 12px 0 3px 0;
            color: #333;
        }

        .info-table {
            margin-top: 3px;
            margin-bottom: 8px;
            width: 100%;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 8.5pt;
            line-height: 1.4;
        }
        .info-label {
            font-weight: bold;
            width: 22%;
            color: #222;
        }
        .info-value {
            background-color: #fff;
        }

        .cal-table {
            margin-top: 8px;
            border: 1px solid #000;
        }
        .cal-table th {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 9pt;
            background-color: #d0d0d0;
            font-weight: bold;
            color: #000;
        }
        .cal-table td {
            border: 1px solid #666;
            padding: 4px;
            text-align: center;
            font-size: 8.5pt;
        }
        .materia-nombre {
            text-align: left !important;
            padding-left: 8px !important;
            font-size: 8pt;
            font-weight: normal;
            width: 60%;
        }
        .materia-calificaciones {
            width: 10%;
        }
        .especialidad-header {
            background-color: #c0c0c0 !important;
            font-weight: bold;
            text-align: center;
            padding: 5px !important;
            font-size: 9pt;
        }
        .promedio-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        .promedio-label {
            text-align: right !important;
            padding-right: 12px !important;
            font-size: 9pt;
        }
    </style>

    <!-- DATOS DE LA ESCUELA -->
    <div class="subsection-title">Datos de la Escuela</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Ciclo Escolar:</td>
            <td class="info-value">' . $cicloEscolar . '</td>
            <td class="info-label">Nombre:</td>
            <td class="info-value">HÉROES DE LA REVOLUCIÓN</td>
        </tr>
        <tr>
            <td class="info-label">Centro de Trabajo:</td>
            <td class="info-value">21EBH0196Z</td>
            <td class="info-label">Nivel Educativo:</td>
            <td class="info-value">BACHILLERATO</td>
        </tr>
        <tr>
            <td class="info-label">Turno:</td>
            <td class="info-value">MATUTINO</td>
            <td class="info-label">Municipio:</td>
            <td class="info-value">TETELES DE ÁVILA CASTILLO</td>
        </tr>
        <tr>
            <td class="info-label">Domicilio:</td>
            <td class="info-value" colspan="3">BLVD VALSEQUILLO EXTERIOR EXT. 29 INT. TETELES DE ÁVILA CASTILLO</td>
        </tr>
        <tr>
            <td class="info-label">Localidad:</td>
            <td class="info-value">TETELES DE ÁVILA CASTILLO</td>
            <td class="info-label">Teléfono:</td>
            <td class="info-value">-</td>
        </tr>
        <tr>
            <td class="info-label">Entidad:</td>
            <td class="info-value" colspan="3">PUEBLA</td>
        </tr>
    </table>

    <br/>
    <!-- DATOS DEL ALUMNO -->
    <div class="subsection-title">Datos del Alumno</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nombre:</td>
            <td class="info-value">' . strtoupper($alumno->persona->nombre) . '</td>
            <td class="info-label">Segundo Apellido:</td>
            <td class="info-value">' . strtoupper($alumno->persona->apellidoMaterno) . '</td>
        </tr>
        <tr>
            <td class="info-label">Primer Apellido:</td>
            <td class="info-value">' . strtoupper($alumno->persona->apellidoPaterno) . '</td>
            <td class="info-label">Situación:</td>
            <td class="info-value">' . $alumno->situacion . '</td>
        </tr>
        <tr>
            <td class="info-label">CURP:</td>
            <td class="info-value">' . $alumno->persona->curp . '</td>
            <td class="info-label">Especialidad:</td>
            <td class="info-value">' . ($especialidad ? strtoupper($especialidad->nombre) : 'N/A') . '</td>
        </tr>
        <tr>
            <td class="info-label">NIA:</td>
            <td class="info-value" colspan="3">' . $alumno->nia . '</td>
        </tr>
    </table>

    <!-- CALIFICACIONES -->
    <div class="section-title">Calificaciones</div>
    <table class="cal-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 60%">Semestre: ' . $grupoSemestre->semestre->numero . '</th>
                <th colspan="3" style="width: 30%;">Momento</th>
                <th rowspan="2" style="width: 10%;">Promedio</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($materiasComunes as $cal) {
            $promedio = number_format(($cal->momento1 + $cal->momento2 + $cal->momento3) / 3, 1);
            $html .= '
            <tr>
                <td class="materia-nombre">' . strtoupper($cal->clase->asignatura->nombre) . '</td>
                <td class="materia-calificaciones">' . number_format($cal->momento1, 0) . '</td>
                <td class="materia-calificaciones">' . number_format($cal->momento2, 0) . '</td>
                <td class="materia-calificaciones">' . number_format($cal->momento3, 0) . '</td>
                <td class="materia-calificaciones"><strong>' . $promedio . '</strong></td>
            </tr>';
        }

        if ($materiasEspecialidad->count() > 0) {
            $html .= '
            <tr>
                <td colspan="5" class="especialidad-header">MATERIAS DE ESPECIALIDAD</td>
            </tr>';
            foreach ($materiasEspecialidad as $cal) {
                $promedio = number_format(($cal->momento1 + $cal->momento2 + $cal->momento3) / 3, 1);
                $html .= '
                <tr>
                    <td class="materia-nombre">' . strtoupper($cal->clase->asignatura->nombre) . '</td>
                    <td class="materia-calificaciones">' . number_format($cal->momento1, 0) . '</td>
                    <td class="materia-calificaciones">' . number_format($cal->momento2, 0) . '</td>
                    <td class="materia-calificaciones">' . number_format($cal->momento3, 0) . '</td>
                    <td class="materia-calificaciones"><strong>' . $promedio . '</strong></td>
                </tr>';
            }
        }

        $html .= '
            <tr class="promedio-row">
                <td colspan="4" class="promedio-label">Promedio Anual</td>
                <td><strong>' . number_format($promedioGeneral, 1) . '</strong></td>
            </tr>
        </tbody>
    </table>';

        return $html;
    }
}

// Clase personalizada que extiende TCPDF para agregar header y footer
class BoletaPDF extends TCPDF
{
    private $alumno;
    private $anio;

    public function setDatosAlumno($alumno)
    {
        $this->alumno = $alumno;
    }

    public function setAnio($anio)
    {
        $this->anio = $anio;
    }

    // Header se ejecuta automáticamente en cada página
    public function Header()
    {
        // Logos
        $logoLeft = public_path('images/logo_left_top.png');
        $logoRight = public_path('images/logo_right_top.png');

        if (file_exists($logoLeft)) {
            $this->Image($logoLeft, 15, 10, 20);
        }

        if (file_exists($logoRight)) {
            $this->Image($logoRight, 165, 10, 30);
        }

        // Título centrado
        $this->SetY(12);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 5, 'CONSULTA DE CALIFICACIONES', 0, 1, 'C');

        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 4, 'Secretaría de Educación - Gobierno de Puebla', 0, 1, 'C');
        $this->Cell(0, 4, 'SICEP - Sistema de Control Escolar del Estado de Puebla', 0, 1, 'C');

        // Línea separadora
        $this->Ln(2);
        $this->Line(10, $this->GetY(), 205, $this->GetY());
    }

    // Footer se ejecuta automáticamente en cada página
    public function Footer()
    {
        $this->SetY(-50);

        // Firmas
        $this->SetFont('helvetica', '', 8);

        // Columna 1: Directora
        $this->SetXY(20, -48);
        $this->Cell(50, 4, '________________________________', 0, 0, 'C');
        $this->SetXY(20, -44);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(50, 4, 'DIRECTORA', 0, 0, 'C');
        $this->SetXY(20, -40);
        $this->SetFont('helvetica', '', 7);
        $this->Cell(50, 4, 'LIC. MARÍA DEL CONSUELO PARRA CASTILLO', 0, 0, 'C');

        // Columna 2: Sello (círculo)
        $this->Circle(107.5, -38, 12, 0, 360, 'D');

        // Columna 3: Tutor
        $this->SetXY(145, -48);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(50, 4, '________________________________', 0, 0, 'C');
        $this->SetXY(145, -44);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(50, 4, 'TUTOR DE GRUPO', 0, 0, 'C');
        $this->SetXY(145, -40);
        $this->SetFont('helvetica', '', 7);
        $this->Cell(50, 4, 'LIC. JUANA MARCELO LOAIZA', 0, 0, 'C');

        // Nota final
        $this->SetY(-25);
        $this->SetFont('helvetica', '', 7.5);
        $fechaActual = Carbon::now()->format('d/m/Y');
        $this->Cell(0, 4, 'Fecha de Consulta: ' . $fechaActual, 0, 1, 'C');
        $this->Cell(0, 4, 'Esta consulta es de carácter informativo. No sustituye documentos oficiales.', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 7.5);
        $this->Cell(0, 4, 'BLVD VALSEQUILLO EXTERIOR, TETELES DE ÁVILA CASTILLO, Pue.', 0, 0, 'C');
    }
}
