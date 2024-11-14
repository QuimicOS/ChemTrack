@extends('admin.templateAdmin')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('title', 'Edit Label - ChemTrack')
@section('content')
<style>
    .content-area {
        margin-left: 120px;
        padding: 1.25rem;
        margin-top: 25px;
    }

    /* Styling for the search container */
    .search-container {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-start;
        margin-bottom: 1.5rem;
    }

    .search-container label {
        white-space: nowrap;
    }

    .search-container input {
        min-width: 200px;
        max-width: 200px;
    }

    .search-container button {
        height: 38px;
    }

    /* Container for form fields */
    .form-block {
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 20px;
    }
</style>

<div class="content-area container">
    <div class="text-center mb-4">
        <h1 class="display-5">Edit Label</h1>
        <hr class="my-4">
    </div>

    <!-- Label ID Input with Search Button (aligned horizontally) -->
    <div class="search-container">
        <label for="labelID" class="form-label">Label ID <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="labelID" placeholder="Enter Label ID" required>
        <button id="searchButton" class="btn btn-primary" disabled>Search</button>
        <div class="invalid-feedback" style="width: 100%;">Please enter a valid numeric Label ID.</div>
    </div>

    <!-- Form Fields Section (Initially Hidden) -->
    <div class="form-section form-block">
        <fieldset>
            <legend>Basic Information</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="editedBy" class="form-label">Edited By (Username)</label>
                    <input type="text" class="form-control" id="editedBy" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stored" class="form-label">New Total Stored</label>
                    <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity (ex. 4.6, 7)" oninput="validateStoredInput()">
                    <small id="storedError" class="text-danger" style="display: none;">Incorrect input: Only numeric values are allowed.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="units" class="form-label">Units</label>
                    <select class="form-select" id="units" required>
                        <option selected disabled>Select units</option>
                        <option value="gal">Gallons (gal)</option>
                        <option value="ml">Milliliters (mL)</option>
                        <option value="L">Liters (L)</option>
                        <option value="g">Grams (g)</option>
                        <option value="kg">Kilograms (kg)</option>
                        <option value="lbs">Pounds (lbs)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="labelSize" class="form-label">Label Size</label>
                    <select class="form-select" id="labelSize" required>
                        <option selected disabled>Select label size</option>
                        <option value="Small">Small (1x1)</option>
                        <option value="Medium">Medium (3x2)</option>
                        <option value="Large">Large (6x4)</option>
                    </select>
                </div>
            </div>
        </fieldset>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    // Initialize variables for form sections and label data
    let labelData = {};  // Stores the fetched label data
    const crsfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Hide form sections initially
    document.querySelector('.form-section').style.display = 'none';
    document.querySelector('.table-section').style.display = 'none';
    document.querySelector('.submit-section').style.display = 'none';

 // Fetch and populate form with label data
