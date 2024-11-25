<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class QuizController extends Controller
{
    private $questions = [
        [
            'id' => 1,
            'text' => '¿Cuál es el objetivo principal del entrenamiento de seguridad en el manejo de materiales químicos en los laboratorios del RUM?',
            'options' => [
                'a) Aprender a manejar residuos radiactivos',
                'b) Cumplir con las normas de la universidad',
                'c) Garantizar la seguridad personal y el cumplimiento de regulaciones locales y federales',
                'd) Aprender a usar equipos de laboratorio avanzados',
            ],
        ],
        [
            'id' => 2,
            'text' => '¿Qué normativa regula la exposición a químicos peligrosos en los laboratorios según OSHA?',
            'options' => [
                'a) 29 CFR 1910.1200',
                'b) Subparte K de la CFR 40 Parte 262',
                'c) 29 CFR 1910.1450',
                'd) Plan de Manejo de Laboratorio del RUM',
            ],
        ],
        [
            'id' => 3,
            'text' => '¿Qué se debe hacer si en un laboratorio se acumula el máximo de galones de materiales no deseados?',
            'options' => [
                'a) Los materiales deben ser etiquetados inmediatamente',
                'b) Los materiales deben ser retirados en un plazo de 10 días',
                'c) Se deben transferir a otro laboratorio',
                'd) Deben ser almacenados por un máximo de 6 meses',
            ],
        ],
        [
            'id'=> 4,
            'text'=> "¿Cuál de las siguientes es una de las mejores prácticas recomendadas en el Plan de Manejo de Laboratorio?",
            'options'=> [
                "a) Almacenar todos los productos químicos juntos sin importar su compatibilidad",
                "b) No etiquetar los productos químicos para ahorrar tiempo",
                "c) Utilizar métodos adecuados para el almacenamiento y la segregación de sustancias químicas peligrosas",
                "d) Usar cualquier contenedor disponible para almacenar productos químicos"
            ],
        ],
        [
            'id'=> 5,
            'text'=> "¿Qué información debe incluirse en el etiquetado de los materiales no deseados?",
            'options'=> [
                "a) Solo el nombre del material",
                "b) Nombre del material, fecha de acumulación y cantidad",
                "c) Solo la cant'id'ad del material",
                "d) Fecha de acumulación y responsable del material"
        ],
    ],   
        [
            'id'=> 6,
            'text'=> "¿Cuál es el límite de tiempo que los materiales no deseados pueden permanecer en un laboratorio antes de ser retirados?",
            'options'=> [
                "a) 1 mes",
                "b) 6 meses",
                "c) 3 meses",
                "d) 12 meses"
        ],
    ],
        [
            'id'=> 7,
            'text'=> "¿Qué volumen máximo de materiales no deseados puede acumularse en un laboratorio antes de que deban ser retirados en 10 días?",
            'options'=> [
                "a) 10 galones",
                "b) 55 galones",
                "c) 25 galones",
                "d) 5 galones"
        ],
    ],
        [
            'id'=> 8,
            'text'=> "¿Qué recomendación se debe seguir al almacenar sustancias corrosivas o especialmente peligrosas (PHS)?",
            'options'=> [
                "a) Almacenarlas por encima del nivel de los ojos",
                "b) Almacenarlas en estantes montados en la pared",
                "c) Almacenarlas por debajo del nivel de los ojos",
                "d) Almacenarlas cerca de fuentes de calor"
        ],
    ],
        [
            'id'=> 9,
            'text'=> "¿Qué se encontró durante la inspección de la EPA en marzo de 2023 relacionado con el almacenamiento de materiales?",
            'options'=> [
                "a) Todos los materiales estaban correctamente etiquetados",
                "b) Los materiales estaban organizados de manera ejemplar",
                "c) Algunos materiales estaban almacenados de manera incompatible y en contenedores en mal estado",
                "d) No se encontraron problemas significativos"
        ],
    ],
        [
            'id'=> 10,
            'text'=> "¿Cuál es la principal razón para segregar los materiales químicos en el laboratorio?",
            'options'=> [
                "a) Facilitar la búsqueda de productos",
                "b) Evitar reacciones peligrosas entre materiales incompatibles",
                "c) Mejorar la estética del laboratorio",
                "d) Reducir la cantidad de inventario almacenado"
        ],
    ],
        [
            'id'=> 11,
            'text'=> "¿Cuál es el primer paso en el proceso de almacenamiento de productos químicos?",
            'options'=> [
                "a) Segregar los productos por compatibilidad química",
                "b) Organizar los productos por orden alfabético",
                "c) Separar los productos por su estado físico (sólidos, líquidos, gases)",
                "d) Colocar los productos en contenedores secundarios"
        ],
    ],
        [
            'id'=> 12,
            'text'=> "¿Qué representa el pictograma de corrosión en los materiales químicos?",
            'options'=> [
                "a) Que la sustancia es inflamable",
                "b) Que puede causar quemaduras graves en la piel o daños a los metales",
                "c) Que es tóxica si se ingiere",
                "d) Que es un peligro para el medio ambiente"
        ],
    ],
        [
        'id'=> 13,
        'text'=> "¿Cuál es la función principal de las Hojas de Datos de Seguridad (SDS)?",
        'options'=> [
            "a) Proporcionar una lista de precios de los productos químicos",
            "b) Informar sobre las propiedades, peligros y manejo seguro de los productos químicos",
            "c) Identificar el fabricante del producto",
            "d) Organizar los materiales en el laboratorio"
        ],
    ],
        [
        'id'=> 14,
        'text'=> "¿Qué información importante debe incluirse en una SDS relacionada con la exposición personal?",
        'options'=> [
            "a) Solo las propiedades físicas del producto",
            "b) Medidas de primeros auxilios, control de exposición y equipo de protección personal",
            "c) La historia del fabricante",
            "d) Normativas locales sobre transporte"
        ],
    ],
        [
        'id'=> 15,
        'text'=> "¿Qué pictograma alerta sobre el riesgo para el medio ambiente?",
        'options'=> [
            "a) El signo de exclamación",
            "b) El símbolo de un pez y un árbol",
            "c) El pictograma de toxicidad aguda",
            "d) El pictograma de explosivos"
        ],
    ],
        [
        'id'=> 16,
        'text'=> "¿Cuál es el riesgo de almacenar oxidantes junto a materiales combustibles en el laboratorio?",
        'options'=> [
            "a) Ninguno, siempre que estén en diferentes contenedores",
            "b) Puede causar o intensificar incendios",
            "c) Pueden generar gases tóxicos",
            "d) Aumenta la presión en los contenedores"
        ],
    ],
        [
        'id'=> 17,
        'text'=> "¿Qué tipo de contenedores deben utilizarse para almacenar productos químicos que son propensos a derrames o fugas?",
        'options'=> [
            "a) Contenedores sellados herméticamente",
            "b) Contenedores secundarios",
            "c) Contenedores de vidrio grueso",
            "d) Bolsas plásticas gruesas"
        ],
    ],
        [
        'id'=> 18,
        'text'=> "¿Cuál es la distancia mínima recomendada desde el techo para almacenar químicos en laboratorios que NO tienen rociadores?",
        'options'=> [
            "a) 12 pulgadas",
            "b) 24 pulgadas",
            "c) 18 pulgadas",
            "d) 36 pulgadas"
        ],
    ],
        [
        'id'=> 19,
        'text'=> "¿Qué se encontró como uno de los principales problemas durante la inspección de la EPA en 2023 relacionado con los materiales no deseados?",
        'options'=> [
            "a) Uso incorrecto de contenedores plásticos",
            "b) Etiquetas incorrectas, como “residuos” en lugar de “material no deseado”",
            "c) Volumen de materiales excedido en las campanas de extracción",
            "d) Faltaban hojas de datos de seguridad en los laboratorios"
        ],
    ],
        [
        'id'=> 20,
        'text'=> "¿Por qué no es recomendable almacenar productos químicos dentro de las campanas extractoras?",
        'options'=> [
            "a) Porque reduce el espacio para realizar experimentos",
            "b) Porque compromete la eficacia de la campana para proteger al personal de gases y vapores peligrosos",
            "c) Porque puede sobrecargar el sistema de ventilación",
            "d) Porque es más difícil acceder a los productos químicos en una campana extractora"
        ],
    ],
        [
        'id'=> 21,
        'text'=> "¿Qué método debe utilizarse para organizar productos químicos en lugar de orden alfabético?",
        'options'=> [
            "a) Por estado físico",
            "b) Por compatibilidad química",
            "c) Por volumen de contenedores",
            "d) Por temperatura de almacenamiento"
        ],
    ],
        [
        'id'=> 22,
        'text'=> "¿Qué ventaja ofrece el uso de contenedores secundarios al almacenar líquidos peligrosos?",
        'options'=> [
            "a) Facilita el transporte de los químicos",
            "b) Ayuda a contener derrames y minimizar el riesgo de fugas",
            "c) Permite almacenar más productos en menos espacio",
            "d) Mejora la identificación de los químicos almacenados"
        ],
    ],
        [
        'id'=> 23,
        'text'=> "¿Qué tipo de equipos deben usarse para almacenar materiales inflamables como líquidos peligrosos?",
        'options'=> [
            "a) Refrigeradores y congeladores estándar",
            "b) Refrigeradores y congeladores diseñados específicamente para almacenar materiales inflamables",
            "c) Contenedores herméticos sellados al vacío",
            "d) Estanterías ventiladas y cerradas"
        ],
        ]
    ];

    public function show()
    {
        // Load active questions
        $activeQuestionIds = explode("\n", trim(Storage::get('private/active_questions.txt')));
        $activeQuestions = array_filter($this->questions, function ($question) use ($activeQuestionIds) {
            return in_array($question['id'], $activeQuestionIds);
        });

        return view('quiz', ['questions' => $activeQuestions]);
    }

    public function submit(Request $request)
    {
        try {
            $submittedAnswers = $request->input('answers', []);
            $userEmail = $request->input('email'); // User's email from the request
            Log::info('Submitted Answers:', $submittedAnswers);
    
            $correctAnswers = 0;
            $correctAnswerMap = [];
    
            if (Storage::exists('private/answers.txt')) {
                $answersContent = Storage::get('private/answers.txt');
                $answers = explode("\n", trim($answersContent));
    
                foreach ($answers as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) === 2) {
                        $correctAnswerMap[trim($parts[0])] = trim($parts[1]);
                    }
                }
            } else {
                Log::error('answers.txt file does not exist or cannot be accessed.');
                return response()->json(['success' => false, 'error' => 'Answers file not found.'], 500);
            }
    
            Log::info('Parsed Correct Answer Map:', $correctAnswerMap);
    
            $activeQuestionIds = explode("\n", trim(Storage::get('private/active_questions.txt')));
            $totalQuestions = count($activeQuestionIds);
    
            foreach ($activeQuestionIds as $id) {
                $submitted = $submittedAnswers["question$id"] ?? null;
                $correct = $correctAnswerMap[$id] ?? null;
    
                Log::info("Checking Question $id: Submitted = $submitted, Correct = $correct");
    
                if ($submitted !== null && $submitted === $correct) {
                    $correctAnswers++;
                }
            }
    
            $passingScore = ceil($totalQuestions * 0.7);
            $passed = $correctAnswers >= $passingScore;
    
            Log::info('Results:', [
                'Correct' => $correctAnswers,
                'Total' => $totalQuestions,
                'Passed' => $passed ? 'Yes' : 'No',
            ]);
    
            // If the user passed, update their certification status
            if ($passed) {
                $updateResult = $this->updateCertificationStatusInternal($userEmail);
    
                if (!$updateResult['success']) {
                    return response()->json([
                        'success' => false,
                        'error' => $updateResult['message'],
                    ], 500);
                }
            }
    
            return response()->json([
                'success' => true,
                'correctAnswers' => $correctAnswers,
                'totalQuestions' => $totalQuestions,
                'passed' => $passed,
            ]);
        } catch (\Exception $e) {
            Log::error('An error occurred during quiz submission:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    // Internal function to update certification status
    private function updateCertificationStatusInternal($email)
    {
        try {
            if (!$email) {
                return [
                    'success' => false,
                    'message' => 'Email is required.',
                ];
            }
    
            // Find the user by email
            $user = DB::table('user')->where('email', $email)->first();
    
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }
    
            // Update certification status and certification date
            $updated = DB::table('user')
                ->where('email', $email)
                ->update([
                    'certification_status' => true,
                    'certification_date' => now(),
                ]);
    
            if ($updated) {
                return [
                    'success' => true,
                    'message' => 'Certification status updated successfully.',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update certification status.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('An error occurred during certification update:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ];
        }
    }

    public function passQuiz(Request $request)
{
    try {
        $email = $request->input('email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'error' => 'Email is required.',
            ], 400);
        }

        // Update certification status and date
        $updateResult = $this->updateCertificationStatusInternal($email);

        if ($updateResult['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Certification status updated successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $updateResult['message'],
            ], 500);
        }
    } catch (\Exception $e) {
        Log::error('An error occurred while granting a passing grade:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'error' => 'An unexpected error occurred.',
        ], 500);
    }
}

    
}