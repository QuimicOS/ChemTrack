@extends('admin.templateAdmin')

@section('title', 'Unwanted Material Memorandum')

@section('content')
<style>
    .content-area {
        margin-left: 120px; /* Aligns with sidebar width */
        padding: 1.25rem;
        margin-top: 25px; /* Uniform top margin */
    }
    .table-container {
        margin-top: 20px;
        display: none; /* Hide table initially */
    }
    .form-label {
        font-weight: bold;
    }
    .btn-secondary {
        font-weight: bold;
    }
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Unwanted Material Memorandum</h1>
        <hr class="my-4">
    </div>

    <!-- Search Section for Labels -->
    <div class="row mb-5">
        <div class="col-md-10">
            <label for="searchLabel" class="form-label">Search Label by ID</label>
            <input type="text" class="form-control" id="searchLabel" placeholder="Enter Label ID">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" id="searchLabelBtn">Search</button>
        </div>
    </div>

    <!-- Table Section (Memorandum Table) -->
    <div class="table-container">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Label ID</th>
                    <th>Container #</th>
                    <th>Chemical Name</th>
                    <th>Material State</th>
                    <th>Container Material</th>
                    <th>Container Capacity</th>
                    <th>pH</th>
                </tr>
            </thead>
            <tbody id="labelTableBody">
                <!-- Rows will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>
@endsection


@section('scripts')
<script>
// Array to store the searched labels for display in the table
let searchedLabels = [];

// Get references to input and button elements
const searchInput = document.getElementById('searchLabel');
const searchButton = document.getElementById('searchLabelBtn');
const tableContainer = document.querySelector('.table-container'); // Table container to control its display

// Function to enforce only numbers in the search input field
searchInput.addEventListener('input', function(event) {
    // Remove any non-numeric characters from the input value
    searchInput.value = searchInput.value.replace(/[^0-9]/g, '');
    toggleSearchButton(); // Re-check button state
});

// Function to toggle the search button based on input validation
function toggleSearchButton() {
    const searchValue = searchInput.value.trim();
    // Enable button only if searchValue is non-empty and contains only digits
    searchButton.disabled = !(searchValue && /^[0-9]+$/.test(searchValue));
}

// Attach input event listener to search input field
searchInput.addEventListener('input', toggleSearchButton);

// Initial toggle check to disable the button on load
toggleSearchButton();

// Search button click event to fetch and search labels
searchButton.addEventListener('click', function() {
    const searchValue = searchInput.value.trim();

    // Clear search input
    searchInput.value = '';
    toggleSearchButton(); // Disable the button again after clearing

    // Fetch and search label data from the JSON file
    fetch('/json/labelM.json')
        .then(response => response.json())
        .then(data => {
            // Find the label by ID
            const label = data.find(l => l.label_id === searchValue);
            if (label) {
                // Add found label to the searchedLabels array
                searchedLabels.push(label);
                renderTable();
                tableContainer.style.display = 'block'; // Show the table after successful search
            } else {
                alert('Label ID not found');
            }
        })
        .catch(error => console.error('Error loading labels:', error));
});

// Function to render table with searched labels
function renderTable() {
    const tableBody = document.getElementById('labelTableBody');
    tableBody.innerHTML = ''; // Clear previous rows

    // Populate rows for each searched label
    searchedLabels.forEach(label => {
        const row = `<tr>
                        <td>${label.label_id}</td>
                        <td></td> <!-- Empty cell for Container # -->
                        <td>${label.chemical_name}</td>
                        <td></td> <!-- Empty cell for Material State -->
                        <td></td> <!-- Empty cell for Container Material -->
                        <td>${label.container_capacity}</td>
                        <td></td> <!-- Empty cell for pH -->
                    </tr>`;
        tableBody.innerHTML += row;
    });
}
</script>
@endsection
