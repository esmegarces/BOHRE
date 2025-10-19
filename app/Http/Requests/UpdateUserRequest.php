<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Request unicamente para actualizar usuarios (PUT y PATCH)
 * Separa la logica de validacion de la creacion y actualizacion
 * para evitar confusiones con los campos required y unique
 */
class UpdateUserRequest extends FormRequest
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
        // $this->route('id') hace que si la ruta trae el id, se excluya de la validacion unique (solo en update y cuando el campo corresponde al mismo usuario)
        // es decir, si el id del usuario que se esta actualizando es 5, y el correo es el mismo que ya tiene, no marque error de unique
        return [
            'numeroCasa' => 'sometimes|integer|min:1',
            'calle' => 'sometimes|string|max:80|min:5',
            'idLocalidad' => 'sometimes|integer|exists:localidad,id',
            'correo' => "sometimes|email|min:8|max:40|unique:cuenta,correo," . $this->route('id'),
            'contrasena' => 'sometimes|string|max:12|min:8',
            'rol' => 'sometimes|in:admin,alumno,docente',
            'nombre' => 'sometimes|string|max:20|min:3',
            'apellidoPaterno' => 'sometimes|string|max:20|min:3',
            'apellidoMaterno' => 'sometimes|string|max:20|min:3',
            'curp' => 'sometimes|string|max:18|min:18',
            'telefono' => "sometimes|string|max:10|min:10|unique:persona,telefono," . $this->route('id'),
            'sexo' => 'sometimes|in:F,M',
            'fechaNacimiento' => 'sometimes|date',
            'nss' => "sometimes|string|max:50|min:3|unique:persona,nss," . $this->route('id'),
            // validaciones docente
            'cedulaProfesional' => 'sometimes|required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional,' . $this->route('id'),
            'numeroExpediente' => 'sometimes|required_if:rol,docente|string|min:1|unique:docente,numeroExpediente,' . $this->route('id'),
            // validaciones alumno
            'nia' => 'sometimes|required_if:rol,alumno|string|max:8|min:8|unique:alumno,nia,' . $this->route('id'),
            'situacion' => 'sometimes|required_if:rol,alumno|in:activo,baja_temporal,baja_definitiva,egresado',
            'idGrupoSemestre' => 'sometimes|required_if:rol,alumno|numeric|exists:grupo_semestre,id',
            'idGeneracion' => 'sometimes|required_if:rol,alumno|numeric|exists:generacion,id',
            'idEspecialidad' => 'sometimes|required_if:rol,alumno|numeric|exists:especialidad,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rol = $this->input('rol');

            if ($rol === 'docente') {
                if (!$this->filled('cedulaProfesional') || !$this->filled('numeroExpediente')) {
                    $validator->errors()->add('rol', 'Faltan datos para asignar el rol de docente.');
                }
            }

            if ($rol === 'alumno') {
                if (!$this->filled('nia') || !$this->filled('situacion')) {
                    $validator->errors()->add('rol', 'Faltan datos para asignar el rol de alumno.');
                }
            }
        });
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
