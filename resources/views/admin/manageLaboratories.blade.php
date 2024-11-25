@extends('admin.templateAdmin')

@section('title', 'Manage Laboratories')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<style>
    .content-area {
        margin-left: 120px;
        padding: 1.25rem;
        margin-top: 25px;
    }
    .table-container {
        margin-top: 20px;
        display: none; /* Hide table initially */
    }
    .form-label {
        font-weight: bold;
    }
    .btn-primary, .btn-secondary {
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
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Manage Laboratories</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section for Adding Laboratory -->
    <fieldset>
        <legend>Add Laboratory</legend>
        <form id="laboratoryForm" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" placeholder="Enter department" required>
                </div>
                <div class="col-md-6">
                    <label for="building" class="form-label">Building</label>
                    <input type="text" class="form-control" id="building" placeholder="Enter building" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="roomNumber" class="form-label">Room Number</label>
                    <input type="text" class="form-control" id="roomNumber" placeholder="Enter room number (3-5 characters)" required>
                </div>
                <div class="col-md-6">
                    <label for="labName" class="form-label">Laboratory Name</label>
                    <input type="text" class="form-control" id="labName" placeholder="Enter laboratory name" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="professorInvestigator" class="form-label">Professor Investigator</label>
                    <input type="text" class="form-control" id="professorInvestigator" placeholder="Enter professor name" required>
                </div>
                <div class="col-md-6">
                    <label for="departmentDirector" class="form-label">Department Director</label>
                    <input type="text" class="form-control" id="departmentDirector" placeholder="Enter department director name" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-end">
                <button type="button" class="btn btn-success fw-bold" onclick="validateAndAddLab()" disabled>Add Laboratory</button>
            </div>
        </form>
    </fieldset>

    <!-- Search Section for Room Number -->
    <fieldset>
        <legend>Search Laboratory</legend>
        <div class="row my-5">
            <div class="col-md-10">
                <label for="searchRoom" class="form-label">Search Room by Number</label>
                <input type="text" class="form-control" id="searchRoom" placeholder="Search room number (3-5 characters)">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="searchRoom()" disabled>Search</button>
            </div>
        </div>

        <!-- Table Section (Laboratories) -->
        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Department</th>
                        <th>Building Name</th>
                        <th>Room Number</th>
                        <th>Laboratory Name</th>
                        <th>Professor Investigator</th>
                        <th>Department Director</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="laboratoryTableBody">
                    <!-- Rows will be inserted dynamically -->
                </tbody>
            </table>
        </div>
    </fieldset>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Laboratory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editDepartment" class="form-label">Department</label>
                    <input type="text" class="form-control" id="editDepartment" required>
                </div>
                <div class="mb-3">
                    <label for="editBuilding" class="form-label">Building</label>
                    <input type="text" class="form-control" id="editBuilding" required>
                </div>
                <div class="mb-3">
                    <label for="editRoomNumber" class="form-label">Room Number</label>
                    <input type="text" class="form-control" id="editRoomNumber" required>
                </div>
                <div class="mb-3">
                    <label for="editLabName" class="form-label">Laboratory Name</label>
                    <input type="text" class="form-control" id="editLabName" required>
                </div>
                <div class="mb-3">
                    <label for="editProfessor" class="form-label">Professor Investigator</label>
                    <input type="text" class="form-control" id="editProfessor" required>
                </div>
                <div class="mb-3">
                    <label for="editDepartmentDirector" class="form-label">Department Director</label>
                    <input type="text" class="form-control" id="editDepartmentDirector" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveEdit()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Laboratory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this laboratory?
                <p id="deleteLabDetails"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Define DOM elements and CSRF token
const searchRoomField = document.getElementById('searchRoom');
const searchButton = document.querySelector('button[onclick="searchRoom()"]');
const addLabButton = document.querySelector('button[onclick="validateAndAddLab()"]');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const departmentField = document.getElementById('department');
const buildingField = document.getElementById('building');
const roomNumberField = document.getElementById('roomNumber');
const labNameField = document.getElementById('labName');
const professorField = document.getElementById('professorInvestigator');
const directorField = document.getElementById('departmentDirector');

const textPattern = /^[a-zA-Z\s]+$/;
const nameWithSpecialCharsPattern = /^[a-zA-Z\s.,'-]+$/;

// Functions

// Validate Add Lab form
function validateAddLabForm() {
    const isDepartmentValid = textPattern.test(departmentField.value.trim());
    const isBuildingValid = textPattern.test(buildingField.value.trim());
    const isRoomValid = roomNumberField.value.trim() !== '';
    const isLabNameValid = labNameField.value.trim() !== '';
    const isProfessorValid = nameWithSpecialCharsPattern.test(professorField.value.trim());
    const isDepartmentDirectorValid = nameWithSpecialCharsPattern.test(directorField.value.trim());

    addLabButton.disabled = !(isDepartmentValid && isBuildingValid && isRoomValid && isLabNameValid && isProfessorValid && isDepartmentDirectorValid);
}

// Enable or disable the search button
function validateSearchField() {
    searchButton.disabled = searchRoomField.value.trim() === '';
}

// Clear the Add Lab form
function clearForm() {
    document.getElementById('laboratoryForm').reset();
    addLabButton.disabled = true;
}

// Render laboratories in the table
function renderTable(laboratories) {
    const tableBody = document.getElementById('laboratoryTableBody');
    tableBody.innerHTML = ''; // Clear previous results

    laboratories.forEach(lab => {
        const row = `
            <tr>
                <td>${lab.department}</td>
                <td>${lab.building_name}</td>
                <td>${lab.room_number}</td>
                <td>${lab.lab_name}</td>
                <td>${lab.professor_investigator}</td>
                <td>${lab.department_director}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editLab(${lab.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLab(${lab.id})">Delete</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
    document.querySelector('.table-container').style.display = 'block';
}

function searchRoom() {
    const searchValue = searchRoomField.value.trim();
    if (!searchValue) return alert('Please enter a room number to search.');

    fetch(`/labs/room?room_number=${encodeURIComponent(searchValue)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then((response) => {
            if (!response.ok) {
                // Gracefully handle 404 errors
                document.getElementById('laboratoryTableBody').innerHTML = `
                    <tr><td colspan="7" class="text-center">No laboratories found</td></tr>`;
                document.querySelector('.table-container').style.display = 'block';
                throw new Error('Lab not found');
            }
            return response.json();
        })
        .then((data) => {
            renderTable(data); // Render the results if successful
        })
        .catch((error) => {
            console.error('Error fetching labs:', error);
        });
}

function editLab(id) {
    fetch(`/labs/${id}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Failed to fetch lab with ID ${id}`);
            }
            return response.json();
        })
        .then((data) => {
            // Populate modal fields
            document.getElementById('editDepartment').value = data.department || '';
            document.getElementById('editBuilding').value = data.building_name || '';
            document.getElementById('editRoomNumber').value = data.room_number || '';
            document.getElementById('editLabName').value = data.lab_name || '';
            document.getElementById('editProfessor').value = data.professor_investigator || '';
            document.getElementById('editDepartmentDirector').value = data.department_director || '';

            // Set lab ID in modal
            document.getElementById('editModal').setAttribute('data-lab-id', id);

            // Show modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        })
        .catch((error) => {
            console.error('Error fetching laboratory details:', error);
            alert('Failed to load laboratory details. Please try again.');
        });
}

function saveEdit() {
    const labId = document.getElementById('editModal').getAttribute('data-lab-id');

    if (!labId) {
        alert('Invalid laboratory ID. Unable to save changes.');
        return;
    }

    const updatedLabData = {
        department: document.getElementById('editDepartment').value.trim(),
        building_name: document.getElementById('editBuilding').value.trim(),
        room_number: document.getElementById('editRoomNumber').value.trim(),
        lab_name: document.getElementById('editLabName').value.trim(),
        professor_investigator: document.getElementById('editProfessor').value.trim(),
        department_director: document.getElementById('editDepartmentDirector').value.trim(),
    };

    fetch(`/editLabs/${labId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(updatedLabData),
    })
        .then((response) => {
            if (!response.ok) {
                return response.json().then((data) => {
                    console.error('Validation errors:', data.errors || 'Unknown error');
                    throw new Error('Failed to save laboratory changes');
                });
            }
            return response.json();
        })
        .then(() => {
            alert('Laboratory updated successfully!');
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            editModal.hide(); // Close the modal
            searchRoom(); // Optionally refresh the table
        })
        .catch((error) => {
            console.error('Error saving laboratory changes:', error);
            alert('Failed to save changes. Please check the inputs and try again.');
        });
}



function deleteLab(id) {
    if (!confirm("Are you sure you want to delete this laboratory?")) {
        return;
    }

    fetch(`/invalidateLabs/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                console.error('Error deleting laboratory:', data.errors);
                throw new Error('Failed to delete laboratory');
            });
        }
        return response.json();
    })
    .then(data => {
        alert('Laboratory deleted successfully!');
        // Clear the search input and table after deletion
        searchRoomField.value = '';
        document.getElementById('laboratoryTableBody').innerHTML = '';
        document.querySelector('.table-container').style.display = 'none';
    })
    .catch(error => {
        console.error('Error deleting laboratory:', error);
        alert('Failed to delete laboratory.');
    });
}




