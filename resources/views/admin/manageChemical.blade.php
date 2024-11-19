@extends('admin.templateAdmin')

@section('title', 'Manage Chemicals')
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
        display: none;
    }
    .form-label {
        font-weight: bold;
    }
    .btn-primary, .btn-secondary {
        font-weight: bold;
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
        <h1 class="display-5">Manage Chemicals</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Add Chemical) -->
    <fieldset>
    <div class="mb-5">
        <form id="chemicalForm" class="row">
            <div class="col-md-5">
                <label for="chemicalName" class="form-label">Chemical Name</label>
                <input type="text" class="form-control" id="chemicalName" placeholder="Enter Chemical Name" required minlength="3">
                <div class="invalid-feedback">Please enter a valid chemical name (at least 3 characters, alphanumeric and special characters allowed).</div>
            </div>
            <div class="col-md-5">
                <label for="casNumber" class="form-label">CAS Number</label>
                <input type="text" class="form-control" id="casNumber" placeholder="Enter CAS Number" required>
                <div class="invalid-feedback">CAS Number format should be 'XXXXXX-XX-X'.</div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100 fw-bold" id="addChemicalBtn" disabled>Add Chemical</button>
            </div>
        </form>
    </div>
    </fieldset>

    <!-- Search Section for Chemicals with Autocomplete -->
    <fieldset>
    <div class="mb-5">
        <form class="row">
            <div class="col-md-10">
                <label for="searchChemical" class="form-label">Search Chemical by Name</label>
                <input type="text" class="form-control" id="searchChemical" placeholder="Search by Chemical Name" list="chemicalSuggestions">
                <datalist id="chemicalSuggestions"></datalist>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="searchChemicalBtn">Search</button>
            </div>
        </form>
    </div>

    <!-- Table Section (Manage Chemicals) -->
    <div class="table-container">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Chemical Name</th>
                    <th>CAS Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="chemicalTableBody">
                <!-- Rows dynamically inserted -->
            </tbody>
        </table>
    </div>
</div>
</fieldset>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Chemical</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="editChemicalName" class="form-label">Chemical Name</label>
                        <input type="text" class="form-control" id="editChemicalName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCasNumber" class="form-label">CAS Number</label>
                        <input type="text" class="form-control" id="editCasNumber" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveEditBtn" onclick="saveEdit()">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!-- No Results Modal -->
<div class="modal fade" id="noResultsModal" tabindex="-1" aria-labelledby="noResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noResultsModalLabel">Search Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Chemical doesn't exist!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- No Results Modal -->
<div class="modal fade" id="noResultsModal" tabindex="-1" aria-labelledby="noResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noResultsModalLabel">Search Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Chemical doesn't exist!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Invalid Input Modal -->
<div class="modal fade" id="invalidInputModal" tabindex="-1" aria-labelledby="invalidInputModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invalidInputModalLabel">Invalid Input</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Chemical name must contain at least one letter and cannot be all numbers or special characters.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this chemical?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
// Global variable to store the current chemical ID for edits and deletions
let currentChemicalId = null;

// Select fields and buttons
const chemicalNameInput = document.getElementById('chemicalName');
const casNumberInput = document.getElementById('casNumber');
const addChemicalBtn = document.getElementById('addChemicalBtn');
const searchChemicalInput = document.getElementById('searchChemical');
const searchChemicalBtn = document.getElementById('searchChemicalBtn');
const editChemicalNameInput = document.getElementById('editChemicalName');
const editCasNumberInput = document.getElementById('editCasNumber');
const tableContainer = document.querySelector('.table-container');

// Array to store chemicals temporarily
let chemicals = [];

// CSRF token for secure requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Disable the search button initially
searchChemicalBtn.disabled = true;

// Toggle the search button based on input presence
searchChemicalInput.addEventListener('input', () => {
    // Enable button only if there’s input in the search field
    searchChemicalBtn.disabled = searchChemicalInput.value.trim() === '';
});

