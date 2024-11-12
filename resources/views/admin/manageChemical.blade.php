@extends('admin.templateAdmin')

@section('title', 'Manage Chemicals')

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
                <button type="button" class="btn btn-success" onclick="saveEdit()">Save changes</button>
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
// Select fields and buttons
const chemicalNameInput = document.getElementById('chemicalName');
const casNumberInput = document.getElementById('casNumber');
const addChemicalBtn = document.getElementById('addChemicalBtn');
const searchChemicalInput = document.getElementById('searchChemical');
const searchChemicalBtn = document.getElementById('searchChemicalBtn');
const editChemicalNameInput = document.getElementById('editChemicalName');
const editCasNumberInput = document.getElementById('editCasNumber'); // Field in edit modal

// Select the table container and set it to hidden initially
const tableContainer = document.querySelector('.table-container');

// Dummy array to store chemicals
let chemicals = [];
let editingIndex = -1;
let searchResults = [];
let deleteIndex = -1;

// Function to enforce only numbers and hyphen in CAS Number field
function enforceCASFormat(inputField) {
    inputField.addEventListener('keypress', function(event) {
        const char = String.fromCharCode(event.which);
        if (!/[0-9-]/.test(char)) {
            event.preventDefault();
        }
    });
    inputField.addEventListener('input', function() {
        inputField.value = inputField.value.replace(/[^0-9-]/g, ''); // Remove any non-numeric/hyphen characters
    });
}

// Apply format enforcement to main and edit CAS number fields
enforceCASFormat(casNumberInput);
enforceCASFormat(editCasNumberInput); // Ensure restriction on edit modal field

// Enable or disable the Add button based on field validity
function toggleAddButton() {
    const isChemicalNameValid = /^[a-zA-Z0-9\s%,-.]+$/.test(chemicalNameInput.value) && /[a-zA-Z]/.test(chemicalNameInput.value);
    const isCasNumberValid = /^\d{2,6}-\d{2}-\d{1}$/.test(casNumberInput.value);
    addChemicalBtn.disabled = !(isChemicalNameValid && isCasNumberValid);
}

// Enable or disable the Search button based on field content
function toggleSearchButton() {
    searchChemicalBtn.disabled = searchChemicalInput.value.trim() === '';
}

// Add event listeners for real-time validation
chemicalNameInput.addEventListener('input', toggleAddButton);
casNumberInput.addEventListener('input', toggleAddButton);
searchChemicalInput.addEventListener('input', toggleSearchButton);

// Initialize Search button and Add button state on page load
toggleSearchButton();
toggleAddButton(); 

// Show invalid input modal
function showInvalidInputModal() {
    const invalidInputModal = new bootstrap.Modal(document.getElementById('invalidInputModal'));
    invalidInputModal.show();
}

// Show Delete Confirmation Modal
function showDeleteConfirmation(index) {
    deleteIndex = index;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
}

// Confirm and delete the chemical
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteIndex !== -1) {
        deleteChemical(deleteIndex);
        deleteIndex = -1;
    }
});

// Add Chemical and validate
addChemicalBtn.addEventListener('click', function() {
    if (addChemicalBtn.disabled) return;

    const isChemicalNameValid = /^[a-zA-Z0-9\s%,-.]+$/.test(chemicalNameInput.value) && /[a-zA-Z]/.test(chemicalNameInput.value);
    if (!isChemicalNameValid) {
        showInvalidInputModal();
        return;
    }

    const chemicalExists = chemicals.some(c => 
        c.chemicalName.toLowerCase() === chemicalNameInput.value.toLowerCase() &&
        c.casNumber === casNumberInput.value
    );
    
    if (chemicalExists) {
        alert('This chemical already exists with the same CAS number!');
        return;
    }

    chemicals.push({
        chemicalName: chemicalNameInput.value,
        casNumber: casNumberInput.value
    });

    alert('Chemical added successfully!');
    chemicalNameInput.value = '';
    casNumberInput.value = '';
    addChemicalBtn.disabled = true;

    downloadJSON(chemicals);
    populateSuggestions();
});

// Render chemicals table based on search
function renderTable(filteredChemicals = chemicals) {
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = '';

    filteredChemicals.forEach((chemical, index) => {
        const row = `<tr>
                        <td>${chemical.chemicalName}</td>
                        <td>${chemical.casNumber}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editChemical(${index})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="showDeleteConfirmation(${index})">Delete</button>
                        </td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
}

// Edit chemical
function editChemical(index) {
    editingIndex = index;
    const chemical = searchResults.length > 0 ? searchResults[index] : chemicals[index];

    editChemicalNameInput.value = chemical.chemicalName;
    editCasNumberInput.value = chemical.casNumber;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

// Save the edited chemical data
function saveEdit() {
    const updatedChemicalName = editChemicalNameInput.value.trim();
    const updatedCasNumber = editCasNumberInput.value.trim();

    // Validate updated CAS Number format
    if (!/^\d{2,6}-\d{2}-\d{1}$/.test(updatedCasNumber)) {
        alert("CAS Number must follow the 'XXXXXX-XX-X' format.");
        return;
    }

    // Validate updated chemical name and CAS number fields
    if (!updatedChemicalName || !updatedCasNumber) {
        showInvalidInputModal();
        return;
    }

    const chemicalList = searchResults.length > 0 ? searchResults : chemicals;
    chemicalList[editingIndex] = { chemicalName: updatedChemicalName, casNumber: updatedCasNumber };
    renderTable(chemicalList);

    const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
    editModal.hide();
}

// Delete chemical
function deleteChemical(index) {
    if (searchResults.length > 0) {
        const chemicalName = searchResults[index].chemicalName;
        chemicals = chemicals.filter(chem => chem.chemicalName.toLowerCase() !== chemicalName.toLowerCase());
        searchResults.splice(index, 1);
        renderTable(searchResults);
    } else {
        chemicals.splice(index, 1);
        renderTable(chemicals);
    }
    downloadJSON(chemicals);
}

// Automatically download the JSON file
function downloadJSON(chemicals) {
    const jsonStr = JSON.stringify(chemicals, null, 2);
    const blob = new Blob([jsonStr], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'chemicals.json';
    link.click();
    URL.revokeObjectURL(url);
}

// Show the table only when there are search results
searchChemicalBtn.addEventListener('click', function() {
    const searchValue = searchChemicalInput.value.trim().toLowerCase();

    if (searchValue === '') {
        return;
    }

    searchResults = chemicals.filter(c => c.chemicalName.toLowerCase().includes(searchValue));

    if (searchResults.length === 0) {
        const noResultsModal = new bootstrap.Modal(document.getElementById('noResultsModal'));
        noResultsModal.show();
    } else {
        renderTable(searchResults);
        tableContainer.style.display = 'block';
    }
});

// Load chemicals from the JSON file when the page loads
function loadChemicalsFromJSON() {
    fetch('/json/chemicals.json')
        .then(response => response.json())
        .then(data => {
            chemicals = data;
            populateSuggestions();
        })
        .catch(error => console.error('Error loading chemicals:', error));
}

// Function to populate datalist with autocomplete suggestions
function populateSuggestions() {
    const datalist = document.getElementById('chemicalSuggestions');
    datalist.innerHTML = '';

    chemicals.forEach(chemical => {
        const option = document.createElement('option');
        option.value = chemical.chemicalName;
        datalist.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', loadChemicalsFromJSON);

</script>
@endsection


