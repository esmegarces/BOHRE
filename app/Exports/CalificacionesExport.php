<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\VistaClasesGrupoSemestre;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CalificacionesExport implements FromArray, WithStyles, WithColumnWidths
{
    protected $idGrupoSemestre;
    protected $totalColumnas;

    public function __construct($idGrupoSemestre)
    {
        $this->idGrupoSemestre = $idGrupoSemestre;
    }

    public function array(): array
    {
        $anioActual = now()->year;

        // Obtener las clases (materias comunes)
        $clases = VistaClasesGrupoSemestre::where('idGrupoSemestre', $this->idGrupoSemestre)
            ->where('anio', $anioActual)
            ->whereNull('idEspecialidad') // Solo COMUN
            ->orderBy('nombreAsignatura', 'asc')
            ->get();

        // Obtener alumnos del grupo ordenados
        $alumnos = Alumno::whereHas('grupo_semestres', function ($q) {
            $q->where('grupo_semestre.id', $this->idGrupoSemestre);
        })
            ->whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('situacion', 'ACTIVO')
            ->with('persona')
            ->get()
            ->sortBy([
                ['persona.apellidoPaterno', 'asc'],
                ['persona.apellidoMaterno', 'asc'],
                ['persona.nombre', 'asc']
            ])
            ->values();

        // Obtener todas las calificaciones del año actual para estos alumnos
        $alumnosIds = $alumnos->pluck('id')->toArray();
        $calificaciones = Calificacion::whereIn('idAlumno', $alumnosIds)
            ->whereHas('clase', function ($q) use ($anioActual) {
                $q->where('idGrupoSemestre', $this->idGrupoSemestre)
                    ->where('anio', $anioActual);
            })
            ->get()
            ->keyBy('idClase')
            ->groupBy('idAlumno');

        $data = [];

        // ============================================
        // FILA 1: Encabezados principales
        // ============================================
        $encabezadosPrincipales = [
            'N° LISTA',
            'CURP',
            'NOMBRE COMPLETO'
        ];

        // Agregar nombres de asignaturas
        foreach ($clases as $clase) {
            $encabezadosPrincipales[] = strtoupper($clase->nombreAsignatura);
        }

        $encabezadosPrincipales[] = 'NIA';
        $encabezadosPrincipales[] = 'OBSERVACIONES';

        $data[] = $encabezadosPrincipales;

        // ============================================
        // FILA 2: Subencabezados (M1, M2, M3, PROM)
        // ============================================
        $subencabezados = ['', '', '']; // Columnas de N°, CURP, NOMBRE

        foreach ($clases as $clase) {
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
            $fila = [
                $numeroLista++,
                $alumno->persona->curp ?? '',
                trim($alumno->persona->apellidoPaterno . ' ' .
                    $alumno->persona->apellidoMaterno . ' ' .
                    $alumno->persona->nombre)
            ];

            // Agregar calificaciones por cada clase
            foreach ($clases as $clase) {
                $calificacionAlumno = isset($calificaciones[$alumno->id])
                    ? $calificaciones[$alumno->id]->firstWhere('idClase', $clase->idClase)
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
        // Las primeras 3 columnas NO se fusionan (N°, CURP, NOMBRE)
        // Empezamos desde la columna D (índice 1 en stringFromColumnIndex, porque empieza en 1)
        $clases = VistaClasesGrupoSemestre::where('idGrupoSemestre', $this->idGrupoSemestre)
            ->where('anio', now()->year)
            ->whereNull('idEspecialidad')
            ->orderBy('nombreAsignatura', 'asc')
            ->get();

        $colIndex = 4; // Columna D (A=1, B=2, C=3, D=4)

        foreach ($clases as $clase) {
            $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3); // +3 porque son 4 columnas (M1, M2, M3, PROM)

            // Mergear las 4 celdas de cada asignatura
            $sheet->mergeCells($startCol . '1:' . $endCol . '1');

            // Asegurarnos de que el texto esté en la celda fusionada
            $sheet->setCellValue($startCol . '1', strtoupper($clase->nombreAsignatura));

            $colIndex += 4; // Avanzar 4 columnas (M1, M2, M3, PROM)
        }

        // Fusionar también las celdas vacías de N°, CURP y NOMBRE en fila 2
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');

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
        ];

        // Determinar cuántas clases hay
        $clases = VistaClasesGrupoSemestre::where('idGrupoSemestre', $this->idGrupoSemestre)
            ->where('anio', now()->year)
            ->whereNull('idEspecialidad')
            ->get();

        $colIndex = 4; // Empieza en D
        foreach ($clases as $clase) {
            // M1, M2, M3, PROM
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 8;
            $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 10;
        }

        // NIA y OBSERVACIONES
        $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++)] = 15;
        $widths[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex)] = 25;

        return $widths;
    }
}
