<?php

namespace Database\Seeders;

use App\Models\Localidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            // TETELES DE AVILA CASTILLO
            Localidad::updateOrCreate(['id' => 1, 'nombre' => 'CENTRO', 'codigoPostal' => 73930, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 2, 'nombre' => 'COACALCO', 'codigoPostal' => 73939, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 3, 'nombre' => 'HUIXTA', 'codigoPostal' => 73930, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 4, 'nombre' => 'TEXCALACO', 'codigoPostal' => 73938, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 5, 'nombre' => 'ZITALAPAN', 'codigoPostal' => 73935, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 7, 'nombre' => 'CHALAHUICO', 'codigoPostal' => 73937, 'idMunicipio' => 1]);
            Localidad::updateOrCreate(['id' => 8, 'nombre' => 'LA COLONIA', 'codigoPostal' => 73933, 'idMunicipio' => 1]);

            // TLATLAUQUITEPEC
            Localidad::updateOrCreate(['id' => 9, 'nombre' => 'CENTRO','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 10, 'nombre' => 'SAN JOSE','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 11, 'nombre' => 'XOMIACO', 'codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 12, 'nombre' => 'ANALCO','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 13, 'nombre' => 'ARAGON','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 14, 'nombre' => 'MECAYUCAN', 'codigoPostal' => 73903,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 15, 'nombre' => 'ZOCUILA', 'codigoPostal' => 73904,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 16, 'nombre' => 'GUADALUPE','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 17, 'nombre' => 'CONTLA','codigoPostal' => 73900,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 18, 'nombre' => 'GUERRERO','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 19, 'nombre' => 'LA PALMA','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 20, 'nombre' => 'MAZATEPEC','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 21, 'nombre' => 'NECTEPETL','codigoPostal' => 73902,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 22, 'nombre' => 'ZAPOTITAN','codigoPostal' => 73902,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 23, 'nombre' => 'AHUATAMIMILOL','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 24, 'nombre' => 'EL CAMPANARIO','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 25, 'nombre' => 'EL POZO','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 26, 'nombre' => 'JILOTEPEC','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 27, 'nombre' => 'LAS MESAS','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 28, 'nombre' => 'PORTIZUELAS','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 29, 'nombre' => 'COATECTZIN','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 30, 'nombre' => 'LA PRIMAVERA','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 31, 'nombre' => 'TEPETZINTA','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 32, 'nombre' => 'TUNEL DOS','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 33, 'nombre' => 'YOLOCTZIN','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 34, 'nombre' => 'COXTOJAPAN','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 35, 'nombre' => 'CUACUALAXTA','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 36, 'nombre' => 'CHICUACO','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 37, 'nombre' => 'LA UNION','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 38, 'nombre' => 'MACUILQUILA','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 39, 'nombre' => 'TEHUAGCO','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 40, 'nombre' => 'BUENA VISTA','codigoPostal' => 73902, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 41, 'nombre' => 'CALATEPEC','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 42, 'nombre' => 'CHAGCHALTZIN','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 43, 'nombre' => 'CHILILISTITIPAN','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 44, 'nombre' => 'EL CANAL','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 45, 'nombre' => 'SAN JOSE CHAGCHALZIN','codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 46, 'nombre' => 'TAMALAYO','codigoPostal' => 73905, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 47, 'nombre' => 'ACAMALOTA','codigoPostal' => 73903,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 48, 'nombre' => 'EL PROGRESO','codigoPostal' => 73905,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 49, 'nombre' => 'ELOXOCHITAN','codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 50, 'nombre' => 'JILIAPA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 51, 'nombre' => 'ACOCOGTA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 52, 'nombre' => 'ILITA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 53, 'nombre' => 'PEZMATLAN','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 54, 'nombre' => 'TEPANZOL','codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 55, 'nombre' => 'XALTETA','codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 56, 'nombre' => 'ATALPA','codigoPostal' => 73900,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 57, 'nombre' => 'HUAXTLA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 58, 'nombre' => 'TEPEHICAN','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 59, 'nombre' => 'TEZIHUTANAPA','codigoPostal' => 73903, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 60, 'nombre' => 'XACUINCO','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 61, 'nombre' => 'TATAUZOQUICO','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 62, 'nombre' => 'JALACINGUITO','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 63, 'nombre' => 'TOCHIMPA','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 64, 'nombre' => 'XALTENANGO','codigoPostal' => 73900, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 65, 'nombre' => 'AJOCOTZINGO','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 66, 'nombre' => 'EL CARMEN ILITA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 67, 'nombre' => 'OCOTA','codigoPostal' => 73904, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 68, 'nombre' => 'TANHUIXCO DEL CARMEN','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 69, 'nombre' => 'TZINACANTEPEC','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 70, 'nombre' => 'ALMOLONI','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 71, 'nombre' => 'EL MIRADOR','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 72, 'nombre' => 'PLAN DE GUADALUPE','codigoPostal' => 73907, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 73, 'nombre' => 'OCOTLAN','codigoPostal' => 73907, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 74, 'nombre' => 'XONOCUAUTLA','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 75, 'nombre' => 'CHINAMPA','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 76, 'nombre' => 'TEPETENO DE ITURBIDE','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 77, 'nombre' => 'ATIOYAN','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 78, 'nombre' => 'GOMEZ ORIENTE','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 79, 'nombre' => 'GOMEZ PONIENTE','codigoPostal' => 73906, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 80, 'nombre' => 'GOMEZ SUR','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 81, 'nombre' => 'CUAUTLAMINGO','codigoPostal' => 73907, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 82, 'nombre' => 'EL DURAZNILLO','codigoPostal' => 73909,'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 83, 'nombre' => 'PRIMERO DE SEPTIEMBRE','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 84, 'nombre' => 'TZINCUILAPAN','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 85, 'nombre' => 'IXMATLACO','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 86, 'nombre' => 'OYAMELES','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 87, 'nombre' => 'SAN ANTONIO','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 88, 'nombre' => 'LA CUMBRE','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 89, 'nombre' => 'LOMA DE LA YERBA','codigoPostal' => 73909, 'idMunicipio' => 2]);
            Localidad::updateOrCreate(['id' => 90, 'nombre' => 'SAN JOSE DEL RETIRO','codigoPostal' => 73909, 'idMunicipio' => 2]);

            // YAONAHUAC
            Localidad::updateOrCreate(['id' => 91, 'nombre' => 'CENTRO','codigoPostal' => 73910, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 92, 'nombre' => 'ACOCOGTA','codigoPostal' => 73918, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 93, 'nombre' => 'AHUATA','codigoPostal' => 73913, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 94, 'nombre' => 'ATOTOCOYAN','codigoPostal' => 73915, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 95, 'nombre' => 'TETELTIPAN','codigoPostal' => 73915, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 96, 'nombre' => 'TEXUACO','codigoPostal' => 73915,'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 97, 'nombre' => 'AHUEHUETITAN','codigoPostal' => 73914, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 98, 'nombre' => 'ATEMEYA','codigoPostal' => 73913, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 99, 'nombre' => 'CONZINZINTAN','codigoPostal' => 73917, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 100, 'nombre' => 'MAZATONAL','codigoPostal' => 73913, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 101, 'nombre' => 'OCOTEPEC O TEACO','codigoPostal' => 73916,'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 102, 'nombre' => 'TAGGOTAHUACAN','codigoPostal' => 73917, 'idMunicipio' => 3]); // posible duplicado de Talcozamán
            Localidad::updateOrCreate(['id' => 103, 'nombre' => 'TALCOZAMAN','codigoPostal' => 73917, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 104, 'nombre' => 'TATEMPAN','codigoPostal' => 73916, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 105, 'nombre' => 'TEPANTILOYAN','codigoPostal' => 73918, 'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 106, 'nombre' => 'TEXOCOATAUJ','codigoPostal' => 73915,'idMunicipio' => 3]);
            Localidad::updateOrCreate(['id' => 107, 'nombre' => 'XOCHIUISTA','codigoPostal' => 73910, 'idMunicipio' => 3]); // no disponible, se dejó el CP de la cabecera

            // CHIGNAUTLA
            Localidad::updateOrCreate(['id' => 108, 'nombre' => 'DE LA CRUZ','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 109, 'nombre' => 'CENTRO','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 110, 'nombre' => 'EMILIANO ZAPATA','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 111, 'nombre' => 'EL ENCANTO','codigoPostal' => 73954,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 112, 'nombre' => 'ANALCO','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 113, 'nombre' => 'LOMAS DEL PEDREGAL','codigoPostal' => 73956,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 114, 'nombre' => 'CALICAPAN','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 115, 'nombre' => 'LA AZTECA','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 116, 'nombre' => 'ATETA','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 117, 'nombre' => 'CRUTZIZIN','codigoPostal' => 73956,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 118, 'nombre' => 'TEQUIMILA','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 119, 'nombre' => 'TEPEPAN','codigoPostal' => 73953,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 120, 'nombre' => 'COAHUIXCO','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 121, 'nombre' => 'YOPI','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 122, 'nombre' => 'HUAPALTEPEC','codigoPostal' => 73958,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 123, 'nombre' => 'COATZALA','codigoPostal' => 73958,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 124, 'nombre' => 'LOS PARAJES','codigoPostal' => 73958,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 125, 'nombre' => 'LOS HUMEROS','codigoPostal' => 73957,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 126, 'nombre' => 'TALZINTAN','codigoPostal' => 73958,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 127, 'nombre' => 'SOTOLTEPEC','codigoPostal' => 73957,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 128, 'nombre' => 'TENEXTEPEC','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 129, 'nombre' => 'TECOLOTEPEC','codigoPostal' => 73957,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 130, 'nombre' => 'TEZHUATEPEC','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 131, 'nombre' => 'SOSA','codigoPostal' => 73950,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 132, 'nombre' => 'LA AGUARDIENTERA','codigoPostal' => 73956,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 133, 'nombre' => 'ENCINO RICO','codigoPostal' => 73956,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 134, 'nombre' => 'SAN ISIDRO','codigoPostal' => 73956,'idMunicipio' => 4]);
            Localidad::updateOrCreate(['id' => 135, 'nombre' => 'LOS ASERRADEROS','codigoPostal' => 73956,'idMunicipio' => 4]);

            // HUEYAPAN
            Localidad::updateOrCreate(['id' => 136,'nombre' => 'ATMOLONI','codigoPostal' => 73923,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 137,'nombre' => 'BUENA VISTA','codigoPostal' => 73920,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 138,'nombre' => 'NEPACHOLAN','codigoPostal' => 73928,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 139,'nombre' => 'TATILOYAN','codigoPostal' => 73927,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 140,'nombre' => 'CENTRO','codigoPostal' => 73920,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 141,'nombre' => 'CUATRO CAMINOS','codigoPostal' => 73926,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 142,'nombre' => 'LA AURORA','codigoPostal' => 73926,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 143,'nombre' => 'LAS GARDENIAS','codigoPostal' => 73926,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 144,'nombre' => 'PASO REAL','codigoPostal' => 73926,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 145,'nombre' => 'TALTZINTAN','codigoPostal' => 73927,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 146,'nombre' => 'ATEXCACO','codigoPostal' => 73924,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 147,'nombre' => 'CUAUTENO','codigoPostal' => 73926,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 148,'nombre' => 'DOS RIOS (LA PAGODA)','codigoPostal' => 73927,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 149,'nombre' => 'TANAMACOYAN','codigoPostal' => 73920,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 150,'nombre' => 'TETELILLA','codigoPostal' => 73927,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 151,'nombre' => 'AHUATEPEC','codigoPostal' => 73928,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 152,'nombre' => 'COLOSTITAN','codigoPostal' => 73924,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 153,'nombre' => 'NEXPAN','codigoPostal' => 73928,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 154,'nombre' => 'TALCOZAMAN','codigoPostal' => 73927,'idMunicipio' => 5]);
            Localidad::updateOrCreate(['id' => 155,'nombre' => 'XOMETA','codigoPostal' => 73926,'idMunicipio' => 5]);

            // TEZIUTLAN
            Localidad::updateOrCreate(['id' => 156,'nombre' => 'ARBOLEDAS DE SAN RAFAEL','codigoPostal' => 73880,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 157,'nombre' => 'EL CHOHUIS','codigoPostal' => 73880,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 158,'nombre' => 'GARDENIAS DE TEZIUTLAN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 159,'nombre' => 'TEZIUTLAN (FOVISSSTE)','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 160,'nombre' => 'VALLE DORADO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 161,'nombre' => 'JARDINES DE TEZIUTLAN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 162,'nombre' => 'CHIGNAULINGO','codigoPostal' => 73883,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 163,'nombre' => 'SONTECOMACO','codigoPostal' => 73883,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 164,'nombre' => 'CENTRO','codigoPostal' => 73800,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 165,'nombre' => 'LA AURORA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 166,'nombre' => 'TAXCALA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 167,'nombre' => 'EL PARAISO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 168,'nombre' => 'LA MACETA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 169,'nombre' => 'NOVENA DEL CARMEN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 170,'nombre' => 'CLONIA SAN CAYETANO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 171,'nombre' => 'COLONOS SAN CAYETANO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 172,'nombre' => 'LOS CASTAÑOS','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 173,'nombre' => 'LOS CIPRESES','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 174,'nombre' => 'SAN FRANCISCO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 175,'nombre' => 'AHUATENO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 176,'nombre' => 'CASA BLANCA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 177,'nombre' => 'EL EDEN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 178,'nombre' => 'LAS CAMELIAS','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 179,'nombre' => 'LOS PRESIDENTES','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 180,'nombre' => 'COYOTZINGO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 181,'nombre' => 'LA GLORIA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 182,'nombre' => 'RINCONADA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 183,'nombre' => 'JUAREZ','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 184,'nombre' => 'MANUEL AVILA CAMACHO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 185,'nombre' => 'FRANCIA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 186,'nombre' => 'XOLOCO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 187,'nombre' => 'LINDA VISTA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 188,'nombre' => 'VILLA MARIA RENEE','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 189,'nombre' => 'LA MAGDALENA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 190,'nombre' => 'EL FRESNILLO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 191,'nombre' => 'LAZARO CARDENAS','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 192,'nombre' => 'LOMA BELLA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 193,'nombre' => 'REVOLUCION','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 194,'nombre' => 'SANTA ROSA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 195,'nombre' => 'EL PINAL','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 196,'nombre' => 'INDUSTRIAL FRANCIA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 197,'nombre' => 'INDUSTRIAL MINERA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 198,'nombre' => 'ARBOLEDAS','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 199,'nombre' => 'LA AZTECA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 200,'nombre' => 'VISTA HERMOSA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 201,'nombre' => 'SAN PEDRO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 202,'nombre' => 'AHUATA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 203,'nombre' => 'AMILA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 204,'nombre' => 'COYOPOTL','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 205,'nombre' => 'SAN JUAN ACATENO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 206,'nombre' => 'TATAHUICAPA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 207,'nombre' => 'TEMECATA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 208,'nombre' => 'AIRE LIBRE','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 209,'nombre' => 'MEXCALCUAUTLA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 210,'nombre' => 'SAN SEBASTIAN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 211,'nombre' => 'LAS BRISAS','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 212,'nombre' => 'SAN DIEGO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 213,'nombre' => 'SAN JUAN TEZONGO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 214,'nombre' => 'ATOLUCA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 215,'nombre' => 'EL CARRIZAL','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 216,'nombre' => 'IXTAHUIATA O LA LEGUA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 217,'nombre' => 'SAN MIGUEL CAPULINES','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 218,'nombre' => 'HUEHUEIMICO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 219,'nombre' => 'EL PEDREGAL','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 220,'nombre' => 'XOLOATENO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 221,'nombre' => 'CUAXOXPA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 222,'nombre' => 'IXTICPAN','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 223,'nombre' => 'IXTLAHUACA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 224,'nombre' => 'MAXTACO','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 225,'nombre' => 'LA CANTERA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 226,'nombre' => 'LA GARITA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 227,'nombre' => 'LOMA BONITA','codigoPostal' => 73887,'idMunicipio' => 6]);
            Localidad::updateOrCreate(['id' => 228,'nombre' => 'SECCION 23','codigoPostal' => 73887,'idMunicipio' => 6]);

            // ATEMPAN
            Localidad::updateOrCreate(['id' => 229,'nombre' => 'LAS DELICIAS','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 230,'nombre' => 'MEYUCO','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 231,'nombre' => 'ATZALAN','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 232,'nombre' => 'SAN NICOLAS','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 233,'nombre' => 'MALOAPAN','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 234,'nombre' => 'CALA NORTE','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 235,'nombre' => 'CALA SUR','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 236,'nombre' => 'ANIMASCO','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 237,'nombre' => 'TACOPAN','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 238,'nombre' => 'APATAUYAN','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 239,'nombre' => 'LAS CANOAS O SAN AMBROSIO','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 240,'nombre' => 'TANHUIXCO','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 241,'nombre' => 'EL POTRERO','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 242,'nombre' => 'HUEXOTENO','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 243,'nombre' => 'TEZHUATEPEC','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 244,'nombre' => 'EL CUATRO','codigoPostal' => 73926,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 245,'nombre' => 'TEZOMPAN','codigoPostal' => 73928,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 246,'nombre' => 'AHUATENO','codigoPostal' => 73927,'idMunicipio' => 7]);
            Localidad::updateOrCreate(['id' => 247,'nombre' => 'CENTRO','codigoPostal' => 73926,'idMunicipio' => 7]);




        });
    }
}
