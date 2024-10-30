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
            According to EPA regulations (40 CFR § 265.16), all laboratory personnel must undergo proper training to manage hazardous waste. This ensures compliance with safe handling, waste disposal, and emergency response protocols.
            <br><br>
            Please review the provided training materials below to proceed. Completion of this training and passing the quiz are required for full access to ChemTrack. For assistance, contact OSSOA at the provided email below.
        </p>

        <!-- Embedded PDF Viewer -->
        <div class="text-center my-4">
            <h4 class="mb-3">Training Materials</h4>
            <iframe src="{{ asset('training/Safety.mp4') }}" width="100%" height="600" style="border: none;"></iframe>
            <p class="mt-2">Review the embedded PowerPoint to complete the training.</p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center mt-4">
            <a href="{{ route('quiz') }}" class="btn btn-primary me-3">Take Quiz</a>
        </div>
    </div>
</div>
@endsection
