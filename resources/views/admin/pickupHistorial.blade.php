@extends('admin.templateAdmin')

@section('title', 'Pickup Historial - ChemTrack')

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
      }

      .search-bar {
        width: 50%;
      }

      .filter-dropdown {
        margin-left: 10px;
      }

      .search-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
      }

      .completion-buttons {
        display: flex;
        gap: 10px;
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
        <input type="text" id="buildingSearch" class="search-input" placeholder="Search by building...">
      </div>
    </div>

    <div class="table-container">
      <table class="table table-bordered table-hover">
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
        <tbody id="pickupTable">
          <!-- Your dummy data is preserved here -->
          <tr data-status="completed" data-type="regular" data-building="luchetti">
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
          <tr data-status="active" data-type="regular" data-building="johnson">
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
              <div class="completion-buttons">
                <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
              </div>
            </td>
          </tr>
          <tr data-status="completed" data-type="cleanout" data-building="figueroa">
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
          <tr data-status="active" data-type="cleanout" data-building="ramirez">
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
              <div class="completion-buttons">
                <button class="btn btn-primary" onclick="completePickup('regular', this)">Regular</button>
                <button class="btn btn-secondary" onclick="completePickup('cleanout', this)">Clean Out</button>
              </div>
            </td>
          </tr>
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
  <script>
    let selectedRow = null;
    let selectedMethod = null;

    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('typeFilter').addEventListener('change', filterTable);
    document.getElementById('buildingSearch').addEventListener('input', filterTable);

    function filterTable() {
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const buildingSearch = document.getElementById('buildingSearch').value.toLowerCase();

    const rows = document.querySelectorAll('#pickupTable tr');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowType = row.getAttribute('data-type');
        const rowBuilding = row.getAttribute('data-building');

        // Check if the row matches the selected filters
        let statusMatch = (status === 'all' || rowStatus === status);
        let typeMatch = (type === 'all' || rowType === type.toLowerCase());
        let buildingMatch = rowBuilding.includes(buildingSearch);

        // Show or hide the row based on the filter match
        row.style.display = (statusMatch && typeMatch && buildingMatch) ? '' : 'none';
    });
}


    function completePickup(method, button) {
      selectedRow = button.closest('tr');
      selectedMethod = method;

      // Show the confirmation modal
      const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
      confirmationModal.show();
    }

    document.getElementById('confirmCompletionButton').addEventListener('click', () => {
      if (selectedRow && selectedMethod) {
        // Update the row to mark as completed
        selectedRow.querySelector('td:nth-last-child(2)').innerText = 'Completed';
        selectedRow.querySelector('td:last-child').innerText = selectedMethod.charAt(0).toUpperCase() + selectedMethod.slice(1);
        
        // Update the data-status attribute for filtering purposes
        selectedRow.setAttribute('data-status', 'completed');
        
        // Reapply filter to refresh the view
        filterTable();
      }

      // Hide the modal after confirming
      const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
      confirmationModal.hide();
    });
  </script>
@endsection
