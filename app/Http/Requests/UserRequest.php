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

        return [
            'numeroCasa' => "$requiredOrSometimes|integer|min:1",
            'calle' => "$requiredOrSometimes|string|max:80|min:5",
            'idLocalidad' => "$requiredOrSometimes|integer|exists:localidad,id",
            'correo' => "$requiredOrSometimes|email|unique:cuenta,correo|max:40|min:12",
            'contrasena' => "$requiredOrSometimes|string|max:12|min:8",
            'rol' => "$requiredOrSometimes|in:admin,alumno,docente",
            'nombre' => "$requiredOrSometimes|string|max:20|min:3",
            'apellidoPaterno' => "$requiredOrSometimes|string|max:20|min:3",
            'apellidoMaterno' => "$requiredOrSometimes|string|max:20|min:3",
            'curp' => "$requiredOrSometimes|string|max:18|min:18",
            'telefono' => "$requiredOrSometimes|string|max:10|min:10|unique:persona,telefono",
            'sexo' => "$requiredOrSometimes|in:F,M",
            'fechaNacimiento' => "$requiredOrSometimes|date",
            'nss' => "$requiredOrSometimes|string|max:50|min:3|unique:persona,nss",
            // validaciones docente
            'cedulaProfesional' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional',
            'numeroExpediente' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,docente|string|min:1|unique:docente,numeroExpediente',
            // validaciones alumno
            'nia' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,alumno|string|max:10|min:8|unique:alumno,nia',
            'situacion' => ($this->isMethod('patch') ? 'sometimes|' : '') . 'required_if:rol,alumno|in:activo,baja_temporal,baja_definitiva,egresado',
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
