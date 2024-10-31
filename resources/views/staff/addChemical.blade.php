@extends('staff/templateStaff')

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
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Add Chemicals</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Add Chemical) -->
    <div class="mb-5">
        <form id="chemicalForm" class="row">
            <div class="col-md-5">
                <label for="chemicalName" class="form-label">Chemical Name</label>
                <input type="text" class="form-control" id="chemicalName" placeholder="Enter Chemical Name" required>
                <div class="invalid-feedback">Please enter a valid chemical name (alphanumeric and special characters allowed).</div>
            </div>
            <div class="col-md-5">
                <label for="casNumber" class="form-label">CAS Number</label>
                <input type="text" class="form-control" id="casNumber" placeholder="Enter CAS Number" required>
                <div class="invalid-feedback">CAS Number format should be 'XXXXXX-XX-X'.</div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="addChemicalBtn">Add Chemical</button>
            </div>
        </form>
    </div>
    @endsection

@section('scripts')
<script>
// Dummy array to store chemicals
let chemicals = [];

// Function to validate form
function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.getElementById('chemicalNameError').textContent = '';
    document.getElementById('casNumberError').textContent = '';

    // Validate chemical name (alphanumeric + special characters: %, , . -)
    const chemicalName = document.getElementById('chemicalName').value;
    const namePattern = /^[a-zA-Z0-9\s%.,-]+$/;
    if (!chemicalName || !namePattern.test(chemicalName)) {
        document.getElementById('chemicalNameError').textContent = 'Please enter a valid chemical name (alphanumeric + % , . - allowed).';
        isValid = false;
    }

    // Validate CAS number (format XXXXXX-XX-X)
    const casNumber = document.getElementById('casNumber').value;
    const casPattern = /^\d{2,6}-\d{2}-\d{1}$/;
    if (!casNumber || !casPattern.test(casNumber)) {
        document.getElementById('casNumberError').textContent = 'CAS Number format should be XXXXXX-XX-X.';
        isValid = false;
    }

    if (isValid) {
        addChemical();
    }
}

// Function to add chemical and generate JSON
function addChemical() {
    const chemicalName = document.getElementById('chemicalName').value.trim();
    const casNumber = document.getElementById('casNumber').value.trim();

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
