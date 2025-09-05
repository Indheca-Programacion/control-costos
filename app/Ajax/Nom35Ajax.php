<?php

namespace App\Ajax;

session_start();

ini_set('display_errors', 1);

require_once "../../vendor/autoload.php";
require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

use App\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Nom35Ajax
{

    public $respuestas = [
        "lote2" => [
            "Nunca" => 0,
            "Casi nunca" => 1,
            "Algunas veces" => 2,
            "Casi siempre" => 3,
            "Siempre" => 4,
        ],
        "lote1" => [
            "Nunca" => 4,
            "Casi nunca" => 3,
            "Algunas veces" => 2,
            "Casi siempre" => 1,
            "Siempre" => 0,
        ]
    ];
	/*=============================================
	Generar reporte
	=============================================*/
	public function crearReporte()
	{

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $filePath = $_FILES['file']['tmp_name'];
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            // Assuming the first row contains the headers
            $headers = array_shift($data);
            $processedData = [];

            foreach ($data as $row) {
                $processedData[] = array_combine($headers, $row);
            }

            $resultados = [
                "total" => 0,
                "Ambiente de trabajo" => 0,
                "Factores propios de la actividad" => 0,
                "Organización del tiempo de trabajo" => 0,
                "Liderazgo y relaciones en el trabajo" => 0,

                "Condiciones en el ambiente de trabajo" => 0,
                "Carga de trabajo" => 0,
                "Falta de control sobre el trabajo" => 0,
                "Jornada de trabajo" => 0,
                "Interferencia en la relación trabajo-familia" => 0,
                "Liderazgo" => 0,
                "Relaciones en el trabajo" => 0,
                "Violencia" => 0,
                
                "Condiciones peligrosas e inseguras" => 0,
                "Condiciones deficientes e insalubres" => 0,
                "Trabajos peligrosos" => 0,
                "Cargas cuantitativas" => 0,
                "Ritmos de trabajo acelerado" => 0,
                "Carga mental" => 0,
                "Cargas de alta responsabilidad" => 0,
                "Cargas contradictorias o inconsistentes" => 0,
                "Jornadas de trabajo extensas" => 0,
                "Influencia del trabajo fuera del centro laboral" => 0,
                "Influencia de las responsabilidades familiares" => 0,
                "Limitada o nula posibilidad de desarrollo" => 0,
                "Falta de control y autonomía sobre el trabajo" => 0,
                "Escasa claridad de funciones" => 0,
                "Limitada o inexistente capacitación" => 0,
                "Características del liderazgo" => 0,
                "Relaciones sociales en el trabajo" => 0,
                "Violencia laboral" => 0,
                "Cargas psicológicas emocionales" => 0,
                "Comunicación ineficaz" => 0,
                "Deficiente relación con los colaboradores que supervisa" => 0,

            ];

            $preguntas = array(
                "Mi trabajo me exige hacer mucho esfuerzo físico." => array(
                    "categoria" => "Ambiente de trabajo",
                    "dominio" => "Condiciones en el ambiente de trabajo",
                    "dimension" => "Condiciones peligrosas e inseguras",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me preocupa sufrir un accidente en mi trabajo." => array(
                    "categoria" => "Ambiente de trabajo",
                    "dominio" => "Condiciones en el ambiente de trabajo",
                    "dimension" => "Condiciones deficientes e insalubres",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Considero que las actividades que realizo son peligrosas." => array(
                    "categoria" => "Ambiente de trabajo",
                    "dominio" => "Condiciones en el ambiente de trabajo",
                    "dimension" => "Trabajos peligrosos",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Por la cantidad de trabajo que tengo debo quedarme tiempo adicional a mi turno." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas cuantitativas",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Por la cantidad de trabajo que tengo debo trabajar sin parar. " => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Ritmos de trabajo acelerado",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Considero que es necesario mantener un ritmo de trabajo acelerado." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Ritmos de trabajo acelerado",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo exige que esté muy concentrado." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Carga mental",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo requiere que memorice mucha información." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Carga mental",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo exige que atienda varios asuntos al mismo tiempo." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas cuantitativas",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "En mi trabajo soy responsable de cosas de mucho valor." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas de alta responsabilidad",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Respondo ante mi jefe por los resultados de toda mi área de trabajo." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas de alta responsabilidad",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "En mi trabajo me dan órdenes contradictorias." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas contradictorias o inconsistentes",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Considero que en mi trabajo me piden hacer cosas innecesarias." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas contradictorias o inconsistentes",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Trabajo horas extras más de tres veces a la semana." => array(
                    "categoria" => "Organización del tiempo de trabajo",
                    "dominio" => "Jornada de trabajo",
                    "dimension" => "Jornadas de trabajo extensas",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo me exige laborar en días de descanso, festivos o fines de semana." => array(
                    "categoria" => "Organización del tiempo de trabajo",
                    "dominio" => "Jornada de trabajo",
                    "dimension" => "Jornadas de trabajo extensas",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Considero que el tiempo en el trabajo es mucho y perjudica mis actividades familiares o personales." => array(
                    "categoria" => "Organización del tiempo de trabajo",
                    "dominio" => "Interferencia en la relación trabajo-familia",
                    "dimension" => "Influencia del trabajo fuera del centro laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Pienso en las actividades familiares o personales cuando estoy en mi trabajo" => array(
                    "categoria" => "Organización del tiempo de trabajo",
                    "dominio" => "Interferencia en la relación trabajo-familia",
                    "dimension" => "Influencia de las responsabilidades familiares",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo permite que desarrolle nuevas habilidades. " => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Limitada o nula posibilidad de desarrollo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "En mi trabajo puedo aspirar a un mejor puesto." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Limitada o nula posibilidad de desarrollo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Durante mi jornada de trabajo puedo tomar pausas cuando las necesito." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Falta de control y autonomía sobre el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Puedo decidir la velocidad a la que realizo mis actividades en mi trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Falta de control y autonomía sobre el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Puedo cambiar el orden de las actividades que realizo en mi trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Falta de control y autonomía sobre el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me informan con claridad cuáles son mis funciones." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Escasa claridad de funciones",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me explican claramente los resultados que debo obtener en mi trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Escasa claridad de funciones",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me informan con quién puedo resolver problemas o asuntos de trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Escasa claridad de funciones",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me permiten asistir a capacitaciones relacionadas con mi trabajo." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Limitada o inexistente capacitación",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Recibo capacitación útil para hacer mi trabajo." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Falta de control sobre el trabajo",
                    "dimension" => "Limitada o inexistente capacitación",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi jefe tiene en cuenta mis puntos de vista y opiniones." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Características del liderazgo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Características del liderazgo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Puedo confiar en mis compañeros de trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Relaciones en el trabajo",
                    "dimension" => "Relaciones sociales en el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Cuando tenemos que realizar trabajo de equipo los compañeros colaboran." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Relaciones en el trabajo",
                    "dimension" => "Relaciones sociales en el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mis compañeros de trabajo me ayudan cuando tengo dificultades." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Relaciones en el trabajo",
                    "dimension" => "Relaciones sociales en el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "En mi trabajo puedo expresarme libremente sin interrupciones." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Relaciones en el trabajo",
                    "dimension" => "Relaciones sociales en el trabajo",
                    "respuesta" => "lote1",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Recibo críticas constantes a mi persona y/o trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Recibo burlas, calumnias, difamaciones, humillaciones o ridiculizaciones." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Se ignora mi presencia o se me excluye de las reuniones de trabajo y en la toma de decisiones." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Se manipulan las situaciones de trabajo para hacerme parecer un mal trabajador." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Se ignoran mis éxitos laborales y se atribuyen a otros trabajadores." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Me bloquean o impiden las oportunidades que tengo para obtener ascenso o mejora en mi trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "He presenciado actos de violencia en mi centro de trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Violencia",
                    "dimension" => "Violencia laboral",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Atiendo clientes o usuarios muy enojados." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas psicológicas emocionales",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Mi trabajo me exige atender personas muy necesitadas de ayuda o enfermas." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas psicológicas emocionales",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Para hacer mi trabajo debo demostrar sentimientos distintos a los míos." => array(
                    "categoria" => "Factores propios de la actividad",
                    "dominio" => "Carga de trabajo",
                    "dimension" => "Cargas psicológicas emocionales",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Comunican tarde los asuntos de trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Relaciones en el trabajo",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Dificultan el logro de los resultados del trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Relaciones en el trabajo",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                ),
                "Ignoran las sugerencias para mejorar su trabajo." => array(
                    "categoria" => "Liderazgo y relaciones en el trabajo",
                    "dominio" => "Liderazgo",
                    "dimension" => "Relaciones en el trabajo",
                    "respuesta" => "lote2",
                    "respuestas" => [
                        "Nunca" => 0,
                        "Casi nunca" => 0,
                        "Algunas veces" => 0,
                        "Casi siempre" => 0,
                        "Siempre" => 0,
                    ]
                )
            );

            foreach ($processedData as $key => $respuesta) {
                $respuesta = array_slice($respuesta, 5);
                

                foreach ($respuesta as $pregunta => $response) {

                    if ( isset($preguntas[$pregunta]) ) {

                        // Se obtiene el valor de la respuesta
                        $valor = $this->obtenerValorRespuesta($response, $preguntas[$pregunta]["respuesta"]);
                        
                        // Se suma la cantidad de respuestas asignadas a cada pregunta
                        if ( strtolower($response) !== 'si' && strtolower($response) !== 'no' && $response !== null ) {
                            $preguntas[$pregunta]["respuestas"][$response] += 1;
                        }
                        
                        // Se suma el valor de cada respuesta a la categoría, dominio y dimensión
                        $resultados["total"] += $valor;
                        $resultados[$preguntas[$pregunta]["categoria"]] += $valor;
                        $resultados[$preguntas[$pregunta]["dominio"]] += $valor;
                        $resultados[$preguntas[$pregunta]["dimension"]] += $valor;
                    }

                }

            }

            include "../../reportes/nom35.php";

        } else {
            echo json_encode(['codigo' => 400, 'error' => true, 'mensaje' => 'No se pudo cargar el archivo']);
            exit;
        }
        
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        

        echo json_encode($respuesta);
	}

    
    function obtenerValorRespuesta($respuestaUsuario, $loteRespuesta) {
        // Verificar si la respuesta del usuario es válida
        if ( isset($this->respuestas[$loteRespuesta][$respuestaUsuario]) ) {
            return $this->respuestas[$loteRespuesta][$respuestaUsuario];
        }else{
            return 0;
        }

    }

}

/*=============================================
GENERAR FORMATO
=============================================*/
$nom35 = new Nom35Ajax();
$nom35 -> crearReporte();