// Add a new laboratory
function validateAndAddLab() {
    const labData = {
        department: departmentField.value.trim(),
        building_name: buildingField.value.trim(),
        room_number: roomNumberField.value.trim(),
        lab_name: labNameField.value.trim(),
        professor_investigator: professorField.value.trim(),
        department_director: directorField.value.trim(),
    };

    // Directly submit the new laboratory data to the backend
    fetch('/labs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(labData),
    })
        .then((response) => {
            if (!response.ok) {
                return response.json().then((data) => {
                    console.error('Validation errors:', data.errors || 'Unknown error');
                    throw new Error('Failed to add laboratory');
                });
            }
            return response.json();
        })
        .then(() => {
            alert('Laboratory added successfully!');
            clearForm(); // Clear the form fields
        })
        .catch((error) => {
            console.error('Error adding laboratory:', error);
            alert('Failed to add laboratory. Please check validation errors.');
        });
}




// Event listeners
searchRoomField.addEventListener('input', validateSearchField);
departmentField.addEventListener('input', validateAddLabForm);
buildingField.addEventListener('input', validateAddLabForm);
roomNumberField.addEventListener('input', validateAddLabForm);
labNameField.addEventListener('input', validateAddLabForm);
professorField.addEventListener('input', validateAddLabForm);
directorField.addEventListener('input', validateAddLabForm);

// Initialize the search button as disabled
validateSearchField();

</script>
@endsection
