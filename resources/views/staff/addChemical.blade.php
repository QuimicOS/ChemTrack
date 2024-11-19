@extends('staff.templateStaff')

@section('title', 'Add Chemicals')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
// Function to toggle Add Chemical button based on field validity
function toggleAddChemicalButton() {
    const chemicalName = document.getElementById('chemicalName').value;
    const casNumber = document.getElementById('casNumber').value;
    const isChemicalNameValid = /^[a-zA-Z0-9\s%.,-]+$/.test(chemicalName) && /[a-zA-Z]/.test(chemicalName); // Requires at least one letter
    const isCasNumberValid = /^\d{2,6}-\d{2}-\d{1}$/.test(casNumber); // Matches format XXXXXX-XX-X

    console.log("Chemical Name Valid:", isChemicalNameValid); // Debug log
    console.log("CAS Number Valid:", isCasNumberValid);       // Debug log

    // Enable button only if both fields are valid
    document.getElementById('addChemicalBtn').disabled = !(isChemicalNameValid && isCasNumberValid);
}

// Add event listeners to trigger validation as user types
document.getElementById('chemicalName').addEventListener('input', () => {
    console.log("Chemical Name Input Changed"); // Debug log
    toggleAddChemicalButton();
});
document.getElementById('casNumber').addEventListener('input', () => {
    console.log("CAS Number Input Changed"); // Debug log
    toggleAddChemicalButton();
});

// Function to validate form and add chemical if valid
function validateForm() {
    console.log("Validate Form Called"); // Debug log

    // Clear previous error messages
    document.getElementById('chemicalNameError').textContent = '';
    document.getElementById('casNumberError').textContent = '';

    const chemicalName = document.getElementById('chemicalName').value.trim();
    const casNumber = document.getElementById('casNumber').value.trim();

    addChemical(chemicalName, casNumber);
}

// Function to add chemical and post data to the server
function addChemical(chemicalName, casNumber) {
    console.log("Add Chemical Function Called"); // Debug log

    const chemicalData = {
        chemical_name: chemicalName,
        cas_number: casNumber,
        //status_of_chemical: 1 // Set status as active by default
    };

     // Get the CSRF token from the meta tag
     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/chemicalCreate`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken, // Include the CSRF token here
    },
    body: JSON.stringify({
        chemical_name: chemicalName,
        cas_number: casNumber
    })
})
    .then(response => {
        console.log("Response Status:", response.status); // Debug log
        if (!response.ok) {
            throw new Error('Failed to add chemical');
        }
        return response.json();
    })
    .then(data => {
        console.log('Chemical added successfully:', data); // Debug log
        clearForm();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add chemical. Please try again.');
    });
}

// Function to clear form fields
function clearForm() {
    document.getElementById('chemicalForm').reset();
    document.getElementById('addChemicalBtn').disabled = true; // Reset Add Chemical button to disabled state
}
</script>
@endsection
