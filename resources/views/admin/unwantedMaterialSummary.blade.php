@extends('admin.templateAdmin')

@section('title', 'Unwanted Material Summary')

@section('content')
<style>
    .content-area {
        margin-left: 115px;
        padding: 1.25rem;
        margin-top: 25px;
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
    fieldset {
        border: 1px solid #ccc;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background-color: #f8f9fa;
    }
    legend {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 0 0.5rem;
    }
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Unwanted Material Summary</h1>
        <hr class="my-4">
    </div>

    <!-- Date Filters Fieldset -->
    <fieldset>
        <legend>Date Range Filter</legend>
        <div class="row mb-4">
            <div class="col-md-5">
                <label for="fromDate" class="form-label">From</label>
                <input type="date" class="form-control" id="fromDate" required>
            </div>
            <div class="col-md-5">
                <label for="toDate" class="form-label">To</label>
                <input type="date" class="form-control" id="toDate" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="dateSearchBtn" disabled>Search by Date</button>
            </div>
        </div>
    </fieldset>

    <!-- Chemical Search and Table Fieldset -->
    <fieldset>
        <legend>Chemical Search and Summary</legend>

        <!-- Chemical Search -->
        <div class="row mb-4">
            <div class="col-md-8">
                <label for="chemicalName" class="form-label">Chemical Name</label>
                <input type="text" class="form-control" id="chemicalName" placeholder="Search by Chemical Name">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-secondary w-100" id="chemicalSearchBtn" disabled>Search by Chemical</button>
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
        <div class="table-container d-none" id="resultsTableContainer">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Chemical Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="chemicalTableBody">
                    <!-- Data will be dynamically populated here -->
                </tbody>
            </table>
        </div>

        <!-- Total Volume/Weight -->
        <div class="row mt-4 d-none" id="totalAmountContainer">
            <div class="col-md-6">
                <h5>Total: <span id="totalAmount">0</span> <span id="unitLabel">L</span></h5>
            </div>
        </div>

        <!-- Alert for invalid chemical search -->
        <div class="alert alert-danger mt-4 d-none" id="noChemicalAlert">
            Chemical not found or not allowed.
        </div>
    </fieldset>
</div>
@endsection

@section('scripts')
<script>
// Dummy data for initial table view
const labels = [
    { chemical: "Iron", quantity: 10, units: "Kg", date: '2024-10-01' },
    { chemical: "Iron", quantity: 5, units: "Kg", date: '2024-10-03' },
    { chemical: "Sodium Chloride", quantity: 20, units: "Kg", date: '2024-10-05' },
    { chemical: "Hydrochloric Acid", quantity: 1, units: "L", date: '2024-10-02' },
    { chemical: "Water", quantity: 15, units: "L", date: '2024-10-06' },
    { chemical: "Copper", quantity: 3, units: "Kg", date: '2024-10-07' }
];

let currentFilteredData = [];

// Enable "Search by Date" button when both dates are selected and valid
const fromDateField = document.getElementById('fromDate');
const toDateField = document.getElementById('toDate');
const dateSearchBtn = document.getElementById('dateSearchBtn');

function toggleDateSearchButton() {
    const fromDate = fromDateField.value;
    const toDate = toDateField.value;
    dateSearchBtn.disabled = !(fromDate && toDate && new Date(fromDate) <= new Date(toDate));
}

fromDateField.addEventListener('input', toggleDateSearchButton);
toDateField.addEventListener('input', toggleDateSearchButton);

// Date range search
dateSearchBtn.addEventListener('click', function() {
    const fromDate = fromDateField.value;
    const toDate = toDateField.value;

    currentFilteredData = labels.filter(label => 
        new Date(label.date) >= new Date(fromDate) && new Date(label.date) <= new Date(toDate)
    );

    filterAndPopulateTable(currentFilteredData);
});

// Enable "Search by Chemical" button when chemical name input is non-empty
const chemicalNameField = document.getElementById('chemicalName');
const chemicalSearchBtn = document.getElementById('chemicalSearchBtn');

function toggleChemicalSearchButton() {
    chemicalSearchBtn.disabled = !chemicalNameField.value.trim();
}

chemicalNameField.addEventListener('input', toggleChemicalSearchButton);

// Chemical name search with case-insensitive match
chemicalSearchBtn.addEventListener('click', function() {
    const chemicalName = chemicalNameField.value.trim().toLowerCase();

    currentFilteredData = labels.filter(label => 
        label.chemical.toLowerCase() === chemicalName
    );

    if (currentFilteredData.length === 0) {
        document.getElementById('noChemicalAlert').classList.remove('d-none');
        document.getElementById('resultsTableContainer').classList.add('d-none');
        document.getElementById('totalAmountContainer').classList.add('d-none');
    } else {
        document.getElementById('noChemicalAlert').classList.add('d-none');
        filterAndPopulateTable(currentFilteredData);
    }
});


// Toggle table display and filter data based on selected unit (volume or weight)
function filterAndPopulateTable(filteredData) {
    const selectedUnit = document.querySelector('input[name="filterType"]:checked').value === 'volume' ? 'L' : 'Kg';
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = '';

    let totalAmount = 0;
    const chemicalSums = {};

    filteredData.forEach(label => {
        if (label.units === selectedUnit) {
            if (!chemicalSums[label.chemical]) {
                chemicalSums[label.chemical] = 0;
            }
            chemicalSums[label.chemical] += label.quantity;
            totalAmount += label.quantity;
        }
    });

    for (const chemical in chemicalSums) {
        const row = `<tr>
                        <td>${chemical}</td>
                        <td>${chemicalSums[chemical]} ${selectedUnit}</td>
                     </tr>`;
        tableBody.innerHTML += row;
    }

    // Display table and total amount only after search
    document.getElementById('resultsTableContainer').classList.remove('d-none');
    document.getElementById('totalAmountContainer').classList.remove('d-none');
    
    document.getElementById('totalAmount').textContent = totalAmount;
    document.getElementById('unitLabel').textContent = selectedUnit;
}
</script>
@endsection
