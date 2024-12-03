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
                <h3>Manage Pickup</h3>
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
            <a href="{{ route('admin.manageQuiz.show') }}" class="middle-option">
                <img src="{{ asset('photos/manageQuiz.png') }}" alt="Manage Quiz Image" class="option-image">
                <h3>Manage Quiz</h3>
            </a>
        </div>

        <!-- Dashboard Cards Section (on the right) -->
        <div class="dashboard-cards">
            <div class="dashboard-card labels-last7days">
                <h5>0</h5>
                <p>LABELS CREATED LAST 7 DAYS</p>
            </div>
            <div class="dashboard-card pending-pickup-requests">
                <h5>0</h5>
                <p>PENDING PICKUP REQUESTS</p>
            </div>
            <div class="dashboard-card new-chemicals-last30days">
                <h5>0</h5>
                <p>CHEMICALS ADDED LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card total-weight">
                <h5>0</h5>
                <p>WEIGHT GENERATED (Kilograms) LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card total-volume">
                <h5>0</h5>
                <p>VOLUME GENERATED (Liters) LAST 30 DAYS</p>
            </div>
            <div class="dashboard-card new-members-last30days">
                <h5>0</h5>
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
document.addEventListener('DOMContentLoaded', async function () {
        try {
            // Fetch data from the backend
            const response = await fetch('/chart-data'); // Update with your correct route
            if (!response.ok) throw new Error('Failed to fetch chart data');
            const { labels, volumeData, weightData } = await response.json();

            // Volume Generated Chart
            const volumeCtx = document.getElementById('volumeGeneratedChart').getContext('2d');
            const volumeGeneratedChart = new Chart(volumeCtx, {
                type: 'line',
                data: {
                    labels: labels, // Dynamically fetched labels
                    datasets: [{
                        label: 'Volume Generated',
                        data: volumeData, // Dynamically fetched data
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Volume Generated (Litters) last 30 days',
                            font: {
                                size: 24 // Increase font size for the title
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'L' // Y-axis label
                            }
                        }
                    }
                }
            });

            // Weight Generated Chart
            const weightCtx = document.getElementById('weightGeneratedChart').getContext('2d');
            const weightGeneratedChart = new Chart(weightCtx, {
                type: 'line',
                data: {
                    labels: labels, // Dynamically fetched labels
                    datasets: [{
                        label: 'Weight Generated',
                        data: weightData, // Dynamically fetched data
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Weight Generated (KG) last 30 days',
                            font: {
                                size: 24 // Increase font size for the title
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'KG' // Y-axis label
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error rendering charts:', error);
        }
    });
    </script>

    <!-- AJAX Calls for Dashboard Data Labels7Days -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetchLabelsLast7Days();
            fetchTotalWeight();
            fetchTotalVolume();
            fetchNewMembersLast30Days();
            fetchNewChemicalsLast30Days();
            fetchPendingPickupRequests();
        });

        function fetchLabelsLast7Days() {
        fetch('/labels/last7days')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.labels-last7days h5').textContent = data.label_count;
            })
            .catch(error => console.error('Error fetching labels count:', error));
        }

        // <!-- AJAX Calls for Dashboard Data wightgenerated -->

        function fetchTotalWeight() {
        fetch('/labels/weight')
            .then(response => response.json())
            .then(data => {
                const totalWeightElement = document.querySelector('.total-weight h5');
                if (totalWeightElement) {
                    totalWeightElement.textContent = `${data.total_weight_kg.toFixed(2)} Kg`;
                } else {
                    console.error('Total weight element not found');
                }
            })
            .catch(error => console.error('Error fetching total weight:', error));
        }

        // <!-- AJAX Calls for Dashboard Data volumegenerated -->

        function fetchTotalVolume() {
        fetch('/labels/volume')
            .then(response => response.json())
            .then(data => {
                const totalVolumeElement = document.querySelector('.total-volume h5');
                if (totalVolumeElement) {
                    totalVolumeElement.textContent = `${data.total_volume_liters.toFixed(2)} Liters`;
                } else {
                    console.error('Total volume element not found');
                }
            })
            .catch(error => console.error('Error fetching total volume:', error));
        }





        // <!-- AJAX Calls for Dashboard Data Users generated -->

        function fetchNewMembersLast30Days() {
        fetch('/users/new-members')
        .then(response => response.json())
            .then(data => {
                const newMembersElement = document.querySelector('.new-members-last30days h5');
                if (newMembersElement) {
                    newMembersElement.textContent = data.new_member_count;
                } else {
                    console.error('New members element not found');
                }
            })
            .catch(error => console.error('Error fetching new members count:', error));
        }

        // <!-- AJAX Calls for Dashboard Chemicals generated -->

        function fetchNewChemicalsLast30Days() {
        fetch('/chemicalCreatedCount')
            .then(response => response.json())
            .then(data => {
                const chemicalCountElement = document.querySelector('.new-chemicals-last30days h5');
                if (chemicalCountElement) {
                    chemicalCountElement.textContent = data.chemicals_created_in_the_last_30_days;
                } else {
                    console.error('Chemical count element not found');
                }
            })
            .catch(error => console.error('Error fetching chemical count:', error));
        }


        function fetchPendingPickupRequests() {
        fetch('/pickup-requests/pending')
            .then(response => response.json())
            .then(data => {
                const pickupCountElement = document.querySelector('.pending-pickup-requests h5');
                if (pickupCountElement) {
                    pickupCountElement.textContent = `${data}`;
                } else {
                    console.error('Pickup requests count element not found');
                }
            })
            .catch(error => console.error('Error fetching pending pickup requests count:', error));
        }

    </script>
@endsection
