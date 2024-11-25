@extends('staff/templateStaff')

<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Invalidate Pickup - ChemTrack')

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
    }

    td, th {
        padding: 15px;
    }

    .filter-container {
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
</style>

<div class="content-area container">
    <div class="text-center mb-4">
        <h1 class="display-5">Invalidate Pickup Request</h1>
        <hr class="my-4">
    </div>

    <div class="filter-container">
        <div>
            <label for="filterStatus" class="form-label">Filter by Status:</label>
            <select id="filterStatus" class="form-select w-auto">
                <option value="">All</option>
                <option value="Active">Active</option>
                <option value="Completed">Completed</option>
                <option value="Invalid">Invalid</option>
                <option value="Pending">Pending</option>
                <option value="Overdue">Overdue</option>
            </select>
        </div>
        <div>
            <label for="filterRoom" class="form-label">Search by Room Number:</label>
            <input type="text" id="filterRoom" class="form-control w-auto" placeholder="Enter room number">
        </div>
    </div>

    <div class="table-container">
        <table class="table table-bordered table-hover" id="pickupTable">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Pickup ID</th>
                    <th scope="col">Label ID</th>
                    <th scope="col">Chemical Name</th>
                    <th scope="col">Building</th>
                    <th scope="col">Room Number</th>
                    <th scope="col">Container Capacity</th>
                    <th scope="col">Pickup Requested</th>
                    <th scope="col">Completion Date</th>
                    <th scope="col">Message</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            
            <tbody>
                <!-- Rows will be populated dynamically via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Invalidation Confirmation -->
<div class="modal fade" id="invalidateModal" tabindex="-1" aria-labelledby="invalidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invalidateModalLabel">Confirm Invalidation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Pickup ID:</strong> <span id="modalPickupID"></span></p>
                <div class="form-group">
                    <label for="invalidateReason"><strong>Reason for Invalidation:</strong></label>
                    <textarea id="invalidateReason" class="form-control" rows="3" placeholder="Enter reason for invalidation" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmInvalidate()">Confirm Invalidate</button>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
 document.addEventListener("DOMContentLoaded", function() {
    initializeDataTable();
    fetchPickupRequests();
});

let selectedPickupID = null;

function initializeDataTable() {
    $('#pickupTable').DataTable({
        "pageLength": 10,
        "order": [[7, "asc"]],
        "dom": 'tip'  // Hide default search box and only show paging
    });

    // Custom search by Room Number
    $('#filterRoom').on('input', function() {
        const table = $('#pickupTable').DataTable();
        table.column(4).search(this.value).draw();
    });

    // Custom filter by Status
    $('#filterStatus').on('change', function() {
        const table = $('#pickupTable').DataTable();
        const status = $(this).val();
        table.column(8).search(status).draw();
    });
}

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function fetchPickupRequests() {
    fetch('/getPickupRequests', { // Updated URL to match the defined route
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data); // Log the data to verify structure
        populateTable(data);
    })
    .catch(error => {
        console.error('Error fetching pickup requests:', error);
        alert('Failed to load pickup requests.');
    });
}

function populateTable(data) {
    const table = $('#pickupTable').DataTable();
    table.clear();

    data.forEach(request => {
        const chemicalNames = Array.isArray(request["Chemical(s)"])
            ? request["Chemical(s)"].join(', ')
            : '-';

        const row = [
            request["Pickup ID"] || '-',
            request["Label ID"] || '-',
            chemicalNames, // Join array items for Chemical Name
            request["Building Name"] || '-',
            request["Room Number"] || '-',
            `${request["Container Size"] || '-'} ${request.units || ''}`,
            request["Request Date"] ? new Date(request["Request Date"]).toLocaleDateString() : '-',
            request["Completion Date"] ? new Date(request["Completion Date"]).toLocaleDateString() : '-',
            request["Message"] || '-', // Display message or default '-'
            formatStatus(request.Status),
            request.Status === 2 ? `<button class="btn btn-danger" onclick="showModal('${request["Pickup ID"]}')">Invalidate</button>` : ''
        ];
        table.row.add(row);
    });

    table.draw();
}


function formatStatus(status) {
    switch (status) {
        case 0: return 'Invalid';
        case 1: return 'Completed';
        case 2: return 'Pending';
        case 3: return 'Overdue';
        default: return 'Unknown';
    }
}

function showModal(pickupID) {
    selectedPickupID = pickupID;
    document.getElementById('modalPickupID').textContent = pickupID;
    const modal = new bootstrap.Modal(document.getElementById('invalidateModal'));
    modal.show();
}

function confirmInvalidate() {
    const reason = document.getElementById('invalidateReason').value.trim();

    if (!reason) {
        alert('Please provide a reason for invalidation.');
        return;
    }

    fetch('/pickupInvalidate', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        pickup_id: selectedPickupID,
        message: reason // Include the reason
    })
})
.then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
})
.then(data => {
    if (data.success) {
        alert(data.message || 'Pickup request invalidated successfully!');
        fetchPickupRequests(); // Refresh the table
        const modal = bootstrap.Modal.getInstance(document.getElementById('invalidateModal'));
        modal.hide();
    } else {
        alert(data.message || 'Failed to invalidate pickup request.');
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('Failed to invalidate pickup request. Please try again.');
});

}

</script>
@endsection
