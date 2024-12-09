<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class QuizController extends Controller
{
    //Function that reads quiz questions from a file, filters them based on their status (active or inactive), and structures them into an array.
    private function loadQuestionsFromFile($filePath, $onlyActive = true)
    {
        $questions = [];

        if (!file_exists($filePath)) { // Use file_exists for absolute path
            return [];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Load file contents    
        foreach ($lines as $line) {
            // Split each line by the pipe (|) delimiter
            $parts = explode('|', $line);
            // Ensure the line has at least 7 parts (question text, 4 options, correct answer, and status)
            if (count($parts) >= 7) {
                $status = trim($parts[6]);
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
        return $questions;
    }


    //Function that shows the quiz with the active questions 
    public function show()
    {
        $filePath = storage_path('app/private/questions.txt'); // Define the absolute path

        $questions = $this->loadQuestionsFromFile($filePath, true); // Load only active questions

        return view('quiz', ['activeQuestions' => $questions]);
    }

    //Function that handles the submit and results logic
    public function submit(Request $request)
    {
        try {
            $filePath = storage_path('app/private/questions.txt'); // Define the absolute path

            $submittedAnswers = $request->input('answers', []);

            $correctAnswers = 0;

            // Load active questions with their answers
            $questions = $this->loadQuestionsFromFile($filePath, true); // Load only active questions    
            // Process each question and compare answers
            foreach ($questions as $question) {
                $submittedAnswer = strtoupper($submittedAnswers['question' . $question['id']] ?? '');
                $correctAnswer = strtoupper($question['correct_answer']);

                // Increment correct answers count if the answer matches
                if ($submittedAnswer === $correctAnswer) {
                    $correctAnswers++;
                }
            }

            // Determine grading
            $totalQuestions = count($questions);
            $passingScore = ceil($totalQuestions * 0.7); // 70% passing score
            $passed = $correctAnswers >= $passingScore;

            // Update certification if passed
            if ($passed) {
                $email = $request->input('email');
                if ($email) {
                    $updated = DB::table('user')
                        ->where('email', $email)
                        ->update([
                            'certification_status' => true,
                            'certification_date' => now(),
                        ]);

                    if ($updated) {
                        //Log::info("Certification status updated successfully for email: {$email}");
                    } else {
                        //Log::error("Failed to update certification status for email: {$email}");
                    }
                } else {
                    //Log::error("Email not provided for certification update.");
                }
            }

            return response()->json([
                'success' => true,
                'correctAnswers' => $correctAnswers,
                'totalQuestions' => $totalQuestions,
                'passed' => $passed,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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

            $user = DB::table('user')->where('email', $email)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

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
            return [
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ];
        }
    }

    //Function that updates certification status for a user
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
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred.',
            ], 500);
        }
    }
}
