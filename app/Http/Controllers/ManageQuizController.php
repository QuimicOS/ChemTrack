<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ManageQuizController extends Controller
{
    private $questions = [
        ['id' => 1, 'text' => '¿Cuál es el objetivo principal del entrenamiento de seguridad en el manejo de materiales químicos en los laboratorios del RUM?'],
        ['id' => 2, 'text' => '¿Qué normativa regula la exposición a químicos peligrosos en los laboratorios según OSHA?'],
        ['id' => 3, 'text' => '¿Qué se debe hacer si en un laboratorio se acumula el máximo de galones de materiales no deseados?'],
        ['id' => 4, 'text' => '¿Cuál de las siguientes es una de las mejores prácticas recomendadas en el Plan de Manejo de Laboratorio?'],
        ['id' => 5, 'text' => '¿Qué información debe incluirse en el etiquetado de los materiales no deseados?'],
        ['id' => 6, 'text' => '¿Cuál es el límite de tiempo que los materiales no deseados pueden permanecer en un laboratorio antes de ser retirados?'],
        ['id' => 7, 'text' => '¿Qué volumen máximo de materiales no deseados puede acumularse en un laboratorio antes de que deban ser retirados en 10 días?'],
        ['id' => 8, 'text' => '¿Qué recomendación se debe seguir al almacenar sustancias corrosivas o especialmente peligrosas (PHS)?'],
        ['id' => 9, 'text' => '¿Qué se encontró durante la inspección de la EPA en marzo de 2023 relacionado con el almacenamiento de materiales?'],
        ['id' => 10, 'text' => '¿Cuál es la principal razón para segregar los materiales químicos en el laboratorio?'],
        ['id' => 11, 'text' => '¿Cuál es el primer paso en el proceso de almacenamiento de productos químicos?'],
        ['id' => 12, 'text' => '¿Qué representa el pictograma de corrosión en los materiales químicos?'],
        ['id' => 13, 'text' => '¿Cuál es la función principal de las Hojas de Datos de Seguridad (SDS)?'],
        ['id' => 14, 'text' => '¿Qué información importante debe incluirse en una SDS relacionada con la exposición personal?'],
        ['id' => 15, 'text' => '¿Qué pictograma alerta sobre el riesgo para el medio ambiente?'],
        ['id' => 16, 'text' => '¿Cuál es el riesgo de almacenar oxidantes junto a materiales combustibles en el laboratorio?'],
        ['id' => 17, 'text' => '¿Qué tipo de contenedores deben utilizarse para almacenar productos químicos que son propensos a derrames o fugas?'],
        ['id' => 18, 'text' => '¿Cuál es la distancia mínima recomendada desde el techo para almacenar químicos en laboratorios que NO tienen rociadores?'],
        ['id' => 19, 'text' => '¿Qué se encontró como uno de los principales problemas durante la inspección de la EPA en 2023 relacionado con los materiales no deseados?'],
        ['id' => 20, 'text' => '¿Por qué no es recomendable almacenar productos químicos dentro de las campanas extractoras?'],
        ['id' => 21, 'text' => '¿Qué método debe utilizarse para organizar productos químicos en lugar de orden alfabético?'],
        ['id' => 22, 'text' => '¿Qué ventaja ofrece el uso de contenedores secundarios al almacenar líquidos peligrosos?'],
        ['id' => 23, 'text' => '¿Qué tipo de equipos deben usarse para almacenar materiales inflamables como líquidos peligrosos?'],
    ];

    public function show()
    {
        // Load active question IDs
        $activeQuestionIds = [];
        if (Storage::exists('private/active_questions.txt')) {
            $activeQuestionIds = explode("\n", trim(Storage::get('private/active_questions.txt')));
        }

        return view('admin.manageQuiz', [
            'questions' => $this->questions,
            'activeQuestionIds' => $activeQuestionIds,
        ]);
    }

    public function save(Request $request)
    {
        $activeQuestions = $request->input('active_questions', []);
        Storage::put('private/active_questions.txt', implode("\n", $activeQuestions));

        return response()->json(['success' => true]);
    }
}