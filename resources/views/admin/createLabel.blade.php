@extends('admin.templateAdmin')

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
                <input type="date" class="form-control" id="dateCreated" name="date_created" readonly>
            </div>
        </fieldset>

        <!-- Block 2: Location Details -->
        <fieldset>
            <legend>Location Details</legend>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" required>
                    <option value="" selected>Select Department</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="building" class="form-label">Building</label>
                <select class="form-select" id="building" required>
                    <option value="" selected>Select Building</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="roomNumber" class="form-label">Room Number</label>
                <select class="form-select" id="roomNumber" required>
                    <option value="" selected>Select Room Number</option>
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
                <!-- Existing rows are dynamically populated -->
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
                document.getElementById("labName").value = labDetails.lab_name;
                document.getElementById("principalInvestigator").value = labDetails.professor_investigator;
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
        const departmentSelect = document.getElementById('department');
        const buildingSelect = document.getElementById('building');
        const roomNumberSelect = document.getElementById('roomNumber');

        data.forEach(lab => {
            if (!Array.from(departmentSelect.options).some(option => option.value === lab.department)) {
                departmentSelect.add(new Option(lab.department, lab.department));
            }
            if (!Array.from(buildingSelect.options).some(option => option.value === lab.building_name)) {
                buildingSelect.add(new Option(lab.building_name, lab.building_name));
            }
            roomNumberSelect.add(new Option(`Room ${lab.room_number}`, lab.room_number));
        });
    }

    // Populate the datalist for chemical autocomplete
    function populateChemicalAutocomplete(data) {
        const chemicalList = document.getElementById('chemicalList');
        chemicalList.innerHTML = ''; // Clear existing options

        data.forEach(chemical => {
            const option = document.createElement('option');
            option.value = chemical.chemical_name;
            option.setAttribute("data-cas", chemical.cas_number);
            chemicalList.appendChild(option);
        });
    }

    // Setup autocomplete for a specific input field
    function setupAutocomplete(inputElement, dataList) {
        inputElement.addEventListener("input", function () {
            const val = this.value.toLowerCase();
            const matchingChemical = dataList.find(chemical => chemical.chemical_name.toLowerCase() === val);

            if (matchingChemical) {
                inputElement.closest("tr").querySelector("input[name='cas_number[]']").value = matchingChemical.cas_number;
            } else {
                inputElement.closest("tr").querySelector("input[name='cas_number[]']").value = "";
            }
        });

        // Enforce valid selection
        inputElement.addEventListener("blur", function () {
            const val = this.value.toLowerCase();
            const matchingChemical = dataList.find(chemical => chemical.chemical_name.toLowerCase() === val);

            if (!matchingChemical) {
                this.value = "";
                this.closest("tr").querySelector("input[name='cas_number[]']").value = "";
                alert("Please select a valid chemical from the list.");
            }
        });
    }

    // Apply autocomplete to all existing chemical name fields
    function setupAutocompleteForChemicals() {
        document.querySelectorAll('.chemical-name').forEach(input => {
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
            //label_size: document.getElementById("labelSize").value,
            units: document.getElementById("units").value,
        };

        const contentData = Array.from(document.querySelectorAll("#chemicalTable tbody tr")).map(row => ({
            chemical_name: row.cells[0].querySelector('input').value,
            cas_number: row.cells[1].querySelector('input').value,
            percentage: row.cells[2].querySelector('input').value,
        }));

        fetch('/labels', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ label: labelData, content: contentData })
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
            console.log('Parsed Response:', data);
            if (data.success) {
                alert('Label created successfully');
                generatePDF(data.data); // Generate the PDF with label data
            } else {
                alert(`Error creating label: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert(`Failed to create label: ${error.message}`);
        });
    }
}


    function validateStoredInput() {
    const storedInput = document.getElementById("stored");
    const value = parseFloat(storedInput.value);

    if (isNaN(value) || value < 0) {
        storedInput.style.borderColor = "red";
        alert("Please enter a valid positive number for the stored quantity.");
    } else {
        storedInput.style.borderColor = "";
    }
}

</script>
@endsection
