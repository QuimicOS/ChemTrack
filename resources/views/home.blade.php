@extends('template')

@section('title', 'ChemTrack')

@section('content')
    <style>
        .hero-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 1.25rem;
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 2rem;
        }

        .gray-box {
            background-color: #f0f0f0;
            padding: 1.25rem;
            border-radius: 10px;
            text-align: center;
            font-size: 1rem;
        }

        .chemtrack-its-a {
            text-align: center;
            font-size: 1.1rem;
        }

        .ossoa-image {
            max-width: 40%;
            border-radius: 10px;
            margin-bottom: 1.5rem; /* Adds spacing below the image */
        }

        .content-area {
            margin-left: 125px;
            padding: 1.25rem;
        }
    </style>

<div class="container-fluid home">
    <!-- Main Content Area -->
    <div class="content-area">
        <!-- UPRM Banner with ChemTrack text embedded inside -->
        <div class="uprm-portico-banner position-relative">
            <img class="uprm-portico-banner-1-icon img-fluid" alt="UPRM Banner" src="{{ asset('photos/UPRM-portico-banner.png') }}">
            <div class="uprm-portico-banner-text">CHEMTRACK</div>
        </div>

        <!-- Hero Section with two columns: left (text and centered logo) and right (OSSOA image) -->
        <div class="hero-container">
            <img class="ossoa-image img-fluid" src="{{ asset('photos/ossoa logo.png') }}" alt="OSSOA Logo">
            <div class="chemtrack-its-a">
                ChemTrack is a webApp designed for tracking, identifying, notifying about orders or pickups, and reporting on unwanted materials at the University of Puerto Rico, Mayagüez Campus.
            </div>
        </div>
    </div>
</div>
@endsection
