@extends('professor/templateProfessor')

@section('title', 'Professor Dashboard - ChemTrack')

@section('content')
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
    <style>
        body {
            background-color: #E4E4E4;
            font-size: 1rem;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Content area */
        .content-area {
            padding: 1.25rem;
            margin-top: 20px;
            margin-left: 175px;
        }

        /* Middle options styling */
        .middle-options {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
        }

        .middle-option {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            font-size: 16px;
            width: 23%;
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-decoration: none;
        }

        .option-image {
            background-color: #ccc;
            width: 140px;
            height: 140px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .middle-option h3 {
            font-size: 18px;
            margin-top: 10px;
        }

        .middle-option:hover {
            background-color: #0056b3;
        }
    </style>

    <!-- Main Content -->
    <div class="content-area container">
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="display-5">Professor Dashboard</h1>
            <hr class="my-4">
        </div>

        <!-- Middle Options -->
        <div class="middle-options">
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/searchLabel.png') }}" alt="Search Label Image" class="option-image">
                <h3>Search Label</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/createLabel.png') }}" alt="Create Label Image" class="option-image">
                <h3>Create Label</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/editLabel.png') }}" alt="Edit Label Image" class="option-image">
                <h3>Edit Label</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/invalidLabel.png') }}" alt="Invalidate Label Image" class="option-image">
                <h3>Invalidate Label</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/pickupRequest.png') }}" alt="Pickup Request Image" class="option-image">
                <h3>Pickup Request</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/invalidRequest.png') }}" alt="Invalidate Pickup Request Image" class="option-image">
                <h3>Invalidate Pickup Request</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/manageChemicals.png') }}" alt="Manage Chemicals Image" class="option-image">
                <h3>Add Chemical</h3>
            </a>
            <a href="#" class="middle-option">
                <img src="{{ asset('photos/manageRoles.png') }}" alt="Role Management Image" class="option-image">
                <h3>User Request</h3>
            </a>
        </div>
    </div>
@endsection
