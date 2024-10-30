@extends('templateQuiz')

@section('title', 'Quiz')

@section('content')
<div class="container" style="max-width: 700px; margin: 80px auto;">
    <div class="text-center mb-4">
        <h1 class="display-5">Waste Management Quiz</h1>
        <hr class="my-4">
    </div>

    <!-- Quiz Instructions -->
    <div class="alert alert-info">
        The quiz consists of 15 multiple-choice questions. You must answer at least 12 questions correctly to pass.
    </div>

    <!-- Quiz Questions Section with white background, compact layout -->
    <div id="quizQuestions" style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 500px; overflow-y: auto;">
        <!-- Dynamic questions will be inserted here -->
    </div>

    <!-- Submit Button -->
    <div class="text-center mt-4">
        <button class="btn btn-success" id="submitQuizBtn" onclick="submitQuiz()" disabled>Submit Quiz</button>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultsModalLabel">Quiz Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalResultContent">
                <!-- Result content will be dynamically inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="handleModalRedirect()">Accept</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Load quiz questions from localStorage and initialize the quiz page
function loadQuizQuestions() {
    const quizQuestions = JSON.parse(localStorage.getItem('quizSettings')) || [];
    const quizQuestionsContainer = document.getElementById('quizQuestions');
    quizQuestionsContainer.innerHTML = ''; // Clear previous content

    // Load exactly 15 active questions
    quizQuestions.filter(q => q.active).slice(0, 15).forEach((question, index) => {
        let optionsHTML = '';
        question.options.forEach((option, optionIndex) => {
            const optionLetter = String.fromCharCode(65 + optionIndex); // A, B, C, D only
            optionsHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="question${question.id}" value="${option}" required onchange="checkAllAnswered()">
                    <label class="form-check-label">${optionLetter}. ${option.slice(3)}</label> <!-- Slice removes "a) ", "b) ", etc. -->
                </div>
            `;
        });

        const questionItem = `
            <div class="mb-3">
                <label>${index + 1}. ${question.text}</label>
                ${optionsHTML}
            </div>
        `;
        quizQuestionsContainer.innerHTML += questionItem;
    });
}

// Function to enable/disable the submit button based on answered questions
function checkAllAnswered() {
    const totalQuestions = document.querySelectorAll('#quizQuestions .form-check-input[type="radio"]:checked').length;
    const submitButton = document.getElementById('submitQuizBtn');
    submitButton.disabled = totalQuestions < 15;
}

// Function to submit the quiz and check answers
function submitQuiz() {
    const quizQuestions = JSON.parse(localStorage.getItem('quizSettings')) || [];
    const totalQuestions = 15;
    let correctAnswers = 0;
    const requiredCorrect = 12;
    const userId = 123; // Simulated user ID for demonstration purposes
    const currentDate = new Date().toLocaleDateString();

    quizQuestions.filter(q => q.active).slice(0, totalQuestions).forEach((question) => {
        const selectedOption = document.querySelector(`input[name="question${question.id}"]:checked`);
        if (selectedOption && selectedOption.value === question.correct) {
            correctAnswers++;
        }
    });

    // Display quiz results in modal
    const modalResultContent = document.getElementById('modalResultContent');
    if (correctAnswers >= requiredCorrect) {
        modalResultContent.innerHTML = `Congratulations! You passed the quiz with ${correctAnswers} out of ${totalQuestions} correct answers.`;
        
        // JSON data for download
        const resultData = {
            userId: userId,
            completionStatus: "1",
            completionDate: currentDate
        };
        const resultBlob = new Blob([JSON.stringify(resultData, null, 2)], { type: 'application/json' });
        const downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(resultBlob);
        downloadLink.download = 'quizResults.json';
        downloadLink.click();

        // Mark success for redirect
        document.getElementById('resultsModal').dataset.result = 'passed';
    } else {
        modalResultContent.innerHTML = `Sorry, you did not pass. You answered ${correctAnswers} out of ${totalQuestions} questions correctly. Please take the training again.`;
        
        // Mark failure for redirect
        document.getElementById('resultsModal').dataset.result = 'failed';
    }

    // Show modal with results
    const resultsModal = new bootstrap.Modal(document.getElementById('resultsModal'));
    resultsModal.show();
}

// Handle modal "Accept" button click for redirection
function handleModalRedirect() {
    const result = document.getElementById('resultsModal').dataset.result;
    if (result === 'passed') {
        window.location.href = '/'; // Redirect to homepage on passing
    } else {
        window.location.href = '/training'; // Redirect to training page on failing
    }
}

// Load the quiz questions when the page loads
document.addEventListener('DOMContentLoaded', loadQuizQuestions);
</script>
@endsection
