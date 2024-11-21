@extends('templateQuiz')

@section('title', 'Waste Management Training')

@section('content')
<div class="container" style="margin-top: 80px; margin-left: 150px;">
    <!-- Title Section -->
    <div class="text-center mb-4">
        <h1 class="display-5">Waste Management Training</h1>
        <hr class="my-4">
    </div>

    <!-- Information Card -->
    <div class="card shadow-lg p-4">
        <h3 class="text-center mb-3">EPA Compliance Training</h3>
        <p class="text-center">
            According to EPA regulations (40 CFR ยง 265.16), all laboratory personnel must undergo proper training to manage hazardous waste. This ensures compliance with safe handling, waste disposal, and emergency response protocols.
            <br><br>
            Please review the provided training materials below to proceed. Completion of this training and passing the quiz are required for full access to ChemTrack. For assistance, contact OSSOA at the provided email below.
        </p>

        <!-- Training Video -->
        <div class="text-center my-4">
            <h4 class="mb-3">Training Video</h4>
            <video id="trainingVideo" width="100%" height="600" controls>
                <source src="{{ asset('training/Safety.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <p class="mt-2">Watch the entire video to enable the "Take Quiz" button.</p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center mt-4">
            <button id="takeQuizBtn" class="btn btn-primary" disabled>Take Quiz</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('trainingVideo');
        const takeQuizBtn = document.getElementById('takeQuizBtn');

        // Ensure button is initially disabled
        takeQuizBtn.disabled = true;

        // Enable button when the video ends
        video.onended = function() {
            takeQuizBtn.disabled = false;
        };

        // Add click event to redirect when enabled
        takeQuizBtn.addEventListener('click', function () {
            if (!takeQuizBtn.disabled) {
                window.location.href = "{{ route('quiz.show') }}";
                }
        });
    });
</script>
@endsection