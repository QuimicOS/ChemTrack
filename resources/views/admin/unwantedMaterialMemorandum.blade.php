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
                    <th>Container Size</th>
                    <th>Chemical Name</th>
                    <th>Percentage</th>
                    <th>Material State</th>
                    <th>Container Material</th>
                    <th>Container #</th>
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
searchInput.addEventListener('input', function (event) {
    searchInput.value = searchInput.value.replace(/[^0-9]/g, ''); // Remove any non-numeric characters
    toggleSearchButton(); // Re-check button state
});

// Function to toggle the search button based on input validation
function toggleSearchButton() {
    const searchValue = searchInput.value.trim();
    searchButton.disabled = !(searchValue && /^[0-9]+$/.test(searchValue)); // Enable button only if input is non-empty and numeric
}

// Initial toggle check to disable the button on load
toggleSearchButton();

// Search button click event to fetch and search labels
searchButton.addEventListener('click', function () {
    const searchValue = searchInput.value.trim();
    searchInput.value = ''; // Clear search input
    toggleSearchButton(); // Disable the button again after clearing

    // Fetch the label data from the correct API endpoint
    fetch(`/unwanted-material-memorandum?label_id=${searchValue}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Label ID not found');
            }
            return response.json();
        })
        .then(data => {
            if (data.length === 0) {
                alert('Label ID not found');
            } else {
                // Clear previous results
                searchedLabels = [];

                // Populate searched labels with the response data
                data.forEach(item => {
                    searchedLabels.push({
                        label_id: item.label_id,
                        container_size: item.container_size,
                        chemical_name: item.chemical_name,
                        percentage: item.percentage
                    });
                });

                renderTable();
                tableContainer.style.display = 'block'; // Show the table after successful search
            }
        })
        .catch(error => console.error('Error loading label data:', error));
});

// Function to render table with searched labels
function renderTable() {
    const tableBody = document.getElementById('labelTableBody');
    tableBody.innerHTML = ''; // Clear previous rows

    // Populate rows for each searched label with only the required columns
    searchedLabels.forEach(label => {
        const row = `<tr>
                        <td>${label.label_id}</td>
                        <td>${label.container_size}</td>
                        <td>${label.chemical_name}</td>
                        <td>${label.percentage}%</td>
                        <td>${''}</td>
                        <td>${''}</td>
                        <td>${''}</td>
                        <td>${''}</td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
}
</script>


@endsection