// Toggle Add button based on field validity
function toggleAddChemicalButton() {
    const chemicalName = chemicalNameInput.value.trim();
    const casNumber = casNumberInput.value.trim();
    const isChemicalNameValid = /^[a-zA-Z0-9\s%.,-]+$/.test(chemicalName) && /[a-zA-Z]/.test(chemicalName);
    const isCasNumberValid = /^\d{2,6}-\d{2}-\d{1}$/.test(casNumber);
    addChemicalBtn.disabled = !(isChemicalNameValid && isCasNumberValid);
}

// Clear form after adding a chemical
function clearForm() {
    chemicalNameInput.value = '';
    casNumberInput.value = '';
    addChemicalBtn.disabled = true;
}

// Event listeners for input validation
chemicalNameInput.addEventListener('input', toggleAddChemicalButton);
casNumberInput.addEventListener('input', toggleAddChemicalButton);

// Add chemical to the database
function addChemical() {
    const chemicalName = chemicalNameInput.value.trim();
    const casNumber = casNumberInput.value.trim();

    fetch('/chemicalCreate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ chemical_name: chemicalName, cas_number: casNumber, status_of_chemical: 1 })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to add chemical');
        return response.json();
    })
    .then(() => {
        alert('Chemical added successfully!');
        clearForm(); // Clear the form without updating the table
    })
    .catch(error => {
        console.error('Error adding chemical:', error);
        alert('Failed to add chemical. Please try again.');
    });
}


// Render chemicals in the table
function renderTable(chemicals) {
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = '';

    chemicals.forEach(chemical => {
        const row = `<tr>
                        <td>${chemical.chemical_name}</td>
                        <td>${chemical.cas_number}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editChemical(${chemical.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteChemical(${chemical.id})">Delete</button>
                        </td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
    tableContainer.style.display = 'block';
}

// Search chemicals by name
function searchChemical() {
    const chemicalName = searchChemicalInput.value.trim();
    if (!chemicalName) return alert("Please enter a chemical name to search."); // Only prompt if input is empty

    fetch(`/chemicalSearch?chemical_name=${chemicalName}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            chemicals = data;
            renderTable(chemicals);
        } else {
            alert('No chemicals found.');
            renderTable([]); // Clear the table if no chemicals found
        }
    })
    .catch(error => {
        console.error('Error loading chemicals:', error);
        alert('Failed to load chemicals.');
    });
}

// Edit chemical data
function editChemical(id) {
    const chemical = chemicals.find(c => c.id === id);
    if (!chemical) return alert("Chemical not found.");

    currentChemicalId = chemical.id; // Set the current chemical ID

    // Populate the form fields
    editChemicalNameInput.value = chemical.chemical_name;
    editCasNumberInput.value = chemical.cas_number;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

// Save edited chemical data to database
function saveEdit() {
    const updatedChemicalName = document.getElementById('editChemicalName').value.trim();
    const updatedCasNumber = document.getElementById('editCasNumber').value.trim();

    fetch(`/chemicalModify`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            chemical_id: currentChemicalId, // Use stored ID
            chemical_name: updatedChemicalName,
            cas_number: updatedCasNumber
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to update chemical');
        return response.json();
    })
    .then(data => {
        alert('Chemical updated successfully!');
        searchChemical(); // Refresh the table with updated data
    })
    .catch(error => {
        console.error('Error updating chemical:', error);
        alert('Failed to update chemical. Please try again.');
    });
}

// Delete chemical
function deleteChemical(chemicalId) {
    fetch(`/chemicalInvalidate`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ chemical_id: chemicalId })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to delete chemical');
        return response.json();
    })
    .then(() => {
        // Remove the deleted chemical from the chemicals array
        chemicals = chemicals.filter(chemical => chemical.id !== chemicalId);
        renderTable(chemicals);
        alert('Chemical has been deleted.');
    })
    .catch(error => {
        console.error('Error deleting chemical:', error);
        alert('Failed to delete chemical. Please try again.');
    });
}

// Event listeners for button actions
addChemicalBtn.addEventListener('click', addChemical);
searchChemicalBtn.addEventListener('click', searchChemical);


</script>
@endsection