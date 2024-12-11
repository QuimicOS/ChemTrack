@extends('professor.templateProfessor')

@section('title', 'Help - ChemTrack')

@section('content')
    <style>
        .content-area {
            margin-left: 150px;
            padding: 1.25rem;
            margin-top: 20px; /* Push content to be right below the navbar */
        }

        /* UPRM Portico Banner */
        .uprm-portico-banner {
            position: relative;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            padding-top: 1rem;
            max-width: 100%;
            text-align: center;
        }

        .uprm-portico-banner img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Dynamic font size for the banner text */
        .uprm-portico-banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Roboto', sans-serif;
            font-size: 5vw; /* Dynamic scaling based on viewport width */
            min-font-size: 64px; /* Minimum font size */
            max-font-size: 100px; /* Maximum font size */
            color: #fff;
            text-align: center;
        }

        /* Help Box and PDF Viewer */
        .help-box {
            background-color: #a7a7a7;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .pdf-viewer-container {
            width: 95%;  /* Keeps the wider width */
            height: 770px; /* Reduced height by 30% */
            max-width: 900px;
            border: 2px solid #ccc;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto; /* Center the container */
            background-color: white;
        }

        .pdf-viewer {
            width: 100%;  /* Ensures the PDF takes up the entire width */
            height: 100%; /* Ensures the PDF takes up the entire height */
        }
    </style>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- UPRM Banner with Help Text inside -->
        <div class="uprm-portico-banner">
            <img class="uprm-portico-banner-1-icon img-fluid" alt="UPRM Banner" src="{{ asset('photos/UPRM-portico-banner.png') }}">
            <div class="uprm-portico-banner-text">HELP</div>
        </div>

        <!-- Help Box Content -->
        <div class="help-box">
            <h2>User Guide</h2>
            <div class="pdf-viewer-container">
                <!-- Embedded PDF using iframe -->
                <iframe class="pdf-viewer" src="{{ asset('User Guide - Professor and Staff.pdf') }}" frameborder="0"></iframe>
            </div>
        </div>

        <!-- Need More Help Section -->
        <div class="alert alert-info text-center mt-4">
            <h4>Need More Help? <a href="{{ route('professor/contactUs') }}" class="text-decoration-underline">Contact Us!</a></h4>
        </div>
    </div>

@endsection
