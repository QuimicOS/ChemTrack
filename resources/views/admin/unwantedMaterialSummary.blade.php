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
        <div id="loadingSpinner" class="spinner-border text-primary d-none" role="status">
            <span class="visually-hidden">Loading...</span>
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
                <button class="btn btn-primary w-100" id="chemicalSearchBtn" disabled>Search by Chemical</button>
            </div>
            <div id="loadingSpinner" class="spinner-border text-primary d-none" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>            
        </div>

        <!-- Filter by Volume or Weight -->
        <!-- <div class="row mb-4">
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
        </div> -->

        <!-- Table Section -->
        <div class="table-container d-none" id="resultsTableContainer">
            <table class="table table-bordered table-hover" id="summaryTable">
                <thead class="table-dark">
                    <tr>
                        <th>Chemical Name</th>
                        <th>Liquid Amount (L)</th>
                        <th>Solid Amount (kg)</th>
                    </tr>
                </thead>
                <tbody id="chemicalTableBody">
                    <!-- Data will be dynamically populated -->
                </tbody>
            </table>
            
        </div>
        
        

        <!-- Total Volume/Weight -->
        <div class="row mt-4 d-none" id="totalAmountContainer">
            <div class="col-md-6">
                <h5>Total Weight: <span id="totalAmount">0</span> <span id="unitLabel"></span></h5>
                <h5>Total Volume: <span id="totalAmountV">0</span> <span id="unitLabel"></span></h5> 

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
<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>

$(document).ready(function() {
    let tableInitialized = false; // Track if DataTable is initialized

        $('#chemicalSearchBtn').on('click', debounce(function() {
        const chemicalName = $('#chemicalName').val().trim();
        if (chemicalName) {
            fetchData({ chemical_name: chemicalName });
        }
    }, 300));

    $('#dateSearchBtn').on('click', debounce(function() {
        const fromDate = $('#fromDate').val();
        const toDate = $('#toDate').val();

        console.log("From Date:", fromDate, "To Date:", toDate); // Debugging: Inspect the date values
        if (fromDate && toDate) {
            fetchData({ start_date: fromDate, end_date: toDate });
        }
    }, 300));



    function fetchData(params) {
    // Show loading spinner
    $('#loadingSpinner').removeClass('d-none');
    $('#dateSearchBtn, #chemicalSearchBtn').prop('disabled', true); // Disable buttons

    const queryString = new URLSearchParams(params).toString();
    fetch(`/unwanted-material-summary?${queryString}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetched Data:", data); // Debugging: Inspect the response
            if (data.length > 0) {
                populateMaterialSummary(data);
                $('#noChemicalAlert').addClass('d-none'); // Hide the alert
            } else {
                $('#noChemicalAlert').removeClass('d-none'); // Show the alert
                $('#resultsTableContainer').addClass('d-none'); // Hide the table
                $('#totalAmountContainer').addClass('d-none'); // Hide the total
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Error fetching data. Please try again.');
        })
        .finally(() => {
            // Re-enable buttons and hide spinner
            $('#loadingSpinner').addClass('d-none');
            $('#dateSearchBtn, #chemicalSearchBtn').prop('disabled', false);
        });
    }


    function populateMaterialSummary(data) {
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = ''; // Clear previous data

    let totalLiquidAmount = 0; // Initialize total liquid amount
    let totalSolidAmount = 0; // Initialize total solid amount

    data.forEach(item => {
        const liquidAmount = item.total_liquid_quantity ? item.total_liquid_quantity.toFixed(2) : "0.00";
        const solidAmount = item.total_solid_quantity ? item.total_solid_quantity.toFixed(2) : "0.00";

        // Accumulate totals
        totalLiquidAmount += parseFloat(liquidAmount);
        totalSolidAmount += parseFloat(solidAmount);

        // Generate table row
        const row = `
            <tr>
                <td>${item.chemical_name}</td>
                <td>${liquidAmount}</td>
                <td>${solidAmount}</td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    // Update total amounts in the UI
    document.getElementById('totalAmountV').textContent = `${totalLiquidAmount.toFixed(2)} L`; // Total Volume with units
    document.getElementById('totalAmount').textContent = `${totalSolidAmount.toFixed(2)} kg`; // Total Weight with units

    // Show table and total amount container
    document.getElementById('resultsTableContainer').classList.remove('d-none');
    document.getElementById('totalAmountContainer').classList.remove('d-none');
    }

});


function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}


// Enable "Search by Date" button when both dates are valid
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
dateSearchBtn.addEventListener('click', function () {
    const fromDate = fromDateField.value;
    const toDate = toDateField.value;

    if (!fromDate || !toDate) {
        alert('Please select both start and end dates.');
        return;
    }

    // Fetch summary data for the specified date range
    fetchData({ start_date: fromDate, end_date: toDate });
});

// Enable "Search by Chemical" button when chemical name input is non-empty
const chemicalNameField = document.getElementById('chemicalName');
const chemicalSearchBtn = document.getElementById('chemicalSearchBtn');

function toggleChemicalSearchButton() {
    chemicalSearchBtn.disabled = !chemicalNameField.value.trim();
}

chemicalNameField.addEventListener('input', toggleChemicalSearchButton);

// Chemical name search with case-insensitive match
chemicalSearchBtn.addEventListener('click', function () {
    const chemicalName = chemicalNameField.value.trim();

    fetchData({ chemical_name: chemicalName });
});

// Fetch data from the backend
function fetchData(params) {
    const queryString = new URLSearchParams(params).toString();
    
    fetch(`/unwanted-material-summary?${queryString}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            return response.json();
        })
        .then(data => {
            if (data.length === 0) {
                document.getElementById('noChemicalAlert').classList.remove('d-none');
                document.getElementById('resultsTableContainer').classList.add('d-none');
                document.getElementById('totalAmountContainer').classList.add('d-none');
            } else {
                document.getElementById('noChemicalAlert').classList.add('d-none');
                filterAndPopulateTable(data);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Error fetching data. Please try again.');
        });
}

// Filter data and populate table
function filterAndPopulateTable(filteredData) {
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = ''; // Clear previous data

    let totalAmount = 0;

    // filteredData.forEach(item => {
    //     totalAmount += item.total_contributed_quantity;

    //     const row = `
    //         <tr>
    //             <td>${item.chemical_name}</td>
    //             <td>${item.total_contributed_quantity.toFixed(2)} ${item.units}</td>
    //             <td>${item.readable_date}</td>

    //         </tr>
    //     `;
    //     tableBody.innerHTML += row;
    // });

    // Display total amount and table
    document.getElementById('resultsTableContainer').classList.remove('d-none');
    document.getElementById('totalAmountContainer').classList.remove('d-none');
    document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('unitLabel').textContent = filteredData[0]?.units || '';
}
</script>
@endsection