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
        </div>

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
                <h5>Total Weight: <span id="totalAmount">0</span> kg</h5>
                <h5>Total Volume: <span id="totalAmountV">0</span> L</h5>
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
    const dataTable = $('#summaryTable').DataTable({
        paging: true,
        searching: false,
        ordering: true,
        pageLength: 10,
        lengthChange: true,
        autoWidth: false,
        info: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Enable the "Search by Chemical" button when chemical name is provided
    $('#chemicalName').on('input', function() {
        const value = $(this).val().trim();
        $('#chemicalSearchBtn').prop('disabled', !value);
    });

    // Enable the "Search by Date" button when both dates are provided
    $('#fromDate, #toDate').on('input', function() {
        const fromDate = $('#fromDate').val();
        const toDate = $('#toDate').val();
        $('#dateSearchBtn').prop('disabled', !(fromDate && toDate && new Date(fromDate) <= new Date(toDate)));
    });

    // Event listeners for search buttons
    $('#chemicalSearchBtn').on('click', function() {
        const chemicalName = $('#chemicalName').val().trim();
        if (chemicalName) {
            fetchData({ chemical_name: chemicalName });
        }
    });

    $('#dateSearchBtn').on('click', function() {
        const fromDate = $('#fromDate').val();
        const toDate = $('#toDate').val();
        if (fromDate && toDate) {
            fetchData({ start_date: fromDate, end_date: toDate });
        }
    });

    // Fetch and populate data dynamically
    function fetchData(params) {
        $('#loadingSpinner').removeClass('d-none');
        const queryString = new URLSearchParams(params).toString();

        fetch(`/unwanted-material-summary?${queryString}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch data');
                return response.json();
            })
            .then(data => {
                if (data.length > 0) {
                    populateMaterialSummary(data);
                    $('#noChemicalAlert').addClass('d-none');
                } else {
                    $('#noChemicalAlert').removeClass('d-none');
                    $('#resultsTableContainer').addClass('d-none');
                    $('#totalAmountContainer').addClass('d-none');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Error fetching data. Please try again.');
            })
            .finally(() => {
                $('#loadingSpinner').addClass('d-none');
            });
    }

    function populateMaterialSummary(data) {
        dataTable.clear();
        let totalLiquidAmount = 0;
        let totalSolidAmount = 0;

        data.forEach(item => {
            const liquidAmount = item.total_liquid_quantity?.toFixed(2) || '0.00';
            const solidAmount = item.total_solid_quantity?.toFixed(2) || '0.00';

            totalLiquidAmount += parseFloat(liquidAmount);
            totalSolidAmount += parseFloat(solidAmount);

            dataTable.row.add([
                item.chemical_name || '-',
                liquidAmount,
                solidAmount
            ]);
        });

        dataTable.draw();
        $('#resultsTableContainer').removeClass('d-none');
        $('#totalAmountContainer').removeClass('d-none');
        $('#totalAmountV').text(totalLiquidAmount.toFixed(2));
        $('#totalAmount').text(totalSolidAmount.toFixed(2));
    }
});
</script>
@endsection
