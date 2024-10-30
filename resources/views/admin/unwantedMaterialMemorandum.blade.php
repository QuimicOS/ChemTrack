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
            <button class="btn btn-secondary w-100" id="searchLabelBtn">Search</button>
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

// Function to handle searching for labels
document.getElementById('searchLabelBtn').addEventListener('click', function() {
    const searchValue = document.getElementById('searchLabel').value.trim();

    // Clear search input
    document.getElementById('searchLabel').value = '';

    if (!searchValue) return;

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

// Load any necessary initial data if needed here (optional)
</script>
@endsection
