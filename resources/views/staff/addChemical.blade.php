@extends('staff.templateStaff')

@section('title', 'Add Chemical')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<style>
    .content-area {
        margin-left: 120px;
        padding: 1.25rem;
        margin-top: 25px;
    }
    .form-label {
        font-weight: bold;
    }
    .btn-primary, .btn-success {
        font-weight: bold;
    }
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
    <div class="text-center mb-4">
        <h1 class="display-5">Add Chemical</h1>
        <hr class="my-4">
    </div>

    <fieldset>
        <legend>Chemical Details</legend>
        <form id="chemicalForm" class="row">
            <div class="col-md-5">
                <label for="chemicalName" class="form-label">Chemical Name</label>
                <input type="text" class="form-control" id="chemicalName" placeholder="Enter Chemical Name" required minlength="3">
                <div class="invalid-feedback">Please enter a valid chemical name (at least 3 characters).</div>
            </div>
            <div class="col-md-5">
                <label for="casNumber" class="form-label">CAS Number</label>
                <input type="text" class="form-control" id="casNumber" placeholder="Format XXXXX-XX-X" required>
                <div class="invalid-feedback">CAS Number format should be 'XXXXXX-XX-X'.</div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100 fw-bold" id="addChemicalBtn" disabled>Add Chemical</button>
            </div>
        </form>
    </fieldset>
</div>
@endsection

@section('scripts')
<script>
// CSRF token for secure requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Select fields and buttons
const chemicalNameInput = document.getElementById('chemicalName');
const casNumberInput = document.getElementById('casNumber');
const addChemicalBtn = document.getElementById('addChemicalBtn');

// Enable/Disable Add Button Based on Validation
function toggleAddChemicalButton() {
    const chemicalName = chemicalNameInput.value.trim();
    const casNumber = casNumberInput.value.trim();
    const isChemicalNameValid = /^[a-zA-Z0-9\s%.,-]+$/.test(chemicalName) && /[a-zA-Z]/.test(chemicalName);
    const isCasNumberValid = /^\d{2,6}-\d{2}-\d{1}$/.test(casNumber);
    addChemicalBtn.disabled = !(isChemicalNameValid && isCasNumberValid);
}

// Clear Form After Successful Addition
function clearForm() {
    chemicalNameInput.value = '';
    casNumberInput.value = '';
    addChemicalBtn.disabled = true;
}

// Event Listeners for Input Validation
chemicalNameInput.addEventListener('input', toggleAddChemicalButton);
casNumberInput.addEventListener('input', toggleAddChemicalButton);

// Add Chemical Functionality
function addChemical() {
    const chemicalName = chemicalNameInput.value.trim().toLowerCase();
    const casNumber = casNumberInput.value.trim();

    // Check if chemical already exists
    fetch(`/chemicalSearch?chemical_name=${encodeURIComponent(chemicalName)}`)
        .then(response => {
            if (response.ok) {
                return response.json(); // Parse JSON for valid responses
            } else if (response.status === 404) {
                return []; // Return an empty array on 404 (no matching chemical)
            } else {
                throw new Error(`Error while checking chemical existence: ${response.statusText}`);
            }
        })
        .then(data => {
            // Check for duplicates
            const duplicate = data.find(
                chem => chem.cas_number === casNumber && chem.chemical_name.toLowerCase() === chemicalName
            );

            if (duplicate) {
                alert("This chemical already exists.");
                return Promise.reject('Duplicate chemical found.'); // Exit the promise chain
            }

            // Proceed to add the chemical
            return fetch('/chemicalCreateStaff', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    chemical_name: chemicalName,
                    cas_number: casNumber,
                    status_of_chemical: 1, // Set status as active by default
                }),
            });
        })
        .then(response => {
            if (response.ok) {
                alert('Chemical added successfully!');
                clearForm(); // Clear the form fields
            } else {
                return response.json().then(errData => {
                    throw new Error(errData.message || 'Failed to add chemical.');
                });
            }
        })
        .catch(error => {
            if (error !== 'Duplicate chemical found.') {
                console.error('Error:', error);
                alert('Failed to add chemical. Please try again.');
            }
        });
}




// Event Listener for Add Button
addChemicalBtn.addEventListener('click', addChemical);
</script>
@endsection
