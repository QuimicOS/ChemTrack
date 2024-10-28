@extends('admin.templateAdmin')

@section('title', 'Create Label - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 270px;
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
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Create Label</h1>
    <hr class="my-4">
</div>

<!-- Create Label Form -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="createdBy" class="form-label">Created By (Username)</label>
            <input type="text" class="form-control" id="createdBy" value="maria.gomez" readonly>
        </div>
        <div class="mb-3">
            <label for="dateCreated" class="form-label">Date</label>
            <input type="date" class="form-control" id="dateCreated">
        </div>
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" placeholder="Enter department">
        </div>
        <div class="mb-3">
            <label for="building" class="form-label">Building</label>
            <input type="text" class="form-control" id="building" placeholder="Enter building">
        </div>
        <div class="mb-3">
            <label for="roomNumber" class="form-label">Room Number</label>
            <select class="form-select" id="roomNumber">
                <option selected>Select Room Number</option>
                <option value="101">Room 101</option>
                <option value="102">Room 102</option>
                <option value="201">Room 201</option>
                <option value="202">Room 202</option>
                <!-- Add more room options as needed -->
            </select>
        </div>
        <div class="mb-3">
            <label for="labName" class="form-label">Laboratory Name</label>
            <input type="text" class="form-control" id="labName" placeholder="Enter laboratory name" readonly>
        </div>
        <div class="mb-3">
            <label for="principalInvestigator" class="form-label">Principal Investigator</label>
            <input type="text" class="form-control" id="principalInvestigator" placeholder="Enter principal investigator" >
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="containerSize" class="form-label">Container Size</label>
            <input type="text" class="form-control" id="containerSize" placeholder="Enter container size">
        </div>
        <div class="mb-3">
            <label for="stored" class="form-label">Stored</label>
            <input type="text" class="form-control" id="stored" placeholder="Enter stored quantity">
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
        <div class="mb-3">
            <label for="units" class="form-label">Units</label>
            <select class="form-select" id="units">
                <option selected>Select units</option>
                <option value="ml">mL</option>
                <option value="l">L</option>
                <option value="g">g</option>
                <option value="kg">kg</option>
            </select>
        </div>
    </div>
</div>

<!-- Chemical Table -->
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
                <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" required></td>
                <td><input type="text" class="form-control" name="cas_number[]" placeholder="CAS Number" required></td>
                <td><input type="number" class="form-control" name="percentage[]" placeholder="Percentage" required></td>
                <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-success" id="addRow">Add Row</button>
</div>

<!-- Submit button -->
<button type="button" class="btn btn-primary" onclick="validateAndShowSummary()">Submit</button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Automatically set today's date
        const today = new Date().toISOString().substr(0, 10);
        document.getElementById("dateCreated").value = today;

        // Add row functionality
        document.getElementById('addRow').addEventListener('click', function() {
            const tableBody = document.getElementById('chemicalTable').getElementsByTagName('tbody')[0];
            const newRow = tableBody.insertRow();
            newRow.innerHTML = `
                <td><input type="text" class="form-control chemical-name" placeholder="Chemical Name" required></td>
                <td><input type="text" class="form-control" name="cas_number[]" placeholder="CAS Number" required></td>
                <td><input type="number" class="form-control" name="percentage[]" placeholder="Percentage" required></td>
                <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
            `;
            addRemoveRowListeners();
            applyAutocomplete(newRow.querySelector('.chemical-name'));  // Apply autocomplete to the new row
        });

        // Remove row functionality
        addRemoveRowListeners();
        applyAutocomplete(document.querySelector('.chemical-name')); // Apply autocomplete on initial row
    });

    // Predefined list of chemicals for autocomplete
    const chemicalNames = ["Acetone", "Ethanol", "Methanol", "Toluene", "Benzene"];

    // Autocomplete function
    function applyAutocomplete(input) {
        input.addEventListener("input", function() {
            closeAllLists();
            if (!this.value) return false;
            const list = document.createElement("div");
            list.setAttribute("id", this.id + "autocomplete-list");
            list.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(list);

            chemicalNames.forEach(name => {
                if (name.toLowerCase().includes(this.value.toLowerCase())) {
                    const item = document.createElement("div");
                    item.classList.add("autocomplete-item");
                    item.innerHTML = "<strong>" + name.substr(0, this.value.length) + "</strong>" + name.substr(this.value.length);
                    item.innerHTML += "<input type='hidden' value='" + name + "'>";
                    item.addEventListener("click", function() {
                        input.value = this.querySelector("input").value;
                        closeAllLists();
                    });
                    list.appendChild(item);
                }
            });
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
        const fileName = 'label_' + new Date().getTime() + '.json';
        const json = JSON.stringify(labelData, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = fileName;
        link.click();
    }

    // Validate required fields
    function validateForm() {
        let isValid = true;
        document.querySelectorAll('input[required], select[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                alert('Please fill in all required fields.');
            }
        });
        return isValid;
    } 

    function generatePDF(labelData) {
    const { jsPDF } = window.jspdf;

    // Set PDF dimensions based on label size
    let doc;
    let width, height;
    if (labelData.label_size === "Small") {
        width = 25.4; // 1 inch in mm
        height = 25.4;
        doc = new jsPDF({ unit: "mm", format: [width, height] });
        doc.setFontSize(5); // Smaller font for compact space
    } else if (labelData.label_size === "Medium") {
        width = 76.2; // 3x2 inch in mm
        height = 50.8;
        doc = new jsPDF({ unit: "mm", format: [width, height] });
        doc.setFontSize(8);
    } else if (labelData.label_size === "Large") {
        width = 152.4; // 6x4 inch in mm
        height = 101.6;
        doc = new jsPDF({ unit: "mm", format: [width, height] });
        doc.setFontSize(10);
    }

    // Center position for the header and other content
    const centerX = width / 2;

    // Header text
    doc.text("UNWANTED MATERIAL", centerX, 5, { align: "center" });

    // Label information
    let y = 10;
    doc.text(`Label ID: ${labelData.label_id || 'N/A'}`, centerX, y, { align: "center" });
    y += 4;
    doc.text(`Date: ${labelData.date_created}`, centerX, y, { align: "center" });
    y += 4;
    doc.text(`Created by: ${labelData.created_by}`, centerX, y, { align: "center" });
    y += 4;
    doc.text(`Room #: ${labelData.room_number}`, centerX, y, { align: "center" });

    // Table for Chemical Data, centered
    y += 6;
    const tableWidth = 50; // Set a specific width for the table to center it properly
    const startX = centerX - tableWidth / 2;
    const rowHeight = 4;
    const colWidths = [20, 5, 20]; // Column widths for Chemical, %, CAS #

    // Table Headers
    doc.text("Chemical", startX + colWidths[0] / 2, y, { align: "center" });
    doc.text("%", startX + colWidths[0] + colWidths[1] / 2, y, { align: "center" });
    doc.text("CAS #", startX + colWidths[0] + colWidths[1] + colWidths[2] / 2, y, { align: "center" });

    y += 2;

    // Draw table header borders
    doc.line(startX, y, startX + tableWidth, y); // Top border
    doc.line(startX, y, startX, y + rowHeight * 2); // Left border
    doc.line(startX + colWidths[0], y, startX + colWidths[0], y + rowHeight * 2); // 1st column divider
    doc.line(startX + colWidths[0] + colWidths[1], y, startX + colWidths[0] + colWidths[1], y + rowHeight * 2); // 2nd column divider
    doc.line(startX + tableWidth, y, startX + tableWidth, y + rowHeight * 2); // Right border

    // Chemical data rows (limited to two for small labels)
    labelData.chemicals.slice(0, 2).forEach((chemical, index) => {
        y += rowHeight;
        doc.text(chemical.chemical_name, startX + colWidths[0] / 2, y, { align: "center" });
        doc.text(String(chemical.percentage), startX + colWidths[0] + colWidths[1] / 2, y, { align: "center" });
        doc.text(chemical.cas_number, startX + colWidths[0] + colWidths[1] + colWidths[2] / 2, y, { align: "center" });

        // Draw row borders
        doc.line(startX, y + rowHeight, startX + tableWidth, y + rowHeight); // Bottom border of each row
    });

    // Save as PDF
    doc.save(`label_${labelData.label_id || 'N/A'}.pdf`);
}

  
</script>
@endsection
