@extends('templateQuiz')

@section('title', 'Quiz')

@section('content')
<div class="container" style="max-width: 700px; margin: 80px auto;">
    <div class="text-center mb-4">
        <h1 class="display-5">Waste Management Quiz</h1>
        <hr class="my-4">
    </div>

    <div class="alert alert-info">
        The quiz consists of {{ count($questions) }} multiple-choice questions. You must answer at least {{ ceil(count($questions) * 0.7) }} questions correctly to pass.
    </div>

    <form id="quizForm" method="POST" action="{{ route('quiz.submit') }}">
        @csrf
        <div id="quizQuestions" style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 500px; overflow-y: auto;">
            @foreach ($questions as $index => $question)
            <div class="mb-3">
                <label>{{ $index + 1 }}. {{ $question['text'] }}</label>
                @foreach ($question['options'] as $optionIndex => $option)
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="question{{ $question['id'] }}" value="{{ chr(97 + $optionIndex) }}" required onchange="checkAllAnswered()">
                    <label class="form-check-label">{{ chr(65 + $optionIndex) }}. {{ $option }}</label>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-success" id="submitQuizBtn" onclick="submitQuiz()" disabled>Submit Quiz</button>
        </div>
    </form>
</div>

<div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultsModalLabel">Quiz Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
    function checkAllAnswered() {
        const totalQuestions = {{ count($questions) }};
        const answeredQuestions = document.querySelectorAll('#quizQuestions .form-check-input[type="radio"]:checked').length;
        document.getElementById('submitQuizBtn').disabled = answeredQuestions < totalQuestions;
    }

    function submitQuiz() {
    const formData = new FormData(document.getElementById('quizForm'));
    const answers = {};
    formData.forEach((value, key) => {
        answers[key] = value;
    });

    const email = "{{ Auth::user()->email }}"; // Fetch the logged-in user's email

    fetch("{{ route('quiz.submit') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ answers: answers, email: email }),
    })
        .then(response => response.json())
        .then(data => {
            const resultModal = new bootstrap.Modal(document.getElementById('resultsModal'));
            const modalContent = document.getElementById('modalResultContent');

            if (data.success) {
                const message = data.passed 
                    ? `Congratulations! You passed with ${data.correctAnswers} out of ${data.totalQuestions} correct answers.` 
                    : `Sorry, you failed. You answered ${data.correctAnswers} out of ${data.totalQuestions} correctly.`;

                modalContent.innerHTML = `<p>${message}</p>`;
                resultModal.show();

                // Redirect based on result after a delay
                setTimeout(() => {
                    window.location.href = data.passed 
                        ? "{{ route('home') }}" 
                        : "{{ route('training') }}";
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



    document.addEventListener('DOMContentLoaded', checkAllAnswered);
</script>
@endsection