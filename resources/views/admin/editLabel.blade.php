@extends('admin.templateAdmin')

@section('title', 'Edit Label - ChemTrack')

@section('content')
    <style>
        .content-area {
            margin-left: 125px;
            padding: 1.25rem;
            margin-top: 25px;
        }
    </style>

    <!-- Main Content -->
    <div class="content-area container">
        <div class="text-center mb-4">
            <h1 class="display-5">Edit Label</h1>
            <hr class="my-4">
        </div>

        <!-- Edit Label Form -->
        <div class="row mb-5">
            <div class="col-md-6">
                <label for="labelID" class="form-label">Label ID <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="labelID" placeholder="Enter Label ID" required>
                <div class="invalid-feedback">Please enter a valid numeric Label ID.</div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button id="searchButton" class="btn btn-primary w-100" disabled>Search</button>
            </div>
        </div>

        <!-- Form Fields Section (Initially Hidden) -->
        <div class="form-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="editedBy" class="form-label">Edited By (Username)</label>
                        <input type="text" class="form-control" id="editedBy" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="stored" class="form-label">Stored Quantity</label>
                        <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity" required>
                        <div class="invalid-feedback">Please enter a valid numeric quantity.</div>
                    </div>
                    <div class="mb-3">
                        <label for="units" class="form-label">Units</label>
                        <select class="form-select" id="units" required>
                            <option selected disabled>Select units</option>
                            <option value="ml">mL</option>
                            <option value="l">L</option>
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chemical Table Section -->
        <div class="table-section table-responsive">
            <table class="table table-bordered" id="chemicalTable">
                <thead class="table-dark">
                    <tr>
                        <th>Chemical Name</th>
                        <th>CAS Number</th>
                        <th>Percentage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows populated dynamically -->
                </tbody>
            </table>
            <button class="btn btn-primary mb-3" id="addRow">Add Row</button>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2 submit-section">
            <button class="btn btn-success" id="updateLabel" type="button" disabled>Update Label</button>
        </div>
    </div>

    <!-- Modal Row Removal Confirmation -->
    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Chemical Name:</strong> <span id="modalChemicalName"></span></p>
                    <p><strong>CAS Number:</strong> <span id="modalCASNumber"></span></p>
                    <p><strong>Percentage:</strong> <span id="modalPercentage"></span></p>
                    <p>Are you sure you want to remove this chemical?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Confirm Remove</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let rowToRemove;
    let labelData = {};  // Store the fetched label data here

    // Hide form sections initially
    document.querySelector('.form-section').style.display = 'none';
    document.querySelector('.table-section').style.display = 'none';
    document.querySelector('.submit-section').style.display = 'none';

    // Fetch and populate the form from JSON
    function loadLabelData(labelId) {
        fetch(`/json/labelData${labelId}.json`)  // file name is dynamic based on label ID
            .then(response => response.json())
            .then(data => {
                labelData = data;

                // Populate form fields
                document.getElementById('labelID').value = data.label_id;
                document.getElementById('editedBy').value = data.edited_by;
                document.getElementById('stored').value = data.stored_quantity;
                document.getElementById('units').value = data.units;

                // Populate chemical table
                const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
                tableBody.innerHTML = ''; // Clear existing rows
                data.chemicals.forEach(chemical => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="text" class="form-control chemical-name" value="${chemical.chemical_name}" /></td>
                        <td><input type="text" class="form-control cas-number" value="${chemical.cas_number}" /></td>
                        <td><input type="text" class="form-control percentage" value="${chemical.percentage}" /></td>
                        <td><button class="btn btn-danger removeRow" data-bs-toggle="modal" data-bs-target="#removeModal" onclick="setRemoveModal(this)">Remove</button></td>
                    `;
                    tableBody.appendChild(row);
                });

                // Show form and table
                document.querySelector('.form-section').style.display = 'block';
                document.querySelector('.table-section').style.display = 'block';
                document.querySelector('.submit-section').style.display = 'block';
                checkFormValidity(); 
            })
            .catch(error => {
                alert('Error loading label data: ' + error.message);
            });
    }

    // Enable Search button when Label ID field has valid numeric input
    document.getElementById('labelID').addEventListener('input', function () {
        const searchButton = document.getElementById('searchButton');
        if (/^\d+$/.test(this.value)) {
            searchButton.disabled = false;
        } else {
            searchButton.disabled = true;
        }
    });

    // Fetch data from JSON when Search button is clicked
    document.getElementById('searchButton').addEventListener('click', function () {
        const labelID = document.getElementById('labelID').value;
        loadLabelData(labelID);
    });

    // Check form validity to enable or disable Update button
    function checkFormValidity() {
        const labelID = document.getElementById('labelID').value.trim();
        const stored = document.getElementById('stored').value.trim();
        const units = document.getElementById('units').value;

        const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');
        let allRowsValid = true;

        chemicalRows.forEach(row => {
            const chemicalName = row.querySelector('.chemical-name').value.trim();
            const casNumber = row.querySelector('.cas-number').value.trim();
            const percentage = row.querySelector('.percentage').value.trim();

            if (!chemicalName || !casNumber || !percentage || !/^\d*\.?\d*$/.test(percentage)) {
                allRowsValid = false;
            }
        });

        const updateButton = document.getElementById('updateLabel');
        if (labelID && /^\d+$/.test(labelID) && stored && /^\d*\.?\d*$/.test(stored) && units !== 'Select units' && allRowsValid) {
            updateButton.disabled = false;
        } else {
            updateButton.disabled = true;
        }
    }

    // Add a new row to the chemical table
    document.getElementById('addRow').addEventListener('click', function () {
        const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" required></td>
            <td><input type="text" class="form-control cas-number" placeholder="CAS Number" required></td>
            <td><input type="text" class="form-control percentage" placeholder="Percentage" required></td>
            <td><button class="btn btn-danger removeRow" data-bs-toggle="modal" data-bs-target="#removeModal" onclick="setRemoveModal(this)">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
        checkFormValidity();
        addRemoveRowListeners();
    });

    // Update JSON and trigger download
    document.getElementById('updateLabel').addEventListener('click', function () {
        // Collect updated data
        labelData.stored_quantity = document.getElementById('stored').value;
        labelData.units = document.getElementById('units').value;
        labelData.chemicals = [];

        const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');
        chemicalRows.forEach(row => {
            const chemicalName = row.querySelector('.chemical-name').value;
            const casNumber = row.querySelector('.cas-number').value;
            const percentage = row.querySelector('.percentage').value;

            labelData.chemicals.push({
                chemical_name: chemicalName,
                cas_number: casNumber,
                percentage: percentage
            });
        });

        // Convert updated data to JSON and trigger download
        const jsonData = JSON.stringify(labelData, null, 2);
        const blob = new Blob([jsonData], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `updatedLabel_${labelData.label_id}.json`;
        link.click();
    });

    // Set modal with row info and assign the row to be removed
    function setRemoveModal(button) {
        const row = button.closest('tr');
        const chemicalName = row.querySelector('.chemical-name').value;
        const casNumber = row.querySelector('.cas-number').value;
        const percentage = row.querySelector('.percentage').value;

        document.getElementById('modalChemicalName').textContent = chemicalName;
        document.getElementById('modalCASNumber').textContent = casNumber;
        document.getElementById('modalPercentage').textContent = percentage;

        rowToRemove = row;
    }

    // Remove row on modal confirmation
    document.getElementById('confirmRemove').addEventListener('click', function () {
        if (rowToRemove) {
            rowToRemove.remove();
            rowToRemove = null;
            const modal = bootstrap.Modal.getInstance(document.getElementById('removeModal'));
            modal.hide();
            checkFormValidity();
        }
    });

    // Add remove row listeners to new rows
    function addRemoveRowListeners() {
        document.querySelectorAll('.chemical-name, .cas-number, .percentage').forEach(input => {
            input.addEventListener('input', checkFormValidity);
        });
    }

    addRemoveRowListeners();
</script>
@endsection
