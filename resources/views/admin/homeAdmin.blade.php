@extends('admin/templateAdmin')

@section('title', 'Admin Dashboard - ChemTrack')

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
        height: 100%;
        overflow-x: hidden;
    }

    /* Content area */
    .content-area {
        margin-left: 125px;
        padding: 1.25rem;
        margin-top: 20px;
    }

    /* Middle options styling */
    .middle-options {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 10px; /* Reduced gap for closer alignment */
        width: 75%; /* Adjusted to fit 4 cards per row */
        float: left;
    }

    .middle-option {
        background-color: #007bff;
        color: #fff;
        text-align: center;
        padding: 15px; /* Reduced padding for more compact design */
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        font-size: 16px;
        width: 22%; /* Adjust width for alignment */
        height: 150px; /* Adjusted height to fit content */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-decoration: none;
    }

    .option-image {
        background-color: #ccc;
        width: 150px; /* Same size for all images */
        height: 80px; 
        margin: 5px 0; /* Reduced margin for spacing */
        border-radius: 5px;
    }

    .middle-option h3 {
        font-size: 18px;
        margin-top: 5px;
    }

    .middle-option:hover {
        background-color: #0056b3;
    }

    /* Dashboard Cards styling */
    .dashboard-cards {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 220px; /* Adjusted width for alignment */
        float: left; /* Align to the left to make it closer to the middle options */
        margin-left: 40px; /* Reduced margin to bring it closer */
    }

    .dashboard-card {
        background-color: #fff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .dashboard-card h5 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    /* Graph section styling */
    .graphs-container {
        margin-top: 120px;
        display: flex;
        justify-content: center;
        gap: 20px;
        clear: both;
    }

    .graph {
        width: 45%;
    }
    </style>

    <!-- Main Content -->
    <div class="content-area container">
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="display-5">Admin Dashboard</h1>
            <hr class="my-4">
        </div>

        <!-- Middle Options -->
        <div class="middle-options">
            <a href="{{ route('admin/searchLabel') }}" class="middle-option">
                <img src="{{ asset('photos/searchLabel.png') }}" alt="Search Label Image" class="option-image">
                <h3>Search Label</h3>
            </a>
            <a href="{{ route('admin/createLabel') }}" class="middle-option">
                <img src="{{ asset('photos/createLabel.png') }}" alt="Create Label Image" class="option-image">
                <h3>Create Label</h3>
            </a>
            <a href="{{ route('admin/editLabel') }}" class="middle-option">
                <img src="{{ asset('photos/editLabel.png') }}" alt="Edit Label Image" class="option-image">
                <h3>Edit Label</h3>
            </a>
            <a href="{{ route('admin/invalidLabel') }}" class="middle-option">
                <img src="{{ asset('photos/invalidLabel.png') }}" alt="Invalidate Label Image" class="option-image">
                <h3>Invalidate Label</h3>
            </a>
            <a href="{{ route('admin/pickupRequest') }}" class="middle-option">
                <img src="{{ asset('photos/pickupRequest.png') }}" alt="Pickup Request Image" class="option-image">
                <h3>Pickup Request</h3>
            </a>
            <a href="{{ route('admin/invalidPickup') }}" class="middle-option">
                <img src="{{ asset('photos/invalidRequest.png') }}" alt="Invalidate Pickup Request Image" class="option-image">
                <h3>Invalidate Pickup</h3>
            </a>
            <a href="{{ route('admin/pickupHistorial') }}" class="middle-option">
                <img src="{{ asset('photos/PickupHistorial.png') }}" alt="Pickup Historial Image" class="option-image">
                <h3>Pickup Historial</h3>
            </a>
            <a href="{{ route('admin/manageChemical') }}" class="middle-option">
                <img src="{{ asset('photos/manageChemicals.png') }}" alt="Manage Chemicals Image" class="option-image">
                <h3>Manage Chemical</h3>
            </a>
            <a href="{{ route('admin/roleManagement') }}" class="middle-option">
                <img src="{{ asset('photos/manageRoles.png') }}" alt="Role Management Image" class="option-image">
                <h3>Role Management</h3>
            </a>
            <a href="{{ route('admin/unwantedMaterialSummary') }}" class="middle-option">
                <img src="{{ asset('photos/Summary.png') }}" alt="Unwanted Material Summary Image" class="option-image">
                <h3>UW Summary</h3>
            </a>
            <a href="{{ route('admin/unwantedMaterialMemorandum') }}" class="middle-option">
                <img src="{{ asset('photos/memorandum.png') }}" alt="Unwanted Material Memorandum Image" class="option-image">
                <h3>UW Memorandum</h3>
            </a>
            <a href="{{ route('admin/manageLaboratories') }}" class="middle-option">
                <img src="{{ asset('photos/manageLabs.png') }}" alt="Manage Laboratories Image" class="option-image">
                <h3>Manage Laboratories</h3>
            </a>
            <a href="{{ route('admin/manageQuiz') }}" class="middle-option">
                <img src="{{ asset('photos/manageQuiz.png') }}" alt="Manage Quiz Image" class="option-image">
                <h3>Manage Quiz</h3>
            </a>
        </div>

        <!-- Dashboard Cards Section (on the right) -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
              <h5>32</h5>
              <p>LABELS CREATED LAST 7 DAYS</p>
            </div>
            <div class="dashboard-card">
              <h5>12</h5>
              <p>PENDING PICKUP REQUESTS</p>
            </div>
            <div class="dashboard-card">
              <h5>8</h5>
              <p>CHEMICALS ADDED LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card">
              <h5>27</h5>
              <p>WEIGHT GENERATED (LBS) LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card">
              <h5>43</h5>
              <p>VOLUME GENERATED (GAL) LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card">
              <h5>17</h5>
              <p>NEW MEMBERS LAST 30 DAYS</p>
            </div>
        </div>

        <!-- Graphs Section -->
        <div class="graphs-container">
            <div class="graph">
                <canvas id="volumeGeneratedChart"></canvas>
            </div>
            <div class="graph">
                <canvas id="weightGeneratedChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Chart.js Script for generating the charts -->
    <script>
        const volumeCtx = document.getElementById('volumeGeneratedChart').getContext('2d');
        const volumeGeneratedChart = new Chart(volumeCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Volume Generated (Gal) last 30 days',
                    data: [5, 8, 4, 10],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const weightCtx = document.getElementById('weightGeneratedChart').getContext('2d');
        const weightGeneratedChart = new Chart(weightCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Weight Generated (Lbs) last 30 days',
                    data: [50, 60, 40, 80],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
