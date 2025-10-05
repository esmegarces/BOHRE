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
        return [
            'numeroCasa' => 'required|integer|min:1',
            'calle' => 'required|string|max:80|min:5',
            'idLocalidad' => 'required|integer|exists:localidad,id',
            'correo' => 'required|email|unique:cuenta,correo|max:40|min:12',
            'contrasena'=>'required|string|max:12|min:8',
            'rol'=>'required|in:admin,alumno,docente',
            'nombre'=>'required|string|max:20|min:3',
            'apellidoPaterno'=>'required|string|max:20|min:3',
            'apellidoMaterno'=>'required|string|max:20|min:3',
            'curp'=>'required|string|max:18|min:18',
            'telefono'=>'required|string|max:10|min:10|unique:persona,telefono',
            'sexo'=>'required|in:F,M',
            'fechaNacimiento'=>'required|date',
            'nss'=>'required|string|max:50|min:3|unique:persona,nss',
            // validaciones docente
            'cedulaProfesional'=>'required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional',
            'numeroExpediente'=>'required_if:rol,docente|string|min:1|unique:docente,numeroExpediente',
            //validaciones alumno
            'nia'=>'required_if:rol,alumno|string|max:10|min:8|unique:alumno,nia',
            //'numeroLista'=>'required_if:rol,docente|string|max:13|min:10|unique:docente,cedulaProfesional',
            'situacion'=>'required_if:rol,alumno|in:activo,baja_temporal,baja_definitiva,egresado',
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
