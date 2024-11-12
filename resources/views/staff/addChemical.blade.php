@extends('staff.templateStaff')

@section('title', 'Add Chemicals')

@section('content')
<style>
    /* Align content area with sidebar and navbar */
    .content-area {
        margin-left: 120px; /* Consistent with sidebar width */
        padding: 1.25rem;
        margin-top: 25px; /* Ensure alignment with other sections */
    }
    .table-container {
        margin-top: 20px;
    }
    .form-label {
        font-weight: bold;
    }
    .btn-primary, .btn-secondary {
        font-weight: bold;
    }
    .error-message {
        color: red;
        font-size: 0.9em;
    }
    /* Add styling for fieldsets and legends for clarity */
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

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Add Chemicals</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Add Chemical) -->
    <fieldset>
        <legend>Chemical Details</legend>
        <div class="mb-5">
            <form id="chemicalForm" class="row">
                <div class="col-md-5">
                    <label for="chemicalName" class="form-label">Chemical Name</label>
                    <input type="text" class="form-control" id="chemicalName" placeholder="Enter Chemical Name" required>
                    <div class="error-message" id="chemicalNameError"></div>
                </div>
                <div class="col-md-5">
                    <label for="casNumber" class="form-label">CAS Number</label>
                    <input type="text" class="form-control" id="casNumber" placeholder="Enter CAS Number" required>
                    <div class="error-message" id="casNumberError"></div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="addChemicalBtn" onclick="validateForm()" disabled>Add Chemical</button>
                </div>
            </form>
        </div>
    </fieldset>
</div>
@endsection

@section('scripts')
<script>
// Dummy array to store chemicals
let chemicals = [];

// Function to toggle Add Chemical button based on field validity
function toggleAddChemicalButton() {
    const chemicalName = document.getElementById('chemicalName').value;
    const casNumber = document.getElementById('casNumber').value;
    const isChemicalNameValid = /^[a-zA-Z0-9\s%.,-]+$/.test(chemicalName) && /[a-zA-Z]/.test(chemicalName); // Requires at least one letter
    const isCasNumberValid = /^\d{2,6}-\d{2}-\d{1}$/.test(casNumber); // Matches format XXXXXX-XX-X

    // Enable button only if both fields are valid
    document.getElementById('addChemicalBtn').disabled = !(isChemicalNameValid && isCasNumberValid);
}

// Add event listeners to trigger validation as user types
document.getElementById('chemicalName').addEventListener('input', toggleAddChemicalButton);
document.getElementById('casNumber').addEventListener('input', toggleAddChemicalButton);

// Function to validate form and add chemical if valid
function validateForm() {
    // Clear previous error messages
    document.getElementById('chemicalNameError').textContent = '';
    document.getElementById('casNumberError').textContent = '';

    const chemicalName = document.getElementById('chemicalName').value.trim();
    const casNumber = document.getElementById('casNumber').value.trim();

    // Check for duplicate chemical entry
    const duplicateChemical = chemicals.some(chemical => 
        chemical.chemical_name.toLowerCase() === chemicalName.toLowerCase() &&
        chemical.cas_number === casNumber
    );

    if (duplicateChemical) {
        document.getElementById('chemicalNameError').textContent = 'This chemical with the same CAS number already exists.';
        return;
    }

    // Add chemical if validation is successful
    addChemical(chemicalName, casNumber);
}

// Function to add chemical and generate JSON
function addChemical(chemicalName, casNumber) {
    const chemicalData = {
        chemical_name: chemicalName,
        cas_number: casNumber
    };

    // Push the data to the chemicals array
    chemicals.push(chemicalData);

    // Generate JSON file
    const chemicalJSON = JSON.stringify(chemicals, null, 4); // Pretty print with 4-space indentation
    downloadJSON(chemicalJSON, 'chemicals.json');

    // Clear form fields after successful addition
    clearForm();
}

// Function to clear form fields
function clearForm() {
    document.getElementById('chemicalForm').reset();
    document.getElementById('addChemicalBtn').disabled = true; // Reset Add Chemical button to disabled state
}

// Function to download JSON file
function downloadJSON(content, fileName) {
    const a = document.createElement('a');
    const file = new Blob([content], { type: 'application/json' });
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}
</script>
@endsection
