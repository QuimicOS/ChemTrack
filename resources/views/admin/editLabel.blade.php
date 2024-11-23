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
                        <option selected value="Small">Small (1x1)</option>
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
                    <tr>
                        <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" list="chemicalList" required></td>
                        <td><input type="text" class="form-control cas-number" name="cas_number[]" placeholder="CAS Number" readonly required></td>
                        <td><input type="number" class="form-control percentage" name="percentage[]" placeholder="Percentage" required></td>
                        <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <datalist id="chemicalList"></datalist>
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
document.addEventListener("DOMContentLoaded", function () {
    const crsfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let labelData = {};
    let chemicalData = [];

    // Hide form sections initially
    const formSections = document.querySelectorAll('.form-section, .table-section, .submit-section');
    formSections.forEach(section => section.style.display = 'none');

    document.querySelector('#chemicalTable').addEventListener('input', function (event) {
    if (event.target.classList.contains('percentage')) {
        checkFormValidity();
    }
    });

    /**
     * Fetch and populate form with label data
     */
    function loadLabelData(labelId) {
        fetch(`/label/${labelId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                labelData = data;
                document.getElementById('labelID').value = data.label_id;
                document.getElementById('editedBy').value = data.created_by;
                document.getElementById('stored').value = data.quantity;
                document.getElementById('units').value = data.units;

                // Handle label size dropdown
                // const labelSizeDropdown = document.getElementById('labelSize');
                // const availableOptions = Array.from(labelSizeDropdown.options).map(option => option.value);

                // if (availableOptions.includes(data.label_size)) {
                //     labelSizeDropdown.value = data.label_size;
                // } else {
                //     labelSizeDropdown.value = "Select label size";
                //     console.warn('Unexpected label_size:', data.label_size);
                // }

                // Clear and populate the chemical table
                const tableBody = document.querySelector('#chemicalTable tbody');
                tableBody.innerHTML = '';
                if (data.contents && data.contents.length > 0) {
                    data.contents.forEach(content => {
                        addChemicalRow(content.chemical_name, content.cas_number, content.percentage);
                    });
                }

                // Show form sections
                formSections.forEach(section => section.style.display = 'block');
                checkFormValidity(); // Validate the form
            })
            .catch(error => {
                alert('Error fetching label data.');
                console.error(error);
            });
    }

    /**
     * Add a new row to the chemical table
     */
    function addChemicalRow(chemicalName = '', casNumber = '', percentage = '') {
        const tableBody = document.querySelector('#chemicalTable tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <input type="text" class="form-control chemical-name" placeholder="Chemical Name" list="chemicalList" value="${chemicalName}" required>
            </td>
            <td>
                <input type="text" class="form-control cas-number" placeholder="CAS Number" value="${casNumber}" readonly required>
            </td>
            <td>
                <input type="number" class="form-control percentage" placeholder="Percentage" value="${percentage}" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger removeRow">Remove</button>
            </td>
        `;
        tableBody.appendChild(newRow);

        // Set up autocomplete for the new row
        setupAutocomplete(newRow.querySelector('.chemical-name'));

        // Add remove functionality
        newRow.querySelector('.removeRow').addEventListener('click', function () {
            newRow.remove();
            checkFormValidity();
        });
    }

    /**
     * Validate the entire form and enable/disable the Update button
     */
     function checkFormValidity() {
    const stored = document.getElementById('stored').value.trim();
    const units = document.getElementById('units').value;
    const labelSize = document.getElementById('labelSize').value;
    const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');

    let allRowsValid = true;
    let totalPercentage = 0;

    chemicalRows.forEach(row => {
        const chemicalName = row.querySelector('.chemical-name')?.value.trim();
        const casNumber = row.querySelector('.cas-number')?.value.trim();
        const percentage = parseFloat(row.querySelector('.percentage')?.value.trim());

        const isValidPercentage = !isNaN(percentage) && percentage > 0;

        if (!chemicalName || !casNumber || !isValidPercentage) {
            allRowsValid = false;
        }

        if (isValidPercentage) {
            totalPercentage += percentage;
        }
    });

    // Validate percentage sum
    const percentageError = document.getElementById('storedError');
    if (totalPercentage !== 100) {
        percentageError.textContent = `The total percentage must equal 100%. Current total: ${totalPercentage}%`;
        percentageError.style.display = "block";
        allRowsValid = false;
    } else {
        percentageError.style.display = "none";
    }

    // Enable or disable the Update button
    const updateButton = document.getElementById('updateLabel');
    updateButton.disabled = !stored || !/^\d*\.?\d*$/.test(stored) || units === "Select units" || labelSize === "Select label size" || !allRowsValid;
}


    /**
     * Set up autocomplete functionality for chemical name fields
     */
    function setupAutocomplete(inputElement) {
        inputElement.addEventListener('input', function () {
            const val = this.value.toLowerCase();
            const matchingChemical = chemicalData.find(chemical => chemical.chemical_name.toLowerCase() === val);

            const casInput = inputElement.closest('tr')?.querySelector('.cas-number');
            if (casInput) {
                if (matchingChemical) {
                    casInput.value = matchingChemical.cas_number;
                } else {
                    casInput.value = '';
                }
            }
        });

        inputElement.addEventListener('blur', function () {
            const val = this.value.toLowerCase();
            const matchingChemical = chemicalData.find(chemical => chemical.chemical_name.toLowerCase() === val);

            const casInput = inputElement.closest('tr')?.querySelector('.cas-number');
            if (casInput && !matchingChemical) {
                this.value = '';
                casInput.value = '';
                alert('Please select a valid chemical from the list.');
            }
        });
    }

    /**
     * Fetch and populate chemical list for autocomplete
     */
    function fetchChemicalList() {
        fetch('/chemicals')
            .then(response => response.json())
            .then(data => {
                chemicalData = data;
                const chemicalList = document.getElementById('chemicalList');
                chemicalList.innerHTML = '';
                data.forEach(chemical => {
                    const option = document.createElement('option');
                    option.value = chemical.chemical_name;
                    chemicalList.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching chemical data:', error));
    }

    /**
     * Enable/disable the search button based on Label ID input
     */
    document.getElementById('labelID').addEventListener('input', function () {
        const labelID = document.getElementById('labelID').value.trim();
        const searchButton = document.getElementById('searchButton');
        const isNumeric = /^\d+$/.test(labelID);

        searchButton.disabled = !isNumeric;
        document.getElementById('labelID').classList.toggle('is-invalid', !isNumeric);
    });

    /**
     * Add row functionality
     */
    document.getElementById('addRow').addEventListener('click', function () {
        addChemicalRow();
    });

    /**
     * Search for label data and load the form
     */
    document.getElementById('searchButton').addEventListener('click', function () {
        const labelID = document.getElementById('labelID').value.trim();
        if (labelID) {
            loadLabelData(labelID);
        }
    });

/**
 * Update label functionality and print updated label
 */
 document.getElementById('updateLabel').addEventListener('click', function () {
    const labelID = document.getElementById('labelID').value.trim();
    const chemicalRows = document.querySelectorAll('#chemicalTable tbody tr');

    let totalPercentage = 0;

    chemicalRows.forEach(row => {
        const percentage = parseFloat(row.querySelector('.percentage')?.value.trim());
        if (!isNaN(percentage)) {
            totalPercentage += percentage;
        }
    });

    if (totalPercentage !== 100) {
        alert(`The total percentage must equal 100%. Current total: ${totalPercentage}%`);
        return; // Prevent the update if the total is not valid
    }

    // Proceed with the update
    const updatedData = {
        quantity: document.getElementById('stored').value.trim(),
        units: document.getElementById('units').value,
        label_size: document.getElementById('labelSize').value,
        chemicals: Array.from(chemicalRows).map(row => ({
            chemical_name: row.querySelector('.chemical-name')?.value.trim(),
            cas_number: row.querySelector('.cas-number')?.value.trim(),
            percentage: row.querySelector('.percentage')?.value.trim(),
        })),
    };

    fetch(`/editLabel/${labelID}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': crsfToken,
        },
        body: JSON.stringify(updatedData),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Label updated successfully!');
                // Optionally reload or reset the form
            } else {
                alert(data.error || 'Error updating label.');
            }
        })
        .catch(error => console.error('Error updating label:', error));
});



    // Initialize chemical list on page load
    fetchChemicalList();
});

function generatePDF(labelData) {
    const { jsPDF } = window.jspdf;

    const pageWidth = 215.9;  // 8.5 inches in mm
    const pageHeight = 279.4; // 11 inches in mm
    let labelHeight, labelWidth, offsetX, offsetY, fontSize, lineSpacing, tableColumnSpacing, additionalSpacing;

    // Fetch the labelSize from the form
    const labelSize = document.getElementById("labelSize").value;

    // Initialize the PDF with A4 (8.5 x 11 inch) dimensions
    let doc = new jsPDF({ unit: "mm", format: [pageWidth, pageHeight] });

    // Adjust label dimensions based on label size
    if (labelSize === "Small") {
        labelHeight = 25.4;
        labelWidth = 25.4;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 4;
        lineSpacing = 2.5;
        tableColumnSpacing = { chemical: 2, cas: 12, percent: 20 };
        additionalSpacing = 1;
    } else if (labelSize === "Medium") {
        labelHeight = 76.2;
        labelWidth = 50.8;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 6;
        lineSpacing = 4.5;
        tableColumnSpacing = { chemical: 2, cas: 20, percent: 34 };
        additionalSpacing = 2;
    } else if (labelSize === "Large") {
        labelHeight = 152.4;
        labelWidth = 101.6;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 9;
        lineSpacing = 7.5;
        tableColumnSpacing = { chemical: 15, cas: 60, percent: 80 };
        additionalSpacing = 6;
    } else {
        // Add a fallback case for invalid sizes
        console.error('Invalid label size:', labelSize);
        alert('Invalid label size selected. Please select a valid label size.');
        return; // Prevent further execution
    }

    // Draw border around the label
    doc.rect(offsetX, offsetY, labelWidth, labelHeight);

    // Label Header
    doc.setFont("Helvetica", "bold");
    if (labelSize === "Small"){
        doc.setFontSize(fontSize + 2);
    }
    if (labelSize === "Medium"){
        doc.setFontSize(fontSize + 5);
    }
    if (labelSize === "Large"){
        doc.setFontSize(fontSize + 10);
    }
    doc.text("UNWANTED MATERIAL", offsetX + labelWidth / 2, offsetY + fontSize + 2, { align: "center" });
    doc.setFont("Helvetica", "normal");
    doc.setFontSize(fontSize);

    // Main information with dynamic line spacing

    let contentY = offsetY + fontSize + 5;

    if (labelSize == "Small") {
        doc.text(`Label ID:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(String(labelData.label_id) || 'N/A', offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Date:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.date_created, offsetX + 12, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Room #:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.room_number, offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");
    }
    else if (labelSize == "Medium") {
        doc.text(`Label ID:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(String(labelData.label_id) || 'N/A', offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Date:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.date_created, offsetX + 12, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Room #:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.room_number, offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Professor Investigator:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.principal_investigator, offsetX + 27, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Department:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.department, offsetX + 18, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Building:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.building, offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Quantity:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(`${labelData.quantity} ${labelData.units}`, offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");
    }
    else {

        doc.text(`Label ID:`, offsetX + 4, contentY + 1);
        doc.setFont("Helvetica", "bold");
        doc.text(String(labelData.label_id) || 'N/A', offsetX + 20, contentY + 1);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Date:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.date_created, offsetX + 15, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Room #:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.room_number, offsetX + 20, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Professor Investigator:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.principal_investigator, offsetX + 40, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Department:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.department, offsetX + 25, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Building:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.building, offsetX + 20, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Quantity:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(`${labelData.quantity} ${labelData.units}`, offsetX + 20, contentY);
        doc.setFont("Helvetica", "normal");
    }

    contentY += lineSpacing + additionalSpacing;
    doc.setFont("Helvetica", "bold");
    doc.text("Chemical", offsetX + tableColumnSpacing.chemical, contentY);
    doc.text("CAS #", offsetX + tableColumnSpacing.cas, contentY);
    doc.text("%", offsetX + tableColumnSpacing.percent, contentY);
    doc.setFont("Helvetica", "normal");

    contentY += 1;
    doc.line(offsetX + 2, contentY, offsetX + labelWidth - 2, contentY);

    (labelData.chemicals || []).slice(0, labelSize === "Small" ? 2 : labelSize === "Medium" ? 5 : 8).forEach((chemical) => {
        contentY += lineSpacing;
        doc.text(chemical.chemical_name, offsetX + tableColumnSpacing.chemical, contentY);
        doc.text(chemical.cas_number, offsetX + tableColumnSpacing.cas, contentY);
        doc.text(String(chemical.percentage), offsetX + tableColumnSpacing.percent, contentY);
    });

    doc.save(`label_${String(labelData.label_id) || "N/A"}.pdf`);
}



</script>

@endsection
