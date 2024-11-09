@extends('admin.templateAdmin')

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
        justify-content: center; /* Center the filter section */
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
            <select id="filterStatus" class="form-select w-auto" onchange="filterTable()">
                <option value="All">All</option>
                <option value="Active">Active</option>
                <option value="Completed">Completed</option>
                <option value="Invalid">Invalid</option>
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
                    <th scope="col">Pickup Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr data-status="Active">
                    <td>00123</td>
                    <td>0000953</td>
                    <td>Acetone</td>
                    <td>Luchetti</td>
                    <td>L-203</td>
                    <td>6 Gallons</td>
                    <td>2024-10-05</td>
                    <td>-</td>
                    <td>Active</td>
                    <td>
                        <button class="btn btn-danger" onclick="showModal('00123')">Invalidate</button>
                    </td>
                </tr>
                <tr data-status="Invalid">
                    <td>00124</td>
                    <td>0000954</td>
                    <td>Sodium Hydroxide</td>
                    <td>Figueroa</td>
                    <td>F-101</td>
                    <td>10 Liters</td>
                    <td>2024-09-20</td>
                    <td>-</td>
                    <td>Invalid</td>
                    <td></td>
                </tr>
                <tr data-status="Completed">
                    <td>00125</td>
                    <td>0000955</td>
                    <td>Ethyl Alcohol</td>
                    <td>Johnson</td>
                    <td>J-202</td>
                    <td>20 Liters</td>
                    <td>2024-09-25</td>
                    <td>2024-09-30</td>
                    <td>Completed</td>
                    <td></td>
                </tr>
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
                <p>Are you sure you want to invalidate this pickup request?</p>
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
    $(document).ready(function() {
        // Initialize DataTable without default search box
        const table = $('#pickupTable').DataTable({
            "pageLength": 10,
            "order": [[7, "asc"]],
            "dom": 'tip'  // Hide default search box and only show paging
        });

        // Custom search by Room Number
        $('#filterRoom').on('input', function() {
            table.column(4).search(this.value).draw(); // Room Number is column index 4
        });
    });

    // Variable to store selected pickup ID for invalidation
    let selectedPickupID = null;

    // Show modal and set the selected pickup ID
    function showModal(pickupID) {
        selectedPickupID = pickupID; // Set selected pickup ID
        document.getElementById('modalPickupID').textContent = pickupID; // Display pickup ID in modal
        const modal = new bootstrap.Modal(document.getElementById('invalidateModal'));
        modal.show();
    }

    // Confirm invalidation of the selected pickup and update the table row
    function confirmInvalidate() {
        const tableRows = document.querySelectorAll('#pickupTable tbody tr');
        
        // Loop through rows to find the selected pickup ID and update its status
        tableRows.forEach(row => {
            const pickupIDCell = row.cells[0].textContent.trim(); // Get the pickup ID cell text
            if (pickupIDCell === selectedPickupID) {
                row.setAttribute('data-status', 'Invalid');
                row.cells[8].textContent = 'Invalid'; // Set status cell text to 'Invalid'
                row.cells[9].innerHTML = ''; // Remove button content in Actions column
            }
        });
        
        // Hide the modal after confirmation
        const modal = bootstrap.Modal.getInstance(document.getElementById('invalidateModal'));
        modal.hide();
    }

    // Filter table rows based on the selected status
    function filterTable() {
        const filterValue = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('#pickupTable tbody tr');

        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (filterValue === 'All' || status === filterValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
