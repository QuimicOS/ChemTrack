@extends('admin.templateAdmin')

<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Pickup Historial - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 120px;
        padding: 1.25rem;
        margin-top: 25px;
    }

    .table-container {
        margin-top: 20px;
    }

    table {
        border: none;
        border-radius: 0px;
        text-align: center;
    }

    td, th {
        padding: 20px;
        text-align: center;
        vertical-align: middle;
    }

    th {
        font-size: 1.1rem;
        background-color: #343a40;
        color: #fff;
    }

    tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    tbody tr:nth-child(odd) {
        background-color: #e9ecef;
    }

    .filters {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .search-bar, .filter-dropdown {
        width: 45%;
    }

    .search-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }
</style>

<!-- Main Content -->
<div class="content-area container">
    <div class="text-center mb-4">
        <h1 class="display-5">Pickup Historial</h1>
        <hr class="my-4">
    </div>

    <div class="filters">
        <div class="filter-dropdown">
            <label for="statusFilter" class="form-label">Filter by Status:</label>
            <select id="statusFilter" class="form-select">
                <option value="all">All</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Invalid">Invalid</option>
            </select>
        </div>
        

        <div class="filter-dropdown">
            <label for="typeFilter" class="form-label">Filter by Type:</label>
            <select id="typeFilter" class="form-select">
                <option value="all">All</option>
                <option value="regular">Regular</option>
                <option value="cleanout">Clean Out</option>
            </select>
        </div>

        <div class="search-bar">
            <label for="buildingSearch" class="form-label">Search by Building:</label>
            <input type="text" id="buildingSearch" class="search-input" placeholder="Enter building name...">
        </div>

        <div class="search-bar">
            <label for="roomSearch" class="form-label">Search by Room Number:</label>
            <input type="text" id="roomSearch" class="search-input" placeholder="Enter room number...">
        </div>
    </div>

    <div class="table-container">
        <table class="table table-bordered table-hover" id="pickupTable">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Pickup ID</th>
                    <th scope="col">Label ID</th>
                    <th scope="col">Requested By</th>
                    <th scope="col">Date Requested</th>
                    <th scope="col">Chemical Name</th>
                    <th scope="col">Building</th>
                    <th scope="col">Room Number</th>
                    <th scope="col">Stored (with Units)</th>
                    <th scope="col">Container Size</th>
                    <th scope="col">Timeframe</th>
                    <th scope="col">Status</th>
                    <th scope="col">Completion Method</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data populated via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Completion Confirmation -->
<div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completionModalLabel">Mark Pickup as Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this pickup as completed?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCompletionButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable and store it in a global variable for reuse
    window.table = $('#pickupTable').DataTable({
        "pageLength": 10,
        "order": [[3, "asc"]],
        "dom": 'tip'  // Hide default search bar, keep pagination
    });

    // Custom search for Building and Room Number
    $('#buildingSearch').on('input', function() {
        window.table.column(5).search(this.value).draw();
    });

    $('#roomSearch').on('input', function() {
        window.table.column(6).search(this.value).draw();
    });

    // Custom filter for Status dropdown
    $('#statusFilter').on('change', function() {
        const status = $(this).val();
        window.table.column(10).search(status === 'all' ? '' : status).draw();
    });

    // Custom filter for Type (Completion Method) dropdown
    $('#typeFilter').on('change', function() {
        const type = $(this).val();
        window.table.column(11).search(type === 'all' ? '' : type === 'regular' ? 'Regular' : 'Clean Out').draw();
    });


    // Fetch and populate data initially
    fetchPickupRequests();
});

function fetchPickupRequests() {
    fetch('/AdminpickupSearch')
        .then(response => response.json())
        .then(data => {
            populateTable(data.pickup_requests);
        })
        .catch(error => {
            console.error('Error fetching pickup requests:', error);
            alert('Failed to load pickup requests.');
        });
}

function populateTable(data) {
    // Clear existing data in the table
    window.table.clear();

    // Populate table with new data
    data.forEach(request => {
        const row = [
            request['Pickup Request ID'] || '-',
            request['Label ID'] || '-',
            request['Requested By Email'] || '-',
            request['Request Date'] || '-',
            request['Chemicals'] ? request['Chemicals'].join(', ') : '-',
            request['Building Name'] || '-',
            request['Room Number'] || '-',
            request['Quantity'] || '-',
            request['Container Size'] || '-',
            request['Timeframe'] || '-',
            request['Status'] || '-',
            request['Completion Method'] || '-',
            request['Status'] === 'Pending'
                ? `<button class="btn btn-primary" onclick="showCompletionModal(${request['Pickup Request ID']}, 'Regular')">Regular</button>
                   <button class="btn btn-secondary" onclick="showCompletionModal(${request['Pickup Request ID']}, 'Clean Out')">Clean Out</button>`
                : '-'
        ];
        window.table.row.add(row);
    });

    // Redraw the table with new data
    window.table.draw();
}

// Function to show confirmation modal
function showCompletionModal(pickupId, method) {
    $('#confirmCompletionButton').off('click').on('click', function() {
        completePickup(pickupId, method);
        $('#completionModal').modal('hide');
    });
    $('#completionModal').modal('show');
}

function completePickup(pickupId, method) {
    fetch('/AdminpickupComplete', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: pickupId, completion_method: method })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pickup request marked as completed successfully!');
            fetchPickupRequests(); // Refresh the table data
        } else {
            alert('Failed to complete pickup request.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to complete pickup request.');
    });
}
</script>
@endsection
