@extends('staff/templateStaff')

@section('title', 'Edit Label - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 120px;
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
                    <label for="stored" class="form-label">New Total Stored</label>
                    <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity (ex. 4.6, 7)" oninput="validateStoredInput()">
                    <small id="storedError" class="text-danger" style="display: none;">Incorrect input: Only numeric values are allowed.</small>
                </div>
                <div class="mb-3">
                    <label for="units" class="form-label">Units</label>
                    <select class="form-select" id="units" required>
                        <option selected>Select units</option>
                        <option value="gal">gal</option>
                        <option value="ml">mL</option>
                        <option value="L">L</option>
                        <option value="g">g</option>
                        <option value="kg">kg</option>
                        <option value="lbs">lbs</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="labelSize" class="form-label">Label Size</label>
                    <select class="form-select" id="labelSize" required>
                        <option selected>Select label size</option>
                        <option value="Small">Small (1x1)</option>
                        <option value="Medium">Medium (3x2)</option>
                        <option value="Large">Large (6x4)</option>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    let rowToRemove;
    let labelData = {};  // Store the fetched label data here

    // Hide form sections initially
    document.querySelector('.form-section').style.display = 'none';
    document.querySelector('.table-section').style.display = 'none';
    document.querySelector('.submit-section').style.display = 'none';

    // Fetch and populate the form from JSON
    function loadLabelData(labelId) {
        fetch(`/json/labelData${labelId}.json`)
            .then(response => response.json())
            .then(data => {
                labelData = data;
                document.getElementById('labelID').value = data.label_id;
                document.getElementById('editedBy').value = data.edited_by;
                document.getElementById('stored').value = data.stored_quantity;
                document.getElementById('units').value = data.units;
                const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
                tableBody.innerHTML = ''; // Clear existing rows
                data.chemicals.forEach(chemical => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="text" class="form-control chemical-name" value="${chemical.chemical_name}" /></td>
                        <td><input type="text" class="form-control cas-number" value="${chemical.cas_number}" /></td>
                        <td><input type="text" class="form-control percentage" value="${chemical.percentage}" oninput="validatePercentageInput(this)" /></td>
                        <td><button class="btn btn-danger removeRow" data-bs-toggle="modal" data-bs-target="#removeModal" onclick="setRemoveModal(this)">Remove</button></td>
                    `;
                    tableBody.appendChild(row);
                });
                document.querySelector('.form-section').style.display = 'block';
                document.querySelector('.table-section').style.display = 'block';
                document.querySelector('.submit-section').style.display = 'block';
                checkFormValidity(); 
            })
            .catch(error => {
                alert('Label ID not found');
            });
    }

    // Validate stored quantity (numeric only)
    function validateStoredInput() {
        const storedInput = document.getElementById("stored");
        const errorMessage = document.getElementById("storedError");
        const isValid = /^\d*\.?\d*$/.test(storedInput.value);

        errorMessage.style.display = isValid ? "none" : "block";
        checkFormValidity();
    }

    // Validate that the label ID contains only numeric characters
    document.getElementById('labelID').addEventListener('input', function () {
        const labelID = document.getElementById('labelID').value;
        const isNumeric = /^\d+$/.test(labelID);
        document.getElementById('searchButton').disabled = !isNumeric;
        document.getElementById('labelID').classList.toggle('is-invalid', !isNumeric);
    });

    // Search label data when clicking the search button
    document.getElementById('searchButton').addEventListener('click', function () {
        const labelID = document.getElementById('labelID').value;
        loadLabelData(labelID);
    });

    // Validate that the percentage field is numeric only
    function validatePercentageInput(input) {
        input.classList.toggle('is-invalid', !/^\d*\.?\d*$/.test(input.value));
        checkFormValidity();
    }

    // Check if all form fields are valid before enabling the Update button
    function checkFormValidity() {
        const stored = document.getElementById('stored').value.trim();
        const units = document.getElementById('units').value;
        const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');
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
        updateButton.disabled = !stored || !/^\d*\.?\d*$/.test(stored) || units === 'Select units' || !allRowsValid;
    }

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

    // Prepare JSON and PDF download after updating label
    document.getElementById('updateLabel').addEventListener('click', function () {
        // Collect updated data
        alert('Label updated Sucessfully');
        labelData.stored_quantity = document.getElementById('stored').value;
        labelData.units = document.getElementById('units').value;
        labelData.label_size = document.getElementById('labelSize').value;
        labelData.chemicals = [];

        document.querySelectorAll('#chemicalTable tbody tr').forEach(row => {
            const chemicalName = row.querySelector('.chemical-name').value;
            const casNumber = row.querySelector('.cas-number').value;
            const percentage = row.querySelector('.percentage').value;

            labelData.chemicals.push({
                chemical_name: chemicalName,
                cas_number: casNumber,
                percentage: percentage
            });
        });

        // Generate JSON file
        const jsonData = JSON.stringify(labelData, null, 2);
        const blob = new Blob([jsonData], { type: 'application/json' });
        const jsonLink = document.createElement('a');
        jsonLink.href = URL.createObjectURL(blob);
        jsonLink.download = `updatedLabel_${labelData.label_id}.json`;
        jsonLink.click();

        // Generate PDF
        generatePDF(labelData);
    });

    // Generate PDF function
    function generatePDF(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.text(`Label ID: ${data.label_id}`, 10, 10);
        doc.text(`Stored Quantity: ${data.stored_quantity}`, 10, 20);
        doc.text(`Units: ${data.units}`, 10, 30);
        doc.text(`Label Size: ${data.label_size}`, 10, 40);

        // Chemical table headers
        let y = 50;
        doc.text("Chemical Name", 10, y);
        doc.text("CAS Number", 60, y);
        doc.text("Percentage", 110, y);
        y += 10;

        // Add each chemical row
        data.chemicals.forEach(chemical => {
            doc.text(chemical.chemical_name, 10, y);
            doc.text(chemical.cas_number, 60, y);
            doc.text(chemical.percentage, 110, y);
            y += 10;
        });

        doc.save(`label_${data.label_id}.pdf`);
    }

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
