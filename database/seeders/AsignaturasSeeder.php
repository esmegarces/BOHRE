<?php

namespace Database\Seeders;

use App\Models\Asignatura;
use Illuminate\Database\Seeder;

class AsignaturasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        \DB::transaction(function () {
            /* primer semestre */
            Asignatura::updateOrCreate(['id' => 1, 'nombre' => 'AFSE: ACTIVIDADES FÍSICAS Y DEPORTIVAS I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 2, 'nombre' => 'AFSE: ACTIVIDADES ARTÍSTICAS Y CULTURALES I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 3, 'nombre' => 'CULTURA DIGITAL I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 4, 'nombre' => 'PENSAMIENTO MATEMÁTICO I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 5, 'nombre' => 'LENGUA Y COMUNICACIÓN I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 6, 'nombre' => 'INGLÉS I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 7, 'nombre' => 'CIENCIAS NATURALES, EXPERIMENTALES Y TECNOLOGÍA I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 8, 'nombre' => 'CIENCIAS SOCIALES I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 9, 'nombre' => 'PENSAMIENTO FILOSÓFICO Y HUMANIDADES I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 10, 'nombre' => 'LABORATORIO DE INVESTIGACIÓN', 'tipo' => 'COMUN']);

            /* segundo semestre */
            Asignatura::updateOrCreate(['id' => 11, 'nombre' => 'AFSE: ACTIVIDADES FÍSICAS Y DEPORTIVAS II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 12, 'nombre' => 'AFSE: ACTIVIDADES ARTÍSTICAS Y CULTURALES II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 13, 'nombre' => 'CULTURA DIGITAL II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 14, 'nombre' => 'PENSAMIENTO MATEMÁTICO II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 15, 'nombre' => 'LENGUA Y COMUNICACIÓN II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 16, 'nombre' => 'INGLÉS II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 17, 'nombre' => 'CIENCIAS NATURALES, EXPERIMENTALES Y TECNOLOGÍA II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 18, 'nombre' => 'CIENCIAS SOCIALES II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 19, 'nombre' => 'PENSAMIENTO FILOSÓFICO Y HUMANIDADES II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 20, 'nombre' => 'TALLER DE CIENCIAS I', 'tipo' => 'COMUN']);

            /* tercer semestre */
            Asignatura::updateOrCreate(['id' => 21, 'nombre' => 'AFSE: EDUCACIÓN INTEGRAL EN SEXUALIDAD Y GÉNERO', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 22, 'nombre' => 'PENSAMIENTO MATEMÁTICO III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 23, 'nombre' => 'LENGUA Y COMUNICACIÓN III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 24, 'nombre' => 'INGLÉS III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 25, 'nombre' => 'ECOSISTEMAS INTERACCIONES, ENERGÍA Y DINÁMICA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 26, 'nombre' => 'HUMANIDADES III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 27, 'nombre' => 'TALLER DE CIENCIAS II', 'tipo' => 'COMUN']);

            /* cuarto semestre */
            Asignatura::updateOrCreate(['id' => 28, 'nombre' => 'AFSE: EDUCACIÓN PARA LA SALUD', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 29, 'nombre' => 'CONCIENCIA HISTÓRICA I, PERSPECTIVA DEL MÉXICO ANTIGUO', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 31, 'nombre' => 'INGLÉS IV', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 32, 'nombre' => 'CIENCIAS SOCIALES III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 33, 'nombre' => 'CIENCIAS NATURALES, EXPERIMENTALES Y TECNOLOGÍA III', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 34, 'nombre' => 'TALLER DE CULTURA DIGITAL', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 35, 'nombre' => 'TEMAS SELECTOS DE MATEMÁTICAS', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 36, 'nombre' => 'PENSAMIENTO LITERARIO', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 37, 'nombre' => 'ESPACIO Y SOCIEDAD', 'tipo' => 'COMUN']);

            /* quinto semestre */
            //Asignatura::updateOrCreate(['id' => 38, 'nombre' => 'AFSE: EDUCACIÓN INTEGRAL EN SEXUALIDAD Y GÉNERO', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 39, 'nombre' => 'OPTATIVA 1: TALLER DE PROBABILIDAD Y ESTADÍSTICA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 40, 'nombre' => 'OPTATIVA 2: COMUNICACIÓN Y SOCIEDAD I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 41, 'nombre' => 'OPTATIVA 3: PENSAMIENTO FILOSÓFICO I', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 42, 'nombre' => 'LA ENERGÍA EN LOS PROCESOS DE LA VIDA DIARIA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 43, 'nombre' => 'CONCIENCIA HISTÓRICA II, MÉXICO DURANTE EL EXPANSIONISMO CAPITALISTA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 44, 'nombre' => 'TALLER DE HABILIDADES DE PENSAMIENTO', 'tipo' => 'COMUN']);

            /* sexto semestre */
            Asignatura::updateOrCreate(['id' => 45, 'nombre' => 'AFSE: PRÁCTICA Y COLABORACIÓN CIUDADANA', 'tipo' => 'COMUN']);
            //Asignatura::updateOrCreate(['id' => 46, 'nombre' => 'OPTATIVA 1: TALLER DE PROBABILIDAD Y ESTADÍSTICA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 47, 'nombre' => 'OPTATIVA 2: COMUNICACIÓN Y SOCIEDAD II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 48, 'nombre' => 'OPTATIVA 3: PENSAMIENTO FILOSÓFICO II', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 49, 'nombre' => 'CONCIENCIA HISTÓRICA III, LA REALIDAD ACTUAL EN PERSPECTIVA HISTÓRICA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 50, 'nombre' => 'ORGANISMOS, ESTRUCTURAS Y PROCESOS. HERENCIA Y EVOLUCIÓN BIOLÓGICA', 'tipo' => 'COMUN']);
            Asignatura::updateOrCreate(['id' => 51, 'nombre' => 'TEMAS SELECTOS DE MATEMÁTICAS II', 'tipo' => 'COMUN']);


            /* ESPECIAL ALIMNENTOS */
            /* tercer semestre */
            Asignatura::updateOrCreate(['id' => 52, 'nombre' => 'FL PAA I: CONSERVA FRUTAS, VERDURAS Y LEGUMBRES A TRAVÉS DE MÉTODOS TRADICIONALES', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 53, 'nombre' => 'FL PAA II: TRANSFORMA CEREALES Y HARINAS PARA LA ELABORACIÓN DE TORTILLAS Y PRODUCTOS AFINES', 'tipo' => 'ESPECIALIDAD']);
            /* cuarto semestre */
            Asignatura::updateOrCreate(['id' => 54, 'nombre' => 'FL PAA I: REALIZA DISTINTOS TIPOS DE PAN A BASE DE HARINAS Y OTROS INGREDIENTES', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 55, 'nombre' => 'FL PAA II: ELABORA PRODUCTOS UTILIZANDO AZÚCAR PARA PREPARAR DULCES TÍPICOS', 'tipo' => 'ESPECIALIDAD']);
            /* quinto semestre */
            Asignatura::updateOrCreate(['id' => 56, 'nombre' => 'FL PAA I: OBTIENE BEBIDAS NO ALCOHÓLICAS CON PROCEDIMIENTOS SIMPLES', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 57, 'nombre' => 'FL PAA II: PREPARA PRODUCTOS DE CARNES, DERIVADOS DISPONIBLES Y SUSTITUTOS DE PROTEÍNA', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 58, 'nombre' => 'OPTATIVA 4. PAA. ORGANIZACIÓN DE FLUJO DE MATERIA', 'tipo' => 'ESPECIALIDAD']);
            /* sexto semestre */
            Asignatura::updateOrCreate(['id' => 59, 'nombre' => 'FL PAA I: ELABORA PRODUCTOS LÁCTEOS POR MÉTODOS TRADICIONALES', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 60, 'nombre' => 'FL PAA II: REALIZA PRODUCTOS UTILIZANDO ACEITES, GRASAS Y CONDIMENTOS A TRAVÉS DE MÉTODOS TRADICIONALES', 'tipo' => 'ESPECIALIDAD']);
            //Asignatura::updateOrCreate(['id' => 61, 'nombre' => 'OPTATIVA 4. PAA. ORGANIZACIÓN DE FLUJO DE MATERIA', 'tipo' => 'ESPECIALIDAD']);


            /* ESPECIALIDAD SALUD */
            /* tercer semestre */
            Asignatura::updateOrCreate(['id' => 62, 'nombre' => 'FL ASA I: LLEVA REGISTRO DE RECETAS, INVENTARIOS DE MEDICAMENTOS Y PRODUCTOS FARMACÉUTICOS', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 63, 'nombre' => 'FL ASA II: DESPACHA MEDICAMENTOS Y MATERIAL DE CURACIÓN DE ACUERDO CON PRESCRIPCIONES MEDICAS Y PRODUCTOS FARMACÉUTICOS', 'tipo' => 'ESPECIALIDAD']);
            /* cuarto semestre */
            Asignatura::updateOrCreate(['id' => 64, 'nombre' => 'FL ASA I: ORDENA LAS EXISTENCIAS DE MEDICAMENTOS EN ESTANTES Y ANAQUELES', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 65, 'nombre' => 'FL ASA II: PARTICIPA EN LA PREPARACIÓN DE MEDICAMENTOS Y OTROS COMPUESTOS BAJO SUPERVISIÓN PROFESIONAL QUÍMICO FARMACÉUTICO', 'tipo' => 'ESPECIALIDAD']);
            /* quinto semestre */
            Asignatura::updateOrCreate(['id' => 66, 'nombre' => 'FL ASA I: ASISTE A ESPECIALISTAS DEL ÁREA EN LAS NECESIDADES DEL PACIENTE', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 67, 'nombre' => 'FL ASA II: ASISTE A ESPECIALISTAS DEL ÁREA EN LAS NECESIDADES DEL PACIENTE DIAGNOSTICADO', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 68, 'nombre' => 'OPTATIVA 4. ASA. SALUD INTEGRAL I', 'tipo' => 'ESPECIALIDAD']);
            /* sexto semestre */
            Asignatura::updateOrCreate(['id' => 69, 'nombre' => 'FL ASA I: LAVA, EMPAQUETA Y ESTERILIZA EL MATERIAL E INSTRUMENTAL UTILIZADO EN LAS DISTINTAS ÁREAS DEL SECTOR SALUD', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 70, 'nombre' => 'FL ASA II: REALIZA DIFERENTES ACTIVIDADES ADMINISTRATIVAS SOLICITADAS', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 71, 'nombre' => 'OPTATIVA 4. ASA. SALUD INTEGRAL II', 'tipo' => 'ESPECIALIDAD']);


            /* ESPECIALIDAD ADMINISTRACION */
            /* tercer semestre */
            Asignatura::updateOrCreate(['id' => 72, 'nombre' => 'FL ADMÓN. I: ENTREGA RECURSOS MATERIALES A OTRAS ÁREAS DE UNA ORGANIZACIÓN', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 73, 'nombre' => 'FL ADMÓN. II: ORGANIZA RECURSOS MATERIALES A SOLICITUD DE UN SUPERIOR', 'tipo' => 'ESPECIALIDAD']);
            /* cuarto semestre */
            Asignatura::updateOrCreate(['id' => 74, 'nombre' => 'FL ADMÓN. I: CAPTURA INFORMACIÓN SOLICITADA POR UN SUPERIOR', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 75, 'nombre' => 'FL ADMÓN. II: REGISTRA ENTRADA Y SALIDA DEL PERSONAL DE UNA ORGANIZACIÓN', 'tipo' => 'ESPECIALIDAD']);
            /* quinto semestre */
            Asignatura::updateOrCreate(['id' => 76, 'nombre' => 'FL ADMÓN. I: ORGANIZA EXPEDIENTES Y DOCUMENTACIÓN INTERNA DE LAS DIFERENTES ÁREAS DE UNA ORGANIZACIÓN', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 77, 'nombre' => 'FL ADMÓN. II: ELABORA TRÁMITES ADMINISTRATIVOS BÁSICOS DE UNA ORGANIZACIÓN', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 78, 'nombre' => 'OPTATIVA 4. ADMÓN. DERECHO Y SOCIEDAD I', 'tipo' => 'ESPECIALIDAD']);
            /* sexto semestre */
            Asignatura::updateOrCreate(['id' => 79, 'nombre' => 'FL ADMÓN. I: PROPORCIONA INFORMACIÓN DETALLADA Y CONDICIONES DE VENTA DE BIENES Y SERVICIOS', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 80, 'nombre' => 'FL ADMÓN. II: REALIZA VENTAS DE BIENES Y SERVICIOS AL PUBLICO EN GENERAL', 'tipo' => 'ESPECIALIDAD']);
            Asignatura::updateOrCreate(['id' => 81, 'nombre' => 'OPTATIVA 4. ADMÓN. DERECHO Y SOCIEDAD II', 'tipo' => 'ESPECIALIDAD']);
        });
    }
}
