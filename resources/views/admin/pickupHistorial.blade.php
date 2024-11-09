@extends('admin.templateAdmin')

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
                <option value="active">Active</option>
                <option value="completed">Completed</option>
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
                </tr>
            </thead>
            <tbody>
                <!-- Sample Data Rows without 'Invalid' Status -->
                <tr data-status="completed" data-type="regular" data-building="Luchetti" data-room="L-203">
                    <td>00084194</td>
                    <td>0000953</td>
                    <td>maria.gomez@upr.edu</td>
                    <td>2024-10-01</td>
                    <td>Acetone</td>
                    <td>Luchetti</td>
                    <td>L-203</td>
                    <td>6 Gallons</td>
                    <td>10 Liters</td>
                    <td>Monday, 9:00 AM - 12:00 PM</td>
                    <td>Completed</td>
                    <td>Regular</td>
                </tr>
                <tr data-status="active" data-type="regular" data-building="Johnson" data-room="J-202">
                    <td>00084196</td>
                    <td>0000955</td>
                    <td>ramirez.ana@upr.edu</td>
                    <td>2024-10-03</td>
                    <td>Ethyl Alcohol</td>
                    <td>Johnson</td>
                    <td>J-202</td>
                    <td>15 Gallons</td>
                    <td>20 Liters</td>
                    <td>Wednesday, 11:00 AM - 2:00 PM</td>
                    <td>Active</td>
                    <td>
                        <div class="d-flex justify-content-between gap-2">
                            <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                            <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
                        </div>
                    </td>
                </tr>
                <tr data-status="completed" data-type="cleanout" data-building="Figueroa" data-room="F-101">
                    <td>00084195</td>
                    <td>0000954</td>
                    <td>juan.pablo@upr.edu</td>
                    <td>2024-10-02</td>
                    <td>Sodium Hydroxide</td>
                    <td>Figueroa</td>
                    <td>F-101</td>
                    <td>10 Liters</td>
                    <td>15 Liters</td>
                    <td>Tuesday, 10:00 AM - 1:00 PM</td>
                    <td>Completed</td>
                    <td>Clean Out</td>
                </tr>
                <tr data-status="active" data-type="cleanout" data-building="Ramirez" data-room="R-303">
                    <td>00084197</td>
                    <td>0000956</td>
                    <td>john.doe@upr.edu</td>
                    <td>2024-10-04</td>
                    <td>Methanol</td>
                    <td>Ramirez</td>
                    <td>R-303</td>
                    <td>8 Gallons</td>
                    <td>25 Liters</td>
                    <td>Friday, 8:00 AM - 12:00 PM</td>
                    <td>Active</td>
                    <td>
                        <div class="d-flex justify-content-between gap-2">
                            <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                            <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
                        </div>
                    </td>
                </tr>
                <!-- Additional Rows -->
                <tr data-status="completed" data-type="cleanout" data-building="Franklin" data-room="F-201">
                    <td>00084199</td>
                    <td>0000958</td>
                    <td>peter.parker@upr.edu</td>
                    <td>2024-10-06</td>
                    <td>Formaldehyde</td>
                    <td>Franklin</td>
                    <td>F-201</td>
                    <td>5 Gallons</td>
                    <td>10 Gallons</td>
                    <td>Tuesday, 1:00 PM - 3:00 PM</td>
                    <td>Completed</td>
                    <td>Clean Out</td>
                </tr>
                <tr data-status="active" data-type="regular" data-building="Blake" data-room="B-103">
                    <td>00084200</td>
                    <td>0000959</td>
                    <td>lucas.miller@upr.edu</td>
                    <td>2024-10-07</td>
                    <td>Acetic Acid</td>
                    <td>Blake</td>
                    <td>B-103</td>
                    <td>20 Liters</td>
                    <td>50 Liters</td>
                    <td>Wednesday, 10:00 AM - 1:00 PM</td>
                    <td>Active</td>
                    <td>
                        <div class="d-flex justify-content-between gap-2">
                            <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                            <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
                        </div>
                    </td>
                </tr>
                <tr data-status="completed" data-type="cleanout" data-building="Gray" data-room="G-303">
                    <td>00084202</td>
                    <td>0000961</td>
                    <td>mark.taylor@upr.edu</td>
                    <td>2024-10-09</td>
                    <td>Chlorine</td>
                    <td>Gray</td>
                    <td>G-303</td>
                    <td>10 Gallons</td>
                    <td>20 Gallons</td>
                    <td>Friday, 12:00 PM - 2:00 PM</td>
                    <td>Completed</td>
                    <td>Clean Out</td>
                </tr>
                <tr data-status="active" data-type="regular" data-building="Newton" data-room="N-101">
                    <td>00084203</td>
                    <td>0000962</td>
                    <td>emily.woods@upr.edu</td>
                    <td>2024-10-10</td>
                    <td>Ammonia</td>
                    <td>Newton</td>
                    <td>N-101</td>
                    <td>5 Gallons</td>
                    <td>10 Gallons</td>
                    <td>Saturday, 3:00 PM - 6:00 PM</td>
                    <td>Active</td>
                    <td>
                        <div class="d-flex justify-content-between gap-2">
                            <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                            <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
                        </div>
                    </td>
                </tr>
                <tr data-status="completed" data-type="regular" data-building="Tesla" data-room="T-302">
                    <td>00084204</td>
                    <td>0000963</td>
                    <td>albert.einstein@upr.edu</td>
                    <td>2024-10-11</td>
                    <td>Sulfuric Acid</td>
                    <td>Tesla</td>
                    <td>T-302</td>
                    <td>8 Gallons</td>
                    <td>15 Gallons</td>
                    <td>Monday, 9:00 AM - 12:00 PM</td>
                    <td>Completed</td>
                    <td>Regular</td>
                </tr>
                <!-- Additional rows can be added here if needed -->
            </tbody>
        </table>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Completion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this pickup as completed?
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
        // Initialize DataTable with custom settings
        const table = $('#pickupTable').DataTable({
            "pageLength": 10,
            "order": [[3, "asc"]],
            "dom": 'tip', // Hide default search bar, keep pagination
        });

        // Custom search for Building and Room Number
        $('#buildingSearch').on('input', function() {
            table.column(5).search(this.value).draw();
        });

        $('#roomSearch').on('input', function() {
            table.column(6).search(this.value).draw();
        });

        // Custom filter for Status and Type dropdowns
        $('#statusFilter').on('change', function() {
            const status = $(this).val();
            table.column(10).search(status === 'all' ? '' : status).draw();
        });

        $('#typeFilter').on('change', function() {
            const type = $(this).val();
            table.column(11).search(type === 'all' ? '' : type).draw();
        });
    });

    let selectedRow = null;

    function completePickup(method, button) {
        selectedRow = button.closest('tr');
        $('#confirmationModal').modal('show');
        $('#confirmCompletionButton').data('method', method);
    }

    $('#confirmCompletionButton').on('click', function() {
        const method = $(this).data('method');
        if (selectedRow) {
            $(selectedRow).find('td').eq(10).text('Completed'); // Update status
            $(selectedRow).find('td').eq(11).text(method.charAt(0).toUpperCase() + method.slice(1)); // Update method

            selectedRow.setAttribute('data-status', 'completed');
            $('#pickupTable').DataTable().draw();
        }
        $('#confirmationModal').modal('hide');
    });
</script>
@endsection
