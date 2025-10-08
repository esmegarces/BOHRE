<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
    public function rules(): array
    {
        // variable para saber si es required o sometimes de acuerdo al metodo http
        $requiredOrSometimes = $this->isMethod('patch') ? 'sometimes' : 'required';

        // $this->route('id') hace que si la ruta trae el id, se excluya de la validacion unique (solo en update y cuando el campo corresponde al mismo usuario)
        // es decir, si el id del usuario que se esta actualizando es 5, y el correo es el mismo que ya tiene, no marque error de unique
        return [
            'numeroCasa' => "$requiredOrSometimes|integer|min:1",
            'calle' => "$requiredOrSometimes|string|max:80|min:5",
            'idLocalidad' => "$requiredOrSometimes|integer|exists:localidad,id",
            'correo' => "$requiredOrSometimes|email|unique:cuenta,correo," . $this->route('id') . "|max:40|min:12",
            'contrasena' => "$requiredOrSometimes|string|max:12|min:8",
            'rol' => "$requiredOrSometimes|in:admin,alumno,docente",
            'nombre' => "$requiredOrSometimes|string|max:20|min:3",
            'apellidoPaterno' => "$requiredOrSometimes|string|max:20|min:3",
            'apellidoMaterno' => "$requiredOrSometimes|string|max:20|min:3",
            'curp' => "$requiredOrSometimes|string|max:18|min:18",
            'telefono' => "$requiredOrSometimes|string|max:10|min:10|unique:persona,telefono," . $this->route('id'),
            'sexo' => "$requiredOrSometimes|in:F,M",
            'fechaNacimiento' => "$requiredOrSometimes|date",
            'nss' => "$requiredOrSometimes|string|max:50|min:3|unique:persona,nss," . $this->route('id'),
            // validaciones docente
            'cedulaProfesional' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional,' . $this->route('id'),
            'numeroExpediente' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,docente|string|min:1|unique:docente,numeroExpediente,' . $this->route('id'),
            // validaciones alumno
            'nia' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,alumno|string|max:8|min:8|unique:alumno,nia,' . $this->route('id'),
            'situacion' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,alumno|in:activo,baja_temporal,baja_definitiva,egresado',
        ];
    }

    // Validacion condicional para los campos de docente y alumno
    // extra debido a que required_if no funciona bien con sometimes en metodo patch, necesario para verificar que si se cambia el rol, se envien los datos correspondientes
    // si son enviados los datos de docente o alumno, deben pasar la validacion, peo si no se envian, se marca el error
    // unicamente si viene el campo rol
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
