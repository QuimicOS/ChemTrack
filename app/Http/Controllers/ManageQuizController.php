<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ManageQuizController extends Controller
{
    //Function that reads quiz questions from a file, filters them based on their status (active or inactive), and structures them into an array.
    private function loadQuestionsFromFile($fileName, $onlyActive = true)
    {
        $questions = [];
        if (Storage::exists($fileName)) {
            $lines = explode("\n", Storage::get($fileName));
            foreach ($lines as $line) {
                // Split each line by the pipe (|) delimiter
                $parts = explode('|', $line);
                // Ensure the line has at least 7 parts (question text, 4 options, correct answer, and status)
                if (count($parts) >= 7) {
                    $status = trim($parts[6]); // Extract status
                    if ($onlyActive && $status !== 'active') {
                        continue;
                    }
                    $questions[] = [
                        'id' => count($questions) + 1,
                        'text' => $parts[0],
                        'options' => [
                            'A' => $parts[1],
                            'B' => $parts[2],
                            'C' => $parts[3],
                            'D' => $parts[4],
                        ],
                        'correct_answer' => $parts[5],
                        'status' => $status,
                    ];
                }
            }
        }
        return $questions;
    }
    


    //Function that reads all quiz questions from a file and passes them to a Blade view for display.
    public function viewQuestions()
    {
        $filePath = storage_path('app/private/questions.txt');
        $questions = [];
    
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
            foreach ($lines as $index => $line) {
                // Split each line by the pipe (|) delimiter
                $parts = explode('|', $line);
    
                if (count($parts) >= 6) {
                    $questions[] = [
                        'id' => $index + 1,
                        'text' => $parts[0],
                        'options' => array_slice($parts, 1, 4),
                        'correct_answer' => $parts[5],
                        'status' => $parts[6] ?? 'inactive', // Default to 'inactive' if not specified
                    ];
                }
            }
        }
    
        return view('admin.manageQuiz', ['questions' => $questions]);
    }
    
    //This function adds a new question to the quiz by appending it to the file.
    public function addQuestion(Request $request)
    {
        $filePath = 'private/questions.txt';
        $newQuestion = implode('|', [
            $request->input('text'),
            $request->input('option_a'),
            $request->input('option_b'),
            $request->input('option_c'),
            $request->input('option_d'),
            $request->input('correct_answer')
        ]);
    
        Storage::append($filePath, $newQuestion);
    
        return redirect()->route('manageQuiz')->with('success', 'Question added successfully.');
    }

    //Function that manages the save settings
    public function saveQuizSettings(Request $request)
    {
        $filePath = storage_path('app/private/questions.txt'); // Path to the questions file
        $questions = [];
    
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
            foreach ($lines as $index => $line) {
                // Split each line by the pipe (|) delimiter
                $parts = explode('|', $line);
                if (count($parts) >= 6) {
                    // Check if the question ID is marked active in the form submission
                    $status = $request->has("questions.{$index}") ? 'active' : 'inactive';
    
                    // Update the status in the parts array
                    if (isset($parts[6])) {
                        $parts[6] = $status;
                    } else {
                        $parts[] = $status;
                    }
    
                    $questions[] = implode('|', $parts);
                }
            }
    
            // Save updated questions back to the file
            file_put_contents($filePath, implode("\n", $questions));
        }
    
        return redirect()->route('admin.manageQuiz.show')->with('success', 'Quiz settings updated successfully.');
    }
    
    
}