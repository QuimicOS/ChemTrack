@extends('templateQuiz')

@section('title', 'Quiz')

@section('content')
    <div class="container" style="max-width: 700px; margin: 80px auto;">
        <!-- Quiz Header -->
        <div class="text-center mb-4">
            <h1 class="display-5">Manejo de Materiales Químicos en los Laboratorios Quiz</h1>
            <hr class="my-4">
        </div>

        <!-- Main Quiz Section -->
        @if (count($activeQuestions ?? []) === 0)
            <!-- If no active questions, grant a passing grade -->
            <div class="alert alert-warning text-center">
                No active questions are available at the moment. You have been granted a passing grade for completing the
                training.
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-success" onclick="grantPassingGrade()">Proceed</button>
            </div>
        @else
            <!-- Display the quiz instructions and questions -->
            <div class="alert alert-info">
                The quiz consists of {{ count($activeQuestions) }} multiple-choice questions. You must answer at least
                {{ ceil(count($activeQuestions) * 0.7) }} questions correctly to pass.
            </div>

            <!-- Quiz Form -->
            <form id="quizForm" method="POST" action="{{ route('quiz.submit') }}">
                @csrf
                <!-- Quiz Questions -->
                <div id="quizQuestions"
                    style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 500px; overflow-y: auto;">
                    @foreach ($activeQuestions as $index => $question)
                        <div class="mb-3">
                            <!-- Display question text -->
                            <label>{{ $index + 1 }}. {{ $question['text'] }}</label>
                            @foreach ($question['options'] as $key => $option)
                                <div class="form-check">
                                    <!-- Display radio buttons for answer options -->
                                    <input type="radio" class="form-check-input" name="question{{ $question['id'] }}"
                                        value="{{ $key }}" required onchange="checkAllAnswered()">
                                    <label class="form-check-label">{{ strtoupper($key) }}. {{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success" id="submitQuizBtn" onclick="submitQuiz()" disabled>Submit
                        Quiz</button>
                </div>
            </form>
        @endif
    </div>

    <!-- Modal to Show Quiz Results -->
    <div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultsModalLabel">Quiz Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal body to display quiz results -->
                <div class="modal-body" id="modalResultContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        //Function to grant passing grade if no active questions are available.
        function grantPassingGrade() {
            const email = "{{ Auth::user()->email }}"; // Get the logged-in user's email

            fetch("{{ route('quiz.pass') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        email: email
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Certification granted successfully! Redirecting...");
                        window.location.href = "{{ route('home') }}"; // Redirect to home page
                    } else {
                        alert("Failed to grant certification. Please contact support.");
                    }
                })
                .catch(error => {
                    alert("An unexpected error occurred. Check the console for details.");
                    console.error(error);
                });
        }

        
        //Function to enable the submit button only when all questions are answered.
        function checkAllAnswered() {
            const totalQuestions = {{ count($activeQuestions ?? []) }}; // Total questions count
            const answeredQuestions = document.querySelectorAll('#quizQuestions .form-check-input:checked')
            .length; // Count answered questions
            document.getElementById('submitQuizBtn').disabled = answeredQuestions <
            totalQuestions; // Enable submit if all questions are answered
        }

        
         //Function to submit the quiz answers to the server.
        function submitQuiz() {
            const formData = new FormData(document.getElementById('quizForm'));
            const answers = {};
            formData.forEach((value, key) => {
                answers[key] = value; // Collect answers from the form
            });

            const email = "{{ Auth::user()->email }}"; // Get logged-in user's email

            fetch("{{ route('quiz.submit') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        answers: answers,
                        email: email
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    const modalContent = document.getElementById('modalResultContent');
                    const resultModal = new bootstrap.Modal(document.getElementById('resultsModal'));

                    if (data.success) {
                        // Display the result in the modal
                        const message = data.passed ?
                            `Congratulations! You passed with ${data.correctAnswers} out of ${data.totalQuestions} correct answers.` :
                            `Sorry, you failed. You answered ${data.correctAnswers} out of ${data.totalQuestions} correctly.`;

                        modalContent.innerHTML = `<p>${message}</p>`;
                        resultModal.show();

                        // Redirect based on result after 3 seconds
                        setTimeout(() => {
                            window.location.href = data.passed ?
                                "{{ route('home') }}" :
                                "{{ route('training') }}";
                        }, 3000);
                    } else {
                        modalContent.innerHTML = `<p>Error: ${data.error || 'An error occurred while grading.'}</p>`;
                        resultModal.show();
                    }
                })
                .catch(error => {
                    alert("An unexpected error occurred. Check the console for details.");
                    console.error(error);
                });
        }

        // Attach the `checkAllAnswered` function to the page load event
        document.addEventListener('DOMContentLoaded', checkAllAnswered);
    </script>
@endsection