function loadLabelData(labelId) {
    // Fetch label data from backend
    fetch(`/label/${labelId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error); // Display error if label not found
                return;
            }

            labelData = data; // Store fetched data
            document.getElementById('labelID').value = data.label_id;
            document.getElementById('editedBy').value = data.created_by;
            document.getElementById('stored').value = data.quantity;
            document.getElementById('units').value = data.units;
            document.getElementById('labelSize').value = data.label_size;

            // Clear existing table rows and populate with chemicals if available
            const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; // Clear previous rows
            if (data.contents && data.contents.length > 0) {
                data.contents.forEach(content => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="text" class="form-control chemical-name" value="${content.chemical_name}" /></td>
                        <td><input type="text" class="form-control cas-number" value="${content.cas_number}" /></td>
                        <td><input type="text" class="form-control percentage" value="${content.percentage}" oninput="validatePercentageInput(this)" /></td>
                        <td><button class="btn btn-danger removeRow" data-bs-toggle="modal" data-bs-target="#removeModal" onclick="setRemoveModal(this)">Remove</button></td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                alert('No chemicals found for this label.'); // Alert if no chemicals are present
            }

            // Show form sections after data is populated
            document.querySelector('.form-section').style.display = 'block';
            document.querySelector('.table-section').style.display = 'block';
            document.querySelector('.submit-section').style.display = 'block';

            checkFormValidity(); // Validate form after loading data
        })
        .catch(error => {
            alert('Error fetching label data');
            console.error(error);
        });
}


    // Enable/disable search button based on Label ID input
    document.getElementById('labelID').addEventListener('input', function () {
        const labelID = document.getElementById('labelID').value;
        const isNumeric = /^\d+$/.test(labelID);
        document.getElementById('searchButton').disabled = !isNumeric;
        document.getElementById('labelID').classList.toggle('is-invalid', !isNumeric);
    });

    // Search label data when clicking the search button
    document.getElementById('searchButton').addEventListener('click', function () {
        const labelID = document.getElementById('labelID').value;
        loadLabelData(labelID); // Load data for editing
    });

    // Validate stored quantity (numeric only)
    function validateStoredInput() {
        const storedInput = document.getElementById("stored");
        const errorMessage = document.getElementById("storedError");
        const isValid = /^\d*\.?\d*$/.test(storedInput.value);

        errorMessage.style.display = isValid ? "none" : "block";
        checkFormValidity();
    }

    // Validate percentage input (numeric only)
    function validatePercentageInput(input) {
        input.classList.toggle('is-invalid', !/^\d*\.?\d*$/.test(input.value));
        checkFormValidity();
    }

    // Check if all form fields are valid before enabling the Update button
    function checkFormValidity() {
        const stored = document.getElementById('stored').value.trim();
        const units = document.getElementById('units').value;
        const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');
        const labelSize = document.getElementById('labelSize').value;
        let allRowsValid = true;

        chemicalRows.forEach(row => {
            const chemicalName = row.querySelector('.chemical-name').value.trim();
            const casNumber = row.querySelector('.cas-number').value.trim();
            const percentage = row.querySelector('.percentage').value.trim();
            const isValidPercentage = /^\d*\.?\d*$/.test(percentage);

            if (!chemicalName || !casNumber || !isValidPercentage) {
                allRowsValid = false;
            }
        });

        const updateButton = document.getElementById('updateLabel');
        updateButton.disabled = !stored || !/^\d*\.?\d*$/.test(stored) || units === "Select units" || labelSize === "Select label size" || !allRowsValid;
    }

    // Update label data when clicking the Update button
    document.getElementById('updateLabel').addEventListener('click', function () {
        const labelID = document.getElementById('labelID').value;
        const updatedData = {
            quantity: document.getElementById('stored').value,
            units: document.getElementById('units').value,
            label_size: document.getElementById('labelSize').value,
            chemicals: Array.from(document.querySelectorAll('#chemicalTable tbody tr')).map(row => ({
                chemical_name: row.querySelector('.chemical-name').value,
                cas_number: row.querySelector('.cas-number').value,
                percentage: row.querySelector('.percentage').value
            }))
        };

        // Send updated data to backend
        fetch(`/editLabel/${labelID}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': crsfToken

            },
            body: JSON.stringify(updatedData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update label: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Label updated successfully!');
            } else {
                alert(data.error || 'Failed to update label');
            }
        })
        .catch(error => {
            console.error('Error updating label:', error);
            alert('Error updating label');
        });
    });

    // Add row functionality for chemicals
    document.getElementById('addRow').addEventListener('click', function () {
        const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" required></td>
            <td><input type="text" class="form-control cas-number" placeholder="CAS Number" required></td>
            <td><input type="text" class="form-control percentage" placeholder="Percentage" required oninput="validatePercentageInput(this)" /></td>
            <td><button class="btn btn-danger removeRow" data-bs-toggle="modal" data-bs-target="#removeModal" onclick="setRemoveModal(this)">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
        checkFormValidity();
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

    // Confirm removal of row on modal confirmation
    document.getElementById('confirmRemove').addEventListener('click', function () {
        if (rowToRemove) {
            rowToRemove.remove();
            rowToRemove = null;
            const modal = bootstrap.Modal.getInstance(document.getElementById('removeModal'));
            modal.hide();
            checkFormValidity();
        }
    });
</script>

@endsection
