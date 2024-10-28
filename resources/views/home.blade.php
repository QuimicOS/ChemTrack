@extends('template')

@section('title', 'ChemTrack')

@section('content')
    <style>
        .hero-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.25rem;
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 2rem;
        }

        .hero-left {
            flex: 1;
            text-align: center;
        }

        .hero-left img {
            margin: 0 auto 1rem auto;
            display: block;
        }

        .hero-left .btn {
            margin-top: 1rem;
        }

        .hero-right {
            flex: 1;
            text-align: center;
        }

        .gray-box {
            background-color: #f0f0f0;
            padding: 1.25rem;
            border-radius: 10px;
            text-align: center;
            font-size: 1rem;
        }

        .ossoa-image {
            max-width: 100%;
            border-radius: 10px;
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
            <div class="hero-left">
                <img class="logochemtrack-transparent-1-icon img-fluid mb-3" alt="ChemTrack Logo" src="{{ asset('photos/Logo ChemTrack.jpg') }}">
                <div class="chemtrack-its-a">
                    ChemTrack is a webApp designed for tracking, identifying, notifying about orders or pickups, and reporting on unwanted materials at the University of Puerto Rico, Mayagüez Campus.
                </div>
                <a href="{{ url('contactUs') }}" class="btn btn-primary">See More</a>
            </div>

            <div class="hero-right">
                <img class="ossoa-image img-fluid" src="{{ asset('photos/ossoa logo.png') }}" alt="OSSOA Logo">
            </div>
        </div>
    </div>
</div>
@endsection
