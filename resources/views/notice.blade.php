@extends('templateQuiz')  <!-- Extend from templateAdmin -->

@section('title', 'Access Blocked: Training Required')  <!-- Set the page title -->

@section('content')  <!-- Content section to fill in templateAdmin -->
    <!-- Main Container -->
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh; margin-top: 30px; margin-left: 150px;">
        <!-- Card for Notice -->
        <div class="card shadow-lg" style="width: 70%; padding: 40px; background-color: #f8f9fa; border-radius: 15px;">
            <!-- Title Section -->
            <div class="text-center mb-4">
                <h1 class="display-5 text-danger">Access Blocked</h1>
                <h2 class="text-primary">Training Required</h2>
                <hr class="my-4">
            </div>

            <!-- Notice Content Section -->
            <div class="notice-content mb-4" style="line-height: 1.7;">
                <p class="lead">
                    Your access to the ChemTrack web app has been temporarily blocked. This may be because you are a new user and need to complete the required waste management training, or your annual training is due.
                </p>
                <p class="lead">
                    Please complete the training and pass the quiz to regain full access to the platform. Contact the Oficina de Salud, Seguridad Ocupacional y Ambiental (OSSOA) for more details or assistance.
                </p>
            </div>

            <!-- Action Buttons Section -->
            <div class="text-center">
                <!-- Button to Go to Training -->
                <a href="{{route("training")}}" class="btn btn-primary btn-lg mb-2">Go to Training</a>
                <br>
                <!-- Button to Contact OSSOA -->
                <a href="{{route('contactUs')}}" class="btn btn-secondary btn-lg">Contact OSSOA</a>
            </div>
        </div>
    </div>
