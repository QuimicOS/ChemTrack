@extends('staff.templateStaff')

<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Create Label - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 260px;
        padding: 1.25rem;
        margin-top: 70px;
    }

    /* Autocomplete dropdown styling */
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        top: 97%;
        margin-left: 0px;
    }

    .autocomplete-item {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    .autocomplete-item:hover {
        background-color: #e9e9e9;
    }

    /* Red border for invalid percentage */
    .invalid-percentage {
        border-color: red;
    }

    /* Styling for fieldsets and legends */
    fieldset {
        border: 1px solid #ccc;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background-color: #f8f9fa;
    }

    legend {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 0 0.5rem;
    }
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Create Label</h1>
    <hr class="my-4">
</div>

<!-- Create Label Form -->
<div class="row">
    <div class="col-md-6">
        <!-- Block 1: Basic Information -->
        <fieldset>
            <legend>Basic Information</legend>
            <div class="mb-3">
                <label for="createdBy" class="form-label">Created By (Username)</label>
                <input type="text" class="form-control" id="createdBy" value={{Auth::user()->email}} readonly>
            </div>
            <div class="mb-3">
                <label for="dateCreated" class="form-label">Date</label>
                <input type="date" class="form-control" id="dateCreated" name="date_created">
            </div>
        </fieldset>

        <!-- Block 2: Location Details -->
        <fieldset>
            <legend>Location Details</legend>
            <div class="mb-3">
                <label for="roomNumber" class="form-label">Room Number</label>
                <select class="form-select" id="roomNumber" required>
                    <option value="" selected>Select Room Number</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Select Room Number to autofill" readonly>
            </div>
            <div class="mb-3">
                <label for="building" class="form-label">Building</label>
                <input type="text" class="form-control" id="building" placeholder="Select Room Number to autofill" readonly>
            </div>            
            <div class="mb-3">
                <label for="labName" class="form-label">Laboratory Name</label>
                <input type="text" class="form-control" id="labName" placeholder="Select Room Number to autofill" readonly>
            </div>
            <div class="mb-3">
                <label for="principalInvestigator" class="form-label">Principal Investigator</label>
                <input type="text" class="form-control" id="principalInvestigator" placeholder="Select Room Number to autofill" readonly>
            </div>
        </fieldset>
    </div>

    <div class="col-md-6">
        <!-- Block 3: Container Details -->
        <fieldset>
            <legend>Container Details</legend>
            <div class="mb-3">
                <label for="containerSize" class="form-label">Container Capacity</label>
                <input type="text" class="form-control" id="containerSize" placeholder="Enter container capacity (ex. 6 gallons)" required>
            </div>
            <div class="mb-3">
                <label for="stored" class="form-label">Added Quantity</label>
                <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity (ex. 4.6, 7)" oninput="validateStoredInput()" required>
            </div>
            <div class="mb-3">
                <label for="units" class="form-label">Units</label>
                <select class="form-select" id="units" required>
                    <option value="" selected>Select units</option>
                    <option value="Gallons">Gallons (gal)</option>
                    <option value="Milliliters">Milliliters (mL)</option>
                    <option value="Liters">Liters (L)</option>
                    <option value="Grams">Grams (g)</option>
                    <option value="Kilograms">Kilograms (kg)</option>
                    <option value="Pounds">Pounds (lbs)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="labelSize" class="form-label">Label Size</label>
                <select class="form-select" id="labelSize" required>
                    <option value="" selected>Select label size</option>
                    <option value="Small">Small (1x1)</option>
                    <option value="Medium">Medium (3x2)</option>
                    <option value="Large">Large (6x4)</option>
                </select>
            </div>
        </fieldset>
    </div>
</div>
<!-- Chemical Table -->
<fieldset>
    <legend>Chemicals</legend>
    <div class="mb-3">
        <label for="chemicalTable" class="form-label">Chemicals</label>
        <table class="table table-bordered" id="chemicalTable">
            <thead>
                <tr>
                    <th>Chemical Name</th>
                    <th>CAS Number</th>
                    <th>Percentage</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input
                            type="text"
                            class="form-control chemical-name"
                            placeholder="Chemical Name"
                            list="chemicalList"
                            required
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            class="form-control"
                            name="cas_number[]"
                            placeholder="CAS Number"
                            readonly
                            required
                        />
                    </td>
                    <td>
                        <input
                            type="number"
                            class="form-control percentage"
                            name="percentage[]"
                            placeholder="Percentage"
                            required
                        />
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger removeRow" disabled>Remove</button>
                    </td>
                </tr>
            </tbody>            
        </table>
        <datalist id="chemicalList"></datalist> <!-- Datalist for autocomplete -->
        <button type="button" class="btn btn-primary" id="addRow">Add Row</button>
    </div>
</fieldset>


<!-- Submit button -->
<button type="button" class="btn btn-success" onclick="validateAndSubmit()">Submit and Generate PDF</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

@endsection

@section("scripts")
<script>
    let labData = [];
    let chemicalData = [];

    document.addEventListener("DOMContentLoaded", function () {
        // Set today's date for the label creation date field
        const today = new Date().toISOString().split("T")[0];
        document.getElementById("dateCreated").value = today;

        // Fetch laboratories and populate dropdowns
        fetch('/laboratories')
            .then(response => response.json())
            .then(data => {
                labData = data;
                populateLabDropdowns(data);
            })
            .catch(error => console.error('Error fetching laboratories:', error));

        // Fetch chemicals and set up autocomplete
        fetch('/chemicals')
            .then(response => response.json())
            .then(data => {
                chemicalData = data;
                populateChemicalAutocomplete(data);
                setupAutocompleteForChemicals(); // Apply autocomplete to all chemical name fields
            })
            .catch(error => console.error('Error fetching chemicals:', error));

        // Update lab details based on room number selection
        document.getElementById("roomNumber").addEventListener("change", function () {
            const selectedRoom = this.value;
            const labDetails = labData.find(lab => lab.room_number === selectedRoom);

            if (labDetails) {
            document.getElementById("labName").value = labDetails.lab_name || "N/A";
            document.getElementById("principalInvestigator").value = labDetails.professor_investigator || "N/A";
            document.getElementById("department").value = labDetails.department || "N/A";
            document.getElementById("building").value = labDetails.building_name || "N/A";
            }
        });

        // Add a new row for chemicals in the table
        document.getElementById('addRow').addEventListener('click', function () {
            const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" list="chemicalList" required></td>
                <td><input type="text" class="form-control" name="cas_number[]" placeholder="CAS Number" readonly required></td>
                <td><input type="number" class="form-control percentage" name="percentage[]" placeholder="Percentage" required></td>
                <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
            addRemoveRowListeners(); // Add event listener to the remove button
            setupAutocomplete(newRow.querySelector('.chemical-name'), chemicalData); // Enable autocomplete for the new row
        });

        // Add event listeners for removing rows
        addRemoveRowListeners();
    });

    // Populate laboratory dropdowns
    function populateLabDropdowns(data) {
        const roomNumberSelect = document.getElementById('roomNumber');
        roomNumberSelect.innerHTML = '<option value="" selected>Select Room Number</option>';

        data.forEach(lab => {
            roomNumberSelect.add(new Option(`${lab.room_number}`, lab.room_number));
        });
        }


// Setup autocomplete for a specific input field
function populateChemicalAutocomplete(data) {
    const chemicalList = document.getElementById("chemicalList");
    chemicalList.innerHTML = ""; // Clear existing options

    data.forEach((chemical) => {
        const option = document.createElement("option");
        option.value = `${chemical.chemical_name} (CAS: ${chemical.cas_number})`; // Display both name and CAS number
        option.setAttribute("data-cas", chemical.cas_number);
        option.setAttribute("data-name", chemical.chemical_name); // Add the name attribute for later use
        chemicalList.appendChild(option);
    });
}

// Setup autocomplete for a specific input field
function setupAutocomplete(inputElement, dataList) {
    inputElement.addEventListener("blur", function () {
        const val = this.value.trim();
        const selectedOption = Array.from(document.getElementById("chemicalList").options).find(
            (option) => option.value === val
        );

        if (selectedOption) {
            const casNumber = selectedOption.getAttribute("data-cas");
            const chemicalName = selectedOption.getAttribute("data-name");

            // Set the CAS number in the related field
            inputElement.closest("tr").querySelector("input[name='cas_number[]']").value = casNumber;

            // Set only the chemical name in the input field
            this.value = chemicalName;
        } else {
            // Clear invalid input
            this.value = ""; 
            inputElement.closest("tr").querySelector("input[name='cas_number[]']").value = "";
            alert("Please select a valid chemical from the list.");
        }
    });
}


// Apply autocomplete to all existing and new rows
function setupAutocompleteForChemicals() {
    document.querySelectorAll(".chemical-name").forEach((input) => {
        setupAutocomplete(input, chemicalData);
    });
}


    // Add event listeners to all "Remove" buttons
    function addRemoveRowListeners() {
        document.querySelectorAll('.removeRow').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });
    }

    // Validate the form before submission
    function validateForm() {
    let isValid = true;

    // Check required fields
    document.querySelectorAll('input[required], select[required]').forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            alert('Please fill in all required fields.');
        }
    });

    // Validate percentage inputs
    const percentages = Array.from(document.querySelectorAll('.percentage'));
    let totalPercentage = 0;

    percentages.forEach(input => {
        const value = parseFloat(input.value);
        if (isNaN(value) || value <= 0 || value > 100) {
            input.classList.add("invalid-percentage");
            alert("Each percentage must be a positive number and cannot exceed 100.");
            isValid = false;
        } else {
            input.classList.remove("invalid-percentage");
            totalPercentage += value;
        }
    });

    if (totalPercentage !== 100) {
        alert(`The total percentage of all chemicals must be exactly 100. Current total: ${totalPercentage}`);
        isValid = false;
    }

    return isValid;
}

    // Submit the form data to the server
    function validateAndSubmit() {
    if (validateForm()) {
        const labelData = {
            created_by: document.getElementById("createdBy").value,
            date_created: document.getElementById("dateCreated").value,
            department: document.getElementById("department").value,
            building: document.getElementById("building").value,
            room_number: document.getElementById("roomNumber").value,
            lab_name: document.getElementById("labName").value,
            principal_investigator: document.getElementById("principalInvestigator").value,
            container_size: document.getElementById("containerSize").value,
            quantity: document.getElementById("stored").value,
            units: document.getElementById("units").value,
        };

        const contentData = Array.from(document.querySelectorAll("#chemicalTable tbody tr")).map(row => ({
            chemical_name: row.cells[0].querySelector('input').value,
            cas_number: row.cells[1].querySelector('input').value,
            percentage: parseFloat(row.cells[2].querySelector('input').value),
        }));

        // Check for duplicate chemical names and CAS numbers
        const duplicates = contentData.filter((chemical, index, self) => 
            self.findIndex(c => c.chemical_name === chemical.chemical_name && c.cas_number === chemical.cas_number) !== index
        );

        if (duplicates.length > 0) {
            alert(`Duplicate chemical entries found: ${duplicates.map(d => `${d.chemical_name} (CAS: ${d.cas_number})`).join(", ")}`);
            return; // Stop the submission
        }

        fetch('/Stafflabels', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ label: labelData, content: contentData }),
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Label created successfully');
                labelData.label_id = data.data.label_id;
                labelData.chemicals = contentData;
                generatePDF(labelData);
            } else {
                alert(`Error creating label: ${data.message}`);
            }
        })
        .catch(error => {
            alert(`Failed to create label: ${error.message}`);
        });
    }
}

    //Look if the percentage value its greater than 0
    function validateStoredInput() {
    const storedInput = document.getElementById("stored");
    const value = parseFloat(storedInput.value);

    if (isNaN(value) || value <= 0) {
        storedInput.style.borderColor = "red";
        alert("Please enter a valid positive number for the stored quantity.");
    } else {
        storedInput.style.borderColor = "";
    }
    }

