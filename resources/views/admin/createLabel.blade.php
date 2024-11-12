@extends('admin.templateAdmin')

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
                <input type="text" class="form-control" id="createdBy" value="juan.pablo@upr.edu" readonly>
            </div>
            <div class="mb-3">
                <label for="dateCreated" class="form-label">Date</label>
                <input type="date" class="form-control" id="dateCreated" readonly>
            </div>
        </fieldset>

        <!-- Block 2: Location Details -->
        <fieldset>
            <legend>Location Details</legend>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department">
                    <option selected>Select Department</option>
                    <option value="INEL">INEL</option>
                    <option value="ICOM">ICOM</option>
                    <option value="BIOL">BIOL</option>
                    <option value="CHEM">CHEM</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="building" class="form-label">Building</label>
                <select class="form-select" id="building">
                    <option selected>Select Building</option>
                    <option value="Luchetti">Luchetti</option>
                    <option value="Chardon">Chardon</option>
                    <option value="Stefani">Stefani</option>
                    <option value="Admi">Admi</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="roomNumber" class="form-label">Room Number</label>
                <select class="form-select" id="roomNumber">
                    <option selected>Select Room Number</option>
                    <option value="101">Room 101</option>
                    <option value="102">Room 102</option>
                    <option value="201">Room 201</option>
                    <option value="202">Room 202</option>
                </select>
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
                <input type="text" class="form-control" id="containerSize" placeholder="Enter container capacity (ex. 6 gallons)">
            </div>
            <div class="mb-3">
                <label for="stored" class="form-label">Added Quantity</label>
                <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity (ex. 4.6, 7)" oninput="validateStoredInput()">
                <small id="storedError" class="text-danger" style="display: none;">Incorrect input: Only numeric values are allowed.</small>
            </div>
            <div class="mb-3">
                <label for="units" class="form-label">Units</label>
                <select class="form-select" id="units">
                    <option selected>Select units</option>
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
                <select class="form-select" id="labelSize">
                    <option selected>Select label size</option>
                    <option value="Small">Small (1x1)</option>
                    <option value="Medium">Medium (3x2)</option>
                    <option value="Large">Large (6x4)</option>
                </select>
            </div>
        </fieldset>
    </div>
</div>

<!-- Block 4: Chemical Table -->
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
                        <input type="text" class="form-control chemical-name" list="chemicalList" placeholder="Chemical Name">
                        <datalist id="chemicalList">
                            <option value="Acetone">
                            <option value="Ethanol">
                            <option value="Methanol">
                            <option value="Toluene">
                            <option value="Benzene">
                        </datalist>
                    </td>
                    <td><input type="text" class="form-control" name="cas_number[]" placeholder="CAS Number" required></td>
                    <td><input type="number" class="form-control percentage" name="percentage[]" placeholder="Percentage"></td>
                    <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" id="addRow">Add Row</button>
    </div>
</fieldset>

<!-- Submit button -->
<button type="button" class="btn btn-success" onclick="validateAndShowSummary()">Submit and Generate PDF</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

@endsection


