<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\GrupoSemestre;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CalificacionesEspecialidadExport implements FromArray, WithStyles, WithColumnWidths
{
    protected $idEspecialidad;
    protected $numeroSemestre;
    protected $totalColumnas;

    public function __construct($idEspecialidad, $numeroSemestre)
    {
        $this->idEspecialidad = $idEspecialidad;
        $this->numeroSemestre = $numeroSemestre;
    }

    public function array(): array
    {
        $anioActual = now()->year;

        // Obtener todos los grupos-semestre del semestre especificado
        $gruposSemestre = GrupoSemestre::whereHas('semestre', function ($q) {
            $q->where('numero', $this->numeroSemestre);
        })
            ->with(['grupo', 'semestre'])
            ->get();

        // Obtener todas las clases de la especialidad en este semestre
        $clases = Clase::whereIn('idGrupoSemestre', $gruposSemestre->pluck('id'))
            ->where('anio', $anioActual)
            ->where('idEspecialidad', $this->idEspecialidad)
            ->with('asignatura')
            ->get()
            ->sortBy('asignatura.nombre')
            ->values();

        // Si no hay clases, retornar mensaje
        if ($clases->isEmpty()) {
            return [
                ['No hay clases de especialidad registradas para este semestre']
            ];
        }

        // Obtener nombres únicos de asignaturas
        $asignaturas = $clases->unique('idAsignatura')->values();

        // Obtener todos los alumnos con esta especialidad en cualquier grupo del semestre
        // Obtener todos los alumnos con esta especialidad en cualquier grupo del semestre
        $alumnos = Alumno::whereHas('grupo_semestres', function ($q) use ($gruposSemestre) {
            $q->whereIn('grupo_semestre.id', $gruposSemestre->pluck('id'));
        })
            ->whereHas('especialidads', function ($q) {
                $q->where('especialidad.id', $this->idEspecialidad);
            })
            ->whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('situacion', 'ACTIVO')
            ->with(['persona', 'grupo_semestres.grupo', 'grupo_semestres.semestre'])
            ->get()
            ->sortBy(function ($alumno) {
                // Buscar el grupo-semestre correspondiente al semestre actual
                $grupoSemestre = $alumno->grupo_semestres
                    ->firstWhere('semestre.numero', $this->numeroSemestre);

                $grupoTexto = $grupoSemestre
                    ? ($grupoSemestre->grupo->prefijo . '-' . $grupoSemestre->semestre->numero)
                    : 'ZZZ'; // Si no tiene grupo válido, lo manda al final

                return sprintf(
                    '%s-%s-%s-%s',
                    $grupoTexto,
                    $alumno->persona->apellidoPaterno,
                    $alumno->persona->apellidoMaterno,
                    $alumno->persona->nombre
                );
            })
            ->values();


        // Obtener todas las calificaciones de estos alumnos
        $alumnosIds = $alumnos->pluck('id')->toArray();
        $clasesIds = $clases->pluck('id')->toArray();

        $calificaciones = Calificacion::whereIn('idAlumno', $alumnosIds)
            ->whereIn('idClase', $clasesIds)
            ->get()
            ->groupBy('idAlumno');

        $data = [];

        // ============================================
        // FILA 1: Encabezados principales
        // ============================================
        $encabezadosPrincipales = [
            'N° LISTA',
            'CURP',
            'NOMBRE COMPLETO',
            'GRUPO-SEMESTRE'
        ];

        // Agregar nombres de asignaturas
        foreach ($asignaturas as $asignatura) {
            $encabezadosPrincipales[] = strtoupper($asignatura->asignatura->nombre);
        }

        $encabezadosPrincipales[] = 'NIA';
        $encabezadosPrincipales[] = 'OBSERVACIONES';

        $data[] = $encabezadosPrincipales;

        // ============================================
        // FILA 2: Subencabezados (M1, M2, M3, PROM)
        // ============================================
        $subencabezados = ['', '', '', '']; // N°, CURP, NOMBRE, GRUPO-SEMESTRE

        foreach ($asignaturas as $asignatura) {
            $subencabezados[] = 'M1';
            $subencabezados[] = 'M2';
            $subencabezados[] = 'M3';
            $subencabezados[] = 'PROM';
        }

        $subencabezados[] = ''; // NIA
        $subencabezados[] = ''; // OBSERVACIONES

        $data[] = $subencabezados;

        // Calcular total de columnas
        $this->totalColumnas = count($subencabezados);

        // ============================================
        // FILAS DE DATOS: Alumnos y calificaciones
        // ============================================
        $numeroLista = 1;
        foreach ($alumnos as $alumno) {
            // Obtener el grupo-semestre del alumno para este semestre
            $grupoSemestreAlumno = $alumno->grupo_semestres
                ->firstWhere('semestre.numero', $this->numeroSemestre);

            $grupoSemestreTexto = $grupoSemestreAlumno
                ? $grupoSemestreAlumno->grupo->prefijo . '-' . $grupoSemestreAlumno->semestre->numero
                : 'N/A';

            $fila = [
                $numeroLista++,
                $alumno->persona->curp ?? '',
                trim($alumno->persona->apellidoPaterno . ' ' .
                    $alumno->persona->apellidoMaterno . ' ' .
                    $alumno->persona->nombre),
                $grupoSemestreTexto
            ];

            // Agregar calificaciones por cada asignatura
            foreach ($asignaturas as $asignatura) {
                // Buscar la clase correspondiente para este alumno
                $claseAlumno = $clases->first(function ($clase) use ($asignatura, $grupoSemestreAlumno) {
                    return $clase->idAsignatura == $asignatura->idAsignatura &&
                        $clase->idGrupoSemestre == $grupoSemestreAlumno->id;
                });

                if ($claseAlumno) {
                    $calificacionAlumno = isset($calificaciones[$alumno->id])
                        ? $calificaciones[$alumno->id]->firstWhere('idClase', $claseAlumno->id)
                        : null;

                    if ($calificacionAlumno) {
                        $m1 = $calificacionAlumno->momento1 ?? 0;
                        $m2 = $calificacionAlumno->momento2 ?? 0;
                        $m3 = $calificacionAlumno->momento3 ?? 0;
                        $promedio = ($m1 + $m2 + $m3) / 3;

                        $fila[] = $m1;
                        $fila[] = $m2;
                        $fila[] = $m3;
                        $fila[] = number_format($promedio, 1);
                    } else {
                        $fila[] = 0;
                        $fila[] = 0;
                        $fila[] = 0;
                        $fila[] = 0;
                    }
                } else {
                    $fila[] = 0;
                    $fila[] = 0;
                    $fila[] = 0;
                    $fila[] = 0;
                }
            }

            $fila[] = $alumno->nia;
            $fila[] = ''; // OBSERVACIONES vacío

            $data[] = $fila;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        // ============================================
        // ESTILOS PARA ENCABEZADOS (Fila 1)
        // ============================================
        $sheet->getStyle('A1:' . $ultimaColumna . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // ============================================
        // ESTILOS PARA SUBENCABEZADOS (Fila 2)
        // ============================================
        $sheet->getStyle('A2:' . $ultimaColumna . '2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'B4C7E7']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // ============================================
        // ESTILOS PARA DATOS (Fila 3 en adelante)
        // ============================================
        $sheet->getStyle('A3:' . $ultimaColumna . $ultimaFila)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0']
                ]
            ]
        ]);

        // Alineación a la izquierda para NOMBRE COMPLETO (columna C)
        $sheet->getStyle('C3:C' . $ultimaFila)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // ============================================
        // ALTURA DE FILAS
        // ============================================
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // ============================================
        // MERGE CELLS para asignaturas en fila 1
        // ============================================
        $anioActual = now()->year;
        $gruposSemestre = GrupoSemestre::whereHas('semestre', function ($q) {
            $q->where('numero', $this->numeroSemestre);
        })->get();

        $clases = Clase::whereIn('idGrupoSemestre', $gruposSemestre->pluck('id'))
            ->where('anio', $anioActual)
            ->where('idEspecialidad', $this->idEspecialidad)
            ->with('asignatura')
            ->get();

        $asignaturas = $clases->unique('idAsignatura')->sortBy('asignatura.nombre')->values();

        $colIndex = 5; // Columna E (después de N°, CURP, NOMBRE, GRUPO-SEMESTRE)

        foreach ($asignaturas as $asignatura) {
            $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3);

            $sheet->mergeCells($startCol . '1:' . $endCol . '1');
            $sheet->setCellValue($startCol . '1', strtoupper($asignatura->asignatura->nombre));

            $colIndex += 4;
        }

        // Fusionar columnas básicas
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2'); // GRUPO-SEMESTRE

        // Fusionar NIA y OBSERVACIONES
        $niaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
        $obsCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
        $sheet->mergeCells($niaCol . '1:' . $niaCol . '2');
        $sheet->mergeCells($obsCol . '1:' . $obsCol . '2');
        $sheet->setCellValue($niaCol . '1', 'NIA');
        $sheet->setCellValue($obsCol . '1', 'OBSERVACIONES');

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 10,  // N° LISTA
            'B' => 20,  // CURP
            'C' => 35,  // NOMBRE COMPLETO
            'D' => 15,  // GRUPO-SEMESTRE
        ];

        $anioActual = now()->year;
        $gruposSemestre = GrupoSemestre::whereHas('semestre', function ($q) {
            $q->where('numero', $this->numeroSemestre);
        })->get();

        $clases = Clase::whereIn('idGrupoSemestre', $gruposSemestre->pluck('id'))
            ->where('anio', $anioActual)
            ->where('idEspecialidad', $this->idEspecialidad)
            ->get();

        $asignaturas = $clases->unique('idAsignatura');

        $colIndex = 5; // Empieza en E
        foreach ($asignaturas as $asignatura) {
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 10;
        }

        $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 15;
        $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex)] = 25;

        return $widths;
    }
}
