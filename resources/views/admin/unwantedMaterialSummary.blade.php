@extends('admin.templateAdmin')

@section('title', 'Unwanted Material Summary')

@section('content')
<style>
    .content-area {
        margin-left: 115px; /* Align with sidebar width */
        padding: 1.25rem;
        margin-top: 25px; /* Consistent top margin for alignment */
    }
    .form-label {
        font-weight: bold;
    }
    .table-container {
        margin-top: 20px;
    }
    .btn-primary, .btn-secondary {
        font-weight: bold;
    }
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Unwanted Material Summary</h1>
        <hr class="my-4">
    </div>

    <!-- Date Filters -->
    <div class="row mb-4">
        <div class="col-md-5">
            <label for="fromDate" class="form-label">From</label>
            <input type="date" class="form-control" id="fromDate" required>
            <div class="invalid-feedback">Invalid date range.</div>
        </div>
        <div class="col-md-5">
            <label for="toDate" class="form-label">To</label>
            <input type="date" class="form-control" id="toDate" required>
            <div class="invalid-feedback">Invalid date range.</div>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" id="dateSearchBtn">Search by Date</button>
        </div>
    </div>

    <!-- Chemical Search -->
    <div class="row mb-4">
        <div class="col-md-8">
            <label for="chemicalName" class="form-label">Chemical Name</label>
            <input type="text" class="form-control" id="chemicalName" placeholder="Search by Chemical Name">
            <div class="invalid-feedback">Please enter a valid chemical name (letters only).</div>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-secondary w-100" id="chemicalSearchBtn">Search by Chemical</button>
        </div>
    </div>

    <!-- Filter by Volume or Weight -->
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">Filter By</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="filterType" id="filterVolume" value="volume" checked>
                <label class="form-check-label" for="filterVolume">Volume (L)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="filterType" id="filterWeight" value="weight">
                <label class="form-check-label" for="filterWeight">Weight (Kg)</label>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Label ID</th>
                    <th>Chemical Name</th>
                    <th>Amount</th> <!-- Displays Volume (L) or Weight (Kg) based on filter -->
                </tr>
            </thead>
            <tbody id="chemicalTableBody">
                <!-- Data will be dynamically populated here -->
            </tbody>
        </table>
    </div>

    <!-- Total Volume/Weight -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h5>Total: <span id="totalAmount">0</span> <span id="unitLabel">L</span></h5>
        </div>
    </div>

    <!-- Alert for invalid chemical search -->
    <div class="alert alert-danger mt-4 d-none" id="noChemicalAlert">
        Chemical not found or not allowed.
    </div>
</div>
@endsection


@section('scripts')
<script>
// Extended dummy data with additional fields
const labels = [
    { label_id: 12345, chemical: "Iron", volume: 0, weight: 50, container_size: 10, date: '2024-10-01'},
    { label_id: 67890, chemical: "Iron", volume: 0, weight: 30, container_size: 20, date: '2024-10-03'},
    { label_id: 11223, chemical: "Sodium Chloride", volume: 0, weight: 10, container_size: 15, date: '2024-10-05'},
    { label_id: 44556, chemical: "Hydrochloric Acid", volume: 500, weight: 0, container_size: 5, date: '2024-10-02'},
    { label_id: 77889, chemical: "Water", volume: 1000, weight: 0, container_size: 8, date: '2024-10-06'},
    { label_id: 99112, chemical: "Copper", volume: 0, weight: 75, container_size: 25, date: '2024-10-07'}
];

let currentFilteredData = [];

// Validate date range and filter data
document.getElementById('dateSearchBtn').addEventListener('click', function() {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    
    if (new Date(fromDate) > new Date(toDate)) {
        alert('From date cannot be later than To date.');
        return;
    }

    currentFilteredData = labels.filter(label => {
        return new Date(label.date) >= new Date(fromDate) && new Date(label.date) <= new Date(toDate);
    });

    filterAndPopulateTable(currentFilteredData);
});

// Validate and search by chemical name
document.getElementById('chemicalSearchBtn').addEventListener('click', function() {
    const chemicalName = document.getElementById('chemicalName').value.trim();

    if (!/^[a-zA-Z\s]+$/.test(chemicalName)) {
        document.getElementById('chemicalName').classList.add('is-invalid');
        return;
    } else {
        document.getElementById('chemicalName').classList.remove('is-invalid');
    }

    currentFilteredData = labels.filter(label => label.chemical.toLowerCase() === chemicalName.toLowerCase());

    if (currentFilteredData.length === 0) {
        document.getElementById('noChemicalAlert').classList.remove('d-none');
    } else {
        document.getElementById('noChemicalAlert').classList.add('d-none');
        filterAndPopulateTable(currentFilteredData);
    }
});

// Add change event listeners for the volume and weight filter options
document.getElementById('filterVolume').addEventListener('change', function() {
    filterAndPopulateTable(currentFilteredData);
});
document.getElementById('filterWeight').addEventListener('change', function() {
    filterAndPopulateTable(currentFilteredData);
});

// Filter and populate table based on the current filter type (Volume or Weight)
function filterAndPopulateTable(filteredData) {
    const filterType = document.querySelector('input[name="filterType"]:checked').value;
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = ''; // Clear previous content

    let totalAmount = 0;
    const groupedChemicals = {};

    // Group by chemical name and sum volume/weight
    filteredData.forEach(label => {
        const key = label.label_id;
        if (!groupedChemicals[key]) {
            groupedChemicals[key] = { ...label };
        } else {
            groupedChemicals[key].volume += label.volume;
            groupedChemicals[key].weight += label.weight;
        }
    });

    // Populate table with filtered results
    for (let labelId in groupedChemicals) {
        const labelData = groupedChemicals[labelId];
        const amount = filterType === 'volume' ? labelData.volume : labelData.weight;
        totalAmount += amount;

        const row = `<tr>
                        <td>${labelData.label_id}</td>
                        <td>${labelData.chemical}</td>
                        <td>${amount} ${filterType === 'volume' ? 'L' : 'Kg'}</td>
                     </tr>`;
        tableBody.innerHTML += row;
    }

    // Update total
    document.getElementById('totalAmount').textContent = totalAmount;
    document.getElementById('unitLabel').textContent = filterType === 'volume' ? 'L' : 'Kg';
}
</script>
@endsection