@section('scripts')
<script>
    // Automatically set today's date in the Date field
    document.addEventListener("DOMContentLoaded", function() {
        // Automatically set today's date
        const today = new Date().toISOString().substr(0, 10); // Formats the date to YYYY-MM-DD
        document.getElementById("dateCreated").value = today; // Sets today's date as default

        // Add row functionality
        document.getElementById('addRow').addEventListener('click', function() {
            const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
            const newRow = tableBody.insertRow();  // Inserts a new row in the table body
            newRow.innerHTML = `
                <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" required></td>
                <td><input type="text" class="form-control" name="cas_number[]" placeholder="CAS Number" required></td>
                <td><input type="number" class="form-control percentage" name="percentage[]" placeholder="Percentage" required></td>
                <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
            `;
            addRemoveRowListeners(); // Adds event listeners for row removal
            applyAutocomplete(newRow.querySelector('.chemical-name'));  // Enables autocomplete for new row
        });

        // Remove row functionality
        addRemoveRowListeners();
        applyAutocomplete(document.querySelector('.chemical-name')); // Apply autocomplete on initial row
    });

    // Predefined list of chemicals for validation
    const chemicalNames = ["Acetone", "Ethanol", "Methanol", "Toluene", "Benzene"];

    // Autocomplete function
    function applyAutocomplete(input) {
        input.addEventListener("input", function() {
            closeAllLists();
            if (!this.value) return false; // Stops if no input
            const list = document.createElement("div");
            list.setAttribute("id", this.id + "autocomplete-list");
            list.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(list); // Appends to the input's parent
        });

         // Room to lab mapping for autofill
        const roomLabMapping = {
            '101': { labName: 'Chemistry Lab', principalInvestigator: 'Dr. Smith' },
            '102': { labName: 'Biology Lab', principalInvestigator: 'Dr. Johnson' },
            '201': { labName: 'Physics Lab', principalInvestigator: 'Dr. Lee' },
            '202': { labName: 'Engineering Lab', principalInvestigator: 'Dr. Martinez' },
            // Add more mappings as needed
        };

        // Update labName and principalInvestigator based on room selection
        document.getElementById("roomNumber").addEventListener("change", function() {
            const selectedRoom = this.value;
            const labDetails = roomLabMapping[selectedRoom];
            if (labDetails) {
                document.getElementById("labName").value = labDetails.labName;
                document.getElementById("principalInvestigator").value = labDetails.principalInvestigator;
            } else {
                document.getElementById("labName").value = '';
                document.getElementById("principalInvestigator").value = '';
            }
        });

        // Close all autocomplete lists
        function closeAllLists(elmnt) {
            const items = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < items.length; i++) {
                if (elmnt != items[i] && elmnt != input) {
                    items[i].parentNode.removeChild(items[i]);
                }
            }
        }

        // Close autocomplete list when clicking outside
        document.addEventListener("click", (e) => closeAllLists(e.target));
    }

    // Remove row listeners
    function addRemoveRowListeners() {
        document.querySelectorAll('.removeRow').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });
    }

    // Validate form and trigger download if valid
    function validateAndShowSummary() {
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
                stored: document.getElementById("stored").value,
                label_size: document.getElementById("labelSize").value,
                units: document.getElementById("units").value,
                chemicals: []
            };

            document.querySelectorAll("#chemicalTable tbody tr").forEach(row => {
                labelData.chemicals.push({
                    chemical_name: row.cells[0].querySelector('input').value,
                    cas_number: row.cells[1].querySelector('input').value,
                    percentage: row.cells[2].querySelector('input').value
                });
            });

            // Existing JSON download function
            downloadJsonFile(labelData);

            // New PDF generation function
            generatePDF(labelData);
        }
    }

    // Download JSON
    function downloadJsonFile(labelData) {
        alert('Label created Sucessfully');
        const fileName = 'label_' + new Date().getTime() + '.json';
        const json = JSON.stringify(labelData, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = fileName;
        link.click();
    }

    // Validate required fields and percentage input
    function validateForm() {
        let isValid = true;
        document.querySelectorAll('input[required], select[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                alert('Please fill in all required fields.');
            }
        });

        // Validate percentage input and highlight non-numeric inputs
        document.querySelectorAll('.percentage').forEach(input => {
            if (!/^\d+(\.\d+)?$/.test(input.value)) {
                input.classList.add("invalid-percentage");
                alert("Please enter a valid numeric value for percentage.");
                isValid = false;
            } else {
                input.classList.remove("invalid-percentage");
            }
        });

        return isValid;
    }

    function validateStoredInput() {
        const storedInput = document.getElementById("stored");
        const errorMessage = document.getElementById("storedError");

        // Regular expression to match numeric values
        const isValid = /^\d*\.?\d*$/.test(storedInput.value);

        if (isValid || storedInput.value === "") {
            errorMessage.style.display = "none"; // Hide error message if input is valid or empty
        } else {
            errorMessage.style.display = "block"; // Show error message if input is invalid
        }
    }
    
