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
        <h1 class="display-5">Manage Chemicals</h1>
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

    <!-- Search Section for Chemicals with Autocomplete -->
    <div class="mb-5">
        <form class="row">
            <div class="col-md-10">
                <label for="searchChemical" class="form-label">Search Chemical by Name</label>
                <input type="text" class="form-control" id="searchChemical" placeholder="Search by Chemical Name" list="chemicalSuggestions">
                <datalist id="chemicalSuggestions"></datalist>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-secondary w-100" id="searchChemicalBtn">Search</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Dummy array to store chemicals
let chemicals = [];
let editingIndex = -1;
let searchResults = []; // Separate array for search results

// Add Chemical and validate
document.getElementById('addChemicalBtn').addEventListener('click', function(event) {
    const chemicalName = document.getElementById('chemicalName');
    const casNumber = document.getElementById('casNumber');

    // Validate chemical name (alphanumeric and special characters)
    const namePattern = /^[a-zA-Z0-9\s%,-\.]+$/;
    if (!namePattern.test(chemicalName.value)) {
        chemicalName.classList.add('is-invalid');
        return;
    } else {
        chemicalName.classList.remove('is-invalid');
    }

    // Validate CAS number format (XXXXXX-XX-X)
    const casPattern = /^\d{2,6}-\d{2}-\d{1}$/;
    if (!casPattern.test(casNumber.value)) {
        casNumber.classList.add('is-invalid');
        return;
    } else {
        casNumber.classList.remove('is-invalid');
    }

    // Check for duplicates (case-insensitive)
    const chemicalExists = chemicals.some(c => c.chemicalName.toLowerCase() === chemicalName.value.toLowerCase());
    if (chemicalExists) {
        alert('Chemical already exists!');
        return;
    }

    // Add chemical to the array
    chemicals.push({
        chemicalName: chemicalName.value,
        casNumber: casNumber.value
    });

    // Alert success and clear the fields
    alert('Chemical added successfully!');
    chemicalName.value = '';
    casNumber.value = '';

    // Update the JSON file but do not show in the table
    downloadJSON(chemicals);
    populateSuggestions();  // Update the suggestions list
});

// Render chemicals table based on search
function renderTable(filteredChemicals = chemicals) {
    const tableBody = document.getElementById('chemicalTableBody');
    tableBody.innerHTML = ''; // Clear previous rows

    filteredChemicals.forEach((chemical, index) => {
        const row = `<tr>
                        <td>${chemical.chemicalName}</td>
                        <td>${chemical.casNumber}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editChemical(${index})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteChemical(${index})">Delete</button>
                        </td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
}

// Edit chemical
function editChemical(index) {
    editingIndex = index;
    const chemical = searchResults.length > 0 ? searchResults[index] : chemicals[index];
    document.getElementById('editChemicalName').value = chemical.chemicalName;
    document.getElementById('editCasNumber').value = chemical.casNumber;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

// Save edited chemical
function saveEdit() {
    const updatedChemicalName = document.getElementById('editChemicalName').value;
    const updatedCasNumber = document.getElementById('editCasNumber').value;

    // Validate chemical name and CAS number (same logic as before)
    const namePattern = /^[a-zA-Z0-9\s%,-\.]+$/;
    const casPattern = /^\d{2,6}-\d{2}-\d{1}$/;

    if (!namePattern.test(updatedChemicalName) || !casPattern.test(updatedCasNumber)) {
        alert('Invalid input!');
        return;
    }

    // Update the search result or the original array
    if (searchResults.length > 0) {
        searchResults[editingIndex].chemicalName = updatedChemicalName;
        searchResults[editingIndex].casNumber = updatedCasNumber;
        renderTable(searchResults);
    } else {
        chemicals[editingIndex].chemicalName = updatedChemicalName;
        chemicals[editingIndex].casNumber = updatedCasNumber;
        renderTable(chemicals);
    }

    downloadJSON(chemicals); // Update the JSON file

    const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
    editModal.hide();
}

// Delete chemical
function deleteChemical(index) {
    if (searchResults.length > 0) {
        const chemicalName = searchResults[index].chemicalName;
        chemicals = chemicals.filter(chem => chem.chemicalName.toLowerCase() !== chemicalName.toLowerCase());
        searchResults.splice(index, 1);
        renderTable(searchResults); // Re-render only search results
    } else {
        chemicals.splice(index, 1);
        renderTable(chemicals); // Re-render the whole list
    }
    downloadJSON(chemicals); // Update the JSON file
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

// Search and filter chemicals from the JSON file
document.getElementById('searchChemicalBtn').addEventListener('click', function() {
    const searchValue = document.getElementById('searchChemical').value.trim().toLowerCase();
    searchResults = chemicals.filter(c => c.chemicalName.toLowerCase().includes(searchValue));
    renderTable(searchResults);
});

// Load chemicals from the JSON file when the page loads
function loadChemicalsFromJSON() {
    fetch('/json/chemicals.json')
        .then(response => response.json())
        .then(data => {
            chemicals = data;
            populateSuggestions(); // Populate suggestions after loading chemicals
        })
        .catch(error => console.error('Error loading chemicals:', error));
}

// Function to populate datalist with autocomplete suggestions
function populateSuggestions() {
    const datalist = document.getElementById('chemicalSuggestions');
    datalist.innerHTML = ''; // Clear previous suggestions

    chemicals.forEach(chemical => {
        const option = document.createElement('option');
        option.value = chemical.chemicalName;
        datalist.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', loadChemicalsFromJSON);

</script>
@endsection