function generatePDF(labelData) {
    const { jsPDF } = window.jspdf;

    const pageWidth = 215.9; // 8.5 inches in mm
    const pageHeight = 279.4; // 11 inches in mm
    let labelHeight, labelWidth, offsetX, offsetY, fontSize, lineSpacing, tableColumnSpacing, additionalSpacing, maxChemicals, noteOffset;

    // Fetch the labelSize from the form
    const labelSize = document.getElementById("labelSize").value;

    // Initialize the PDF with A4 (8.5 x 11 inch) dimensions
    let doc = new jsPDF({ unit: "mm", format: [pageWidth, pageHeight] });

    // Adjust label dimensions and note offset based on label size
    if (labelSize === "Small") {
        labelHeight = 25.4;
        labelWidth = 25.4;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 4;
        lineSpacing = 2.5;
        tableColumnSpacing = { chemical: 2, cas: 12, percent: 20 };
        additionalSpacing = 1;
        maxChemicals = 2;
        noteOffset = 3; // Vertical offset for the note
    } else if (labelSize === "Medium") {
        labelHeight = 76.2;
        labelWidth = 50.8;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 6;
        lineSpacing = 4.5;
        tableColumnSpacing = { chemical: 2, cas: 20, percent: 34 };
        additionalSpacing = 2;
        maxChemicals = 5;
        noteOffset = 6;
    } else if (labelSize === "Large") {
        labelHeight = 152.4;
        labelWidth = 101.6;
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 9;
        lineSpacing = 7.5;
        tableColumnSpacing = { chemical: 15, cas: 60, percent: 80 };
        additionalSpacing = 6;
        maxChemicals = 8;
        noteOffset = 10;
    } else {
        console.error('Invalid label size:', labelSize);
        alert('Invalid label size selected. Please select a valid label size.');
        return; // Prevent further execution
    }

    // Draw border around the label
    doc.rect(offsetX, offsetY, labelWidth, labelHeight);

    // Check if there are more chemicals than can be displayed
    const totalChemicals = (labelData.chemicals || []).length;
    if (totalChemicals > maxChemicals) {
        doc.setFont("Helvetica", "italic");
        doc.setFontSize(3);
        doc.text(
            "Note: Additional chemicals are listed in the system.",
            offsetX + labelWidth / 2,
            offsetY + 1, // Adjusted position to prevent overlap
            { align: "center" }
        );
    }
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
