@extends('professor.templateProfessor')

@section('title', 'About Us - ChemTrack')

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

        /* Content Section Styles */
        .content-section {
            margin: 20px;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
        }
    </style>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- UPRM Banner with Contact Us Text inside -->
        <div class="uprm-portico-banner">
            <img class="uprm-portico-banner-1-icon img-fluid" alt="UPRM Banner" src="{{ asset('photos/UPRM-portico-banner.png') }}">
            <div class="uprm-portico-banner-text">ABOUT US</div>
        </div>

        <!-- Mission, Vision, and About ChemTrack -->
        <div class="content-section">
            <h2>Mission</h2>
            <p>
                The Office of Environmental Health and Safety's mission is to provide excellent services to ensure the health and safety of all employees and students, making sure that safe work and study environments are provided and maintained. Additionally, it aims to maintain and achieve compliance with environmental laws and regulations, as established in the Environmental Policy of the University of Puerto Rico.
            </p>
        </div>

        <div class="content-section">
            <h2>Vision</h2>
            <p>
                Our vision is to make the University of Puerto Rico at Mayagüez a workplace with the highest level of occupational health and safety, promoting the best working conditions for our employees. Additionally, we aim to be a role model in environmental compliance and protection.
            </p>
        </div>

        <div class="content-section">
            <h2>About ChemTrack</h2>
            <p>
                ChemTrack is a webApp designed for tracking, identifying, notifying about orders or pickups, and reporting on unwanted materials at the University of Puerto Rico, Mayagüez Campus, utilized by the EHS office and its laboratories.
            </p>
        </div>
    </div>
@endsection
