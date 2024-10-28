@extends('admin.templateAdmin')

@section('title', 'Invalidate Pickup - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 140px;
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
        justify-content: space-between;
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
            <label for="sortByDate" class="form-label">Order by Date:</label>
            <button class="btn btn-outline-primary" onclick="sortTableByDate('asc')">Ascending</button>
            <button class="btn btn-outline-primary" onclick="sortTableByDate('desc')">Descending</button>
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
                    <td data-date="2024-10-10">2024-10-10</td>
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
                    <td data-date="2024-09-28">2024-09-28</td>
                    <td>Invalid</td>
                    <td><button class="btn btn-secondary" disabled>Already Invalidated</button></td>
                </tr>
                <tr data-status="Completed">
                    <td>00125</td>
                    <td>0000955</td>
                    <td>Ethyl Alcohol</td>
                    <td>Johnson</td>
                    <td>J-202</td>
                    <td>20 Liters</td>
                    <td data-date="2024-09-30">2024-09-30</td>
                    <td>Completed</td>
                    <td><button class="btn btn-secondary" disabled>Already Completed</button></td>
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

<script>
    let selectedPickupID = null;

    function showModal(pickupID) {
        selectedPickupID = pickupID;
        document.getElementById('modalPickupID').textContent = pickupID;
        const modal = new bootstrap.Modal(document.getElementById('invalidateModal'));
        modal.show();
    }

    function confirmInvalidate() {
        const tableRows = document.querySelectorAll('#pickupTable tbody tr');
        
        tableRows.forEach(row => {
            const pickupIDCell = row.cells[0].textContent.trim();
            if (pickupIDCell === selectedPickupID) {
                row.setAttribute('data-status', 'Invalid');
                row.cells[7].textContent = 'Invalid';
                row.cells[8].innerHTML = '<button class="btn btn-secondary" disabled>Already Invalidated</button>';
            }
        });
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('invalidateModal'));
        modal.hide();
    }

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

    function sortTableByDate(order) {
        const table = document.getElementById('pickupTable');
        const rowsArray = Array.from(table.rows).slice(1);
        const orderMultiplier = order === 'asc' ? 1 : -1;

        rowsArray.sort((a, b) => {
            const dateA = new Date(a.querySelector('[data-date]').getAttribute('data-date'));
            const dateB = new Date(b.querySelector('[data-date]').getAttribute('data-date'));
            return (dateA - dateB) * orderMultiplier;
        });

        rowsArray.forEach(row => table.tBodies[0].appendChild(row));
    }
</script>
@endsection
