@extends('admin.templateAdmin')

@section('title', 'Manage Laboratories')

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
                    <label for="supervisor" class="form-label">Supervisor</label>
                    <input type="text" class="form-control" id="supervisor" placeholder="Enter supervisor name" required>
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
                        <th>Building</th>
                        <th>Room Number</th>
                        <th>Laboratory Name</th>
                        <th>Professor Investigator</th>
                        <th>Supervisor</th>
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
                    <label for="editSupervisor" class="form-label">Supervisor</label>
                    <input type="text" class="form-control" id="editSupervisor" required>
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

// Dummy data for initial table view
let laboratories = [
    { department: "Chemistry", building: "Science", roomNumber: "S-122", labName: "Organic Chemistry Lab", professor: "Dr. Jane Doe", supervisor: "John Smith" },
    { department: "Physics", building: "Main", roomNumber: "PH-101", labName: "Quantum Physics Lab", professor: "Dr. Albert Einstein", supervisor: "Marie Curie" }
];

let editingIndex = -1;
let deletingIndex = -1;

// Regular expressions for validation
const textPattern = /^[a-zA-Z\s]+$/; // Only letters and spaces
const roomPattern = /^.*$/; // Any character, 3-5 characters long
const nameWithSpecialCharsPattern = /^[a-zA-Z\s.,'-]+$/; // Allows letters, spaces, and some special characters for names


// Validation for the Add form
const departmentField = document.getElementById('department');
const buildingField = document.getElementById('building');
const roomNumberField = document.getElementById('roomNumber');
const labNameField = document.getElementById('labName');
const professorField = document.getElementById('professorInvestigator');
const supervisorField = document.getElementById('supervisor');
const addLabButton = document.querySelector('button[onclick="validateAndAddLab()"]');

// Validation for the Edit Modal
const editDepartmentField = document.getElementById('editDepartment');
const editBuildingField = document.getElementById('editBuilding');
const editRoomNumberField = document.getElementById('editRoomNumber');
const editLabNameField = document.getElementById('editLabName');
const editProfessorField = document.getElementById('editProfessor');
const editSupervisorField = document.getElementById('editSupervisor');
const saveEditButton = document.querySelector('button[onclick="saveEdit()"]');

// Search validation
const searchRoomField = document.getElementById('searchRoom');
const searchButton = document.querySelector('button[onclick="searchRoom()"]');

// Function to restrict input to valid characters based on a pattern
function enforceInputRestrictions(field, pattern) {
    field.addEventListener('keypress', (e) => {
        const char = String.fromCharCode(e.which);
        if (!pattern.test(char)) {
            e.preventDefault();
        }
    });
}

// Enforce restrictions on input fields
enforceInputRestrictions(departmentField, textPattern);
enforceInputRestrictions(buildingField, textPattern);
enforceInputRestrictions(roomNumberField, /./); // Any character allowed
enforceInputRestrictions(labNameField, /./);
enforceInputRestrictions(professorField, textPattern);
enforceInputRestrictions(supervisorField, textPattern);

// Apply same restrictions to Edit Modal fields
enforceInputRestrictions(editDepartmentField, textPattern);
enforceInputRestrictions(editBuildingField, textPattern);
enforceInputRestrictions(editRoomNumberField, /./);
enforceInputRestrictions(editLabNameField, /./);
enforceInputRestrictions(editProfessorField, textPattern);
enforceInputRestrictions(editSupervisorField, textPattern);

// Function to validate a single input field with a regex pattern
function validateField(field, pattern) {
    const isValid = pattern.test(field.value.trim());
    return isValid;
}

// Function to validate the Add Lab form
function validateAddLabForm() {
    const isDepartmentValid = validateField(departmentField, textPattern);
    const isBuildingValid = validateField(buildingField, textPattern);
    const isRoomValid = validateField(roomNumberField, roomPattern);
    const isLabNameValid = labNameField.value.trim() !== '';
    const isProfessorValid = validateField(professorField, nameWithSpecialCharsPattern);
    const isSupervisorValid = validateField(supervisorField, nameWithSpecialCharsPattern);

    addLabButton.disabled = !(isDepartmentValid && isBuildingValid && isRoomValid && isLabNameValid && isProfessorValid && isSupervisorValid);
}

// Function to validate the Search Room field
function validateSearchField() {
    const isSearchValid = validateField(searchRoomField, roomPattern);
    searchButton.disabled = !isSearchValid;
}

// Function to validate all fields in the Edit Modal
function validateEditLabForm() {
    const isEditDepartmentValid = validateField(editDepartmentField, textPattern);
    const isEditBuildingValid = validateField(editBuildingField, textPattern);
    const isEditRoomValid = validateField(editRoomNumberField, roomPattern);
    const isEditLabNameValid = editLabNameField.value.trim() !== '';
    const isEditProfessorValid = validateField(editProfessorField, nameWithSpecialCharsPattern);
    const isEditSupervisorValid = validateField(editSupervisorField, nameWithSpecialCharsPattern);

    saveEditButton.disabled = !(isEditDepartmentValid && isEditBuildingValid && isEditRoomValid && isEditLabNameValid && isEditProfessorValid && isEditSupervisorValid);
}


// Attach validation to Add Lab form fields
departmentField.addEventListener('input', validateAddLabForm);
buildingField.addEventListener('input', validateAddLabForm);
roomNumberField.addEventListener('input', validateAddLabForm);
labNameField.addEventListener('input', validateAddLabForm);
professorField.addEventListener('input', validateAddLabForm);
supervisorField.addEventListener('input', validateAddLabForm);

// Attach validation to Edit Modal fields
editDepartmentField.addEventListener('input', validateEditLabForm);
editBuildingField.addEventListener('input', validateEditLabForm);
editRoomNumberField.addEventListener('input', validateEditLabForm);
editLabNameField.addEventListener('input', validateEditLabForm);
editProfessorField.addEventListener('input', validateEditLabForm);
editSupervisorField.addEventListener('input', validateEditLabForm);

// Attach validation to the Search Room field
searchRoomField.addEventListener('input', validateSearchField);
// Function to add laboratory if valid
function validateAndAddLab() {
    if (addLabButton.disabled) return;

    const lab = {
        department: departmentField.value.trim(),
        building: buildingField.value.trim(),
        roomNumber: roomNumberField.value.trim(),
        labName: labNameField.value.trim(),
        professor: professorField.value.trim(),
        supervisor: supervisorField.value.trim(),
    };

    laboratories.push(lab);
    alert('Laboratory added successfully!');
    clearForm();
}

// Function to clear form fields
function clearForm() {
    document.getElementById('laboratoryForm').reset();
    addLabButton.disabled = true;
}

// Function to search for rooms by number
function searchRoom() {
    const searchValue = searchRoomField.value.trim().toLowerCase();
    const filteredLabs = laboratories.filter(lab => lab.roomNumber.toLowerCase() === searchValue);

    renderTable(filteredLabs);

    if (filteredLabs.length === 0) {
        alert('No laboratories found for the given room number.');
    } else {
        document.querySelector('.table-container').style.display = 'block';
    }
}

// Function to render table with search results
function renderTable(filteredLabs) {
    const tableBody = document.getElementById('laboratoryTableBody');
    tableBody.innerHTML = '';

    filteredLabs.forEach((lab, index) => {
        const row = `<tr>
                        <td>${lab.department}</td>
                        <td>${lab.building}</td>
                        <td>${lab.roomNumber}</td>
                        <td>${lab.labName}</td>
                        <td>${lab.professor}</td>
                        <td>${lab.supervisor}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editLab('${lab.roomNumber}')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteLab('${lab.roomNumber}')">Delete</button>
                        </td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
}

// Function to edit a lab in the Edit Modal by room number
function editLab(roomNumber) {
    editingIndex = laboratories.findIndex(lab => lab.roomNumber === roomNumber);

    if (editingIndex === -1) {
        alert('Error: Laboratory not found for editing.');
        return;
    }

    const lab = laboratories[editingIndex];
    editDepartmentField.value = lab.department;
    editBuildingField.value = lab.building;
    editRoomNumberField.value = lab.roomNumber;
    editLabNameField.value = lab.labName;
    editProfessorField.value = lab.professor;
    editSupervisorField.value = lab.supervisor;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
    validateEditLabForm();
}

// Save the edited laboratory details
function saveEdit() {
    if (editingIndex === -1) return;

    laboratories[editingIndex] = {
        department: editDepartmentField.value.trim(),
        building: editBuildingField.value.trim(),
        roomNumber: editRoomNumberField.value.trim(),
        labName: editLabNameField.value.trim(),
        professor: editProfessorField.value.trim(),
        supervisor: editSupervisorField.value.trim(),
    };

    alert('Laboratory updated successfully!');
    searchRoom();
    const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
    editModal.hide();
}

// Function to delete a lab by room number
function deleteLab(roomNumber) {
    deletingIndex = laboratories.findIndex(lab => lab.roomNumber === roomNumber);

    if (deletingIndex === -1) {
        alert('Error: Laboratory not found for deletion.');
        return;
    }

    const lab = laboratories[deletingIndex];
    document.getElementById('deleteLabDetails').innerText = `Department: ${lab.department}, Building: ${lab.building}, Room: ${lab.roomNumber}, Lab: ${lab.labName}, Professor: ${lab.professor}, Supervisor: ${lab.supervisor}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Confirm delete
function confirmDelete() {
    laboratories.splice(deletingIndex, 1);
    alert('Laboratory deleted successfully!');
    searchRoom();
    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    deleteModal.hide();
}

</script>
@endsection
