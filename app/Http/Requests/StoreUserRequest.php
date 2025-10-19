<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'numeroCasa' => 'required|integer|min:1',
            'calle' => 'required|string|max:80|min:5',
            'idLocalidad' => 'required|integer|exists:localidad,id',
            'correo' => 'required|email|min:8|max:40|unique:cuenta,correo',
            'contrasena' => 'required|string|max:12|min:8',
            'rol' => 'required|in:admin,alumno,docente',
            'nombre' => 'required|string|max:20|min:3',
            'apellidoPaterno' => 'required|string|max:20|min:3',
            'apellidoMaterno' => 'required|string|max:20|min:3',
            'curp' => 'required|string|max:18|min:18',
            'telefono' => 'required|string|max:10|min:10|unique:persona,telefono',
            'sexo' => 'required|in:F,M',
            'fechaNacimiento' => 'required|date',
            'nss' => 'required|string|max:12|min:11|unique:persona,nss',
            // validaciones docente
            'cedulaProfesional' => 'required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional',
            'numeroExpediente' => 'required_if:rol,docente|string|min:1|unique:docente,numeroExpediente',
            // validaciones alumno
            'nia' => 'required_if:rol,alumno|string|max:8|min:8|unique:alumno,nia',
            'situacion' => 'required_if:rol,alumno|in:activo,baja_temporal,baja_definitiva,egresado',
            'idGrupoSemestre' => 'required_if:rol,alumno|numeric|exists:grupo_semestre,id',
            'idGeneracion' => 'required_if:rol,alumno|numeric|exists:generacion,id',
            'idEspecialidad' => 'sometimes|required_if:rol,alumno|numeric|exists:especialidad,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