// Generate PDF document
function generatePDF(labelData) {
    const { jsPDF } = window.jspdf;

    const pageWidth = 215.9;  // 8.5 inches in mm
    const pageHeight = 279.4; // 11 inches in mm
    let labelHeight, labelWidth, offsetX, offsetY, fontSize, lineSpacing, tableColumnSpacing, additionalSpacing;

    // Initialize the PDF with A4 (8.5 x 11 inch) dimensions
    let doc = new jsPDF({ unit: "mm", format: [pageWidth, pageHeight] });

    if (labelData.label_size === "Small") {
        labelHeight = 25.4; // 1 inch (Height)
        labelWidth = 25.4;  // 1 inch (Width)
        offsetX = (pageWidth - labelWidth) / 2;  // Centering content horizontally
        offsetY = (pageHeight - labelHeight) / 2;  // Centering content vertically
        fontSize = 4;  // Smaller font size for small label
        lineSpacing = 2.5; // Compact line spacing
        tableColumnSpacing = { chemical: 2, cas: 12, percent: 20 };
        additionalSpacing = 1; // Reduced space between text and table

    } else if (labelData.label_size === "Medium") {
        labelHeight = 76.2; // 3 inches (Height)
        labelWidth = 50.8;  // 2 inches (Width)
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 6; // Moderate font size
        lineSpacing = 4.5;  // Medium line spacing
        tableColumnSpacing = { chemical: 2, cas: 20, percent: 34 };
        additionalSpacing = 2;

    } else if (labelData.label_size === "Large") {
        labelHeight = 152.4; // 6 inches (Height)
        labelWidth = 101.6; // 4 inches (Width)
        offsetX = (pageWidth - labelWidth) / 2;
        offsetY = (pageHeight - labelHeight) / 2;
        fontSize = 9; // Larger font size for readability
        lineSpacing = 7.5;  // Generous line spacing for large label
        tableColumnSpacing = { chemical: 5, cas: 50, percent: 80 };
        additionalSpacing = 6; // Extra spacing for larger label

    }

    // Draw border around the label
    doc.rect(offsetX, offsetY, labelWidth, labelHeight);

    // Label Header
    doc.setFont("Helvetica", "bold");
    doc.setFontSize(fontSize + 1);  // Slightly larger for the header
    doc.text("UNWANTED MATERIAL", offsetX + labelWidth / 2, offsetY + fontSize + 2, { align: "center" });
    doc.setFont("Helvetica", "normal");
    doc.setFontSize(fontSize);  // Set back to standard font size

    // Main information with dynamic line spacing
    let contentY = offsetY + fontSize + 5;
    doc.text(`Label ID:`, offsetX + 4, contentY);
    doc.setFont("Helvetica", "bold");
    doc.text(labelData.label_id || 'N/A', offsetX + 25, contentY);
    doc.setFont("Helvetica", "normal");

    contentY += lineSpacing;
    doc.text(`Date:`, offsetX + 4, contentY);
    doc.setFont("Helvetica", "bold");
    doc.text(labelData.date_created, offsetX + 25, contentY);
    doc.setFont("Helvetica", "normal");

    contentY += lineSpacing;
    doc.text(`Created by:`, offsetX + 4, contentY);
    doc.setFont("Helvetica", "bold");
    doc.text(labelData.created_by, offsetX + 25, contentY);
    doc.setFont("Helvetica", "normal");

    contentY += lineSpacing;
    doc.text(`Room #:`, offsetX + 4, contentY);
    doc.setFont("Helvetica", "bold");
    doc.text(labelData.room_number, offsetX + 25, contentY);
    doc.setFont("Helvetica", "normal");

    if (labelData.label_size !== "Small") {
        contentY += lineSpacing;
        doc.text(`Professor Investigator:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.principal_investigator, offsetX + 35, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Department:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.department, offsetX + 25, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Building:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(labelData.building, offsetX + 25, contentY);
        doc.setFont("Helvetica", "normal");

        contentY += lineSpacing;
        doc.text(`Stored:`, offsetX + 4, contentY);
        doc.setFont("Helvetica", "bold");
        doc.text(`${labelData.stored} ${labelData.units}`, offsetX + 25, contentY);
        doc.setFont("Helvetica", "normal");
    }

    // Space between text and table
    contentY += lineSpacing + additionalSpacing;

    // Chemical Table Header
    doc.setFont("Helvetica", "bold");
    doc.text("Chemical", offsetX + tableColumnSpacing.chemical, contentY);
    doc.text("CAS #", offsetX + tableColumnSpacing.cas, contentY);
    doc.text("%", offsetX + tableColumnSpacing.percent, contentY);
    doc.setFont("Helvetica", "normal");

    // Draw a line under the table header
    contentY += 1;
    doc.line(offsetX + 2, contentY, offsetX + labelWidth - 2, contentY);

    // Chemical Table Content - Adjust for each size
    labelData.chemicals.slice(0, labelData.label_size === "Small" ? 2 : labelData.label_size === "Medium" ? 5 : 8).forEach((chemical) => {
        contentY += lineSpacing;
        doc.text(chemical.chemical_name, offsetX + tableColumnSpacing.chemical, contentY);
        doc.text(chemical.cas_number, offsetX + tableColumnSpacing.cas, contentY);
        doc.text(String(chemical.percentage), offsetX + tableColumnSpacing.percent, contentY);
    });

    doc.save(`label_${labelData.label_id || 'N/A'}.pdf`);
}


</script>
@endsection
