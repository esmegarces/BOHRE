<?php

namespace App\Exports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsuariosExport implements FromCollection, WithHeadings
{
    /**
     * Devuelve los datos de la colección
     */
    public function collection()
    {
        return Persona::select(
            'id',
            'nombre',
            'apellidoPaterno',
            'apellidoMaterno',
            'fechaNacimiento'
        )->get();
    }

    /**
     * Define los encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellido Paterno',
            'Apellido Materno',
            'Fecha de Nacimiento',
        ];
    }
}
