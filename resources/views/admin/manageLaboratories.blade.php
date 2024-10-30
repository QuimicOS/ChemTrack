@extends('admin.templateAdmin')

@section('title', 'Manage Laboratories')

@section('content')
<style>
    .content-area {
        margin-left: 120px; /* Aligns with sidebar width */
        padding: 1.25rem;
        margin-top: 25px; /* Consistent top margin */
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
        <h1 class="display-5">Manage Laboratories</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section for Adding Laboratory -->
    <form id="laboratoryForm" novalidate>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Enter department" required>
                <div class="invalid-feedback">Department must contain only letters.</div>
            </div>
            <div class="col-md-6">
                <label for="building" class="form-label">Building</label>
                <input type="text" class="form-control" id="building" placeholder="Enter building" required>
                <div class="invalid-feedback">Building must contain only letters.</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="roomNumber" class="form-label">Room Number</label>
                <input type="text" class="form-control" id="roomNumber" placeholder="Enter room number (e.g., S-122)" required>
                <div class="invalid-feedback">Room Number must be in format: Letter(s)-Number(s) (e.g., S-122).</div>
            </div>
            <div class="col-md-6">
                <label for="labName" class="form-label">Laboratory Name</label>
                <input type="text" class="form-control" id="labName" placeholder="Enter laboratory name" required>
                <div class="invalid-feedback">Laboratory Name must contain only letters.</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="professorInvestigator" class="form-label">Professor Investigator</label>
                <input type="text" class="form-control" id="professorInvestigator" placeholder="Enter professor name" required>
                <div class="invalid-feedback">Professor name must contain only letters.</div>
            </div>
            <div class="col-md-6">
                <label for="supervisor" class="form-label">Supervisor</label>
                <input type="text" class="form-control" id="supervisor" placeholder="Enter supervisor name" required>
                <div class="invalid-feedback">Supervisor name must contain only letters.</div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="button" class="btn btn-primary" onclick="validateAndAddLab()">Add Laboratory</button>
        </div>
    </form>

    <!-- Search Section for Room Number -->
    <div class="row my-5">
        <div class="col-md-10">
            <label for="searchRoom" class="form-label">Search Room by Number</label>
            <input type="text" class="form-control" id="searchRoom" placeholder="Search room number">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-secondary w-100" onclick="searchRoom()">Search</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save changes</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>
// Dummy array to store laboratory data
let laboratories = [];
let editingIndex = -1;
let deletingIndex = -1;

// Function to validate and add a new laboratory
function validateAndAddLab() {
    const departmentField = document.getElementById('department');
    const buildingField = document.getElementById('building');
    const roomNumberField = document.getElementById('roomNumber');
    const labNameField = document.getElementById('labName');
    const professorField = document.getElementById('professorInvestigator');
    const supervisorField = document.getElementById('supervisor');
    let isValid = true;

    const namePattern = /^[a-zA-Z\s]+$/;
    const roomPattern = /^[a-zA-Z]+-[0-9]{3}$/;

    // Validate fields
    if (!namePattern.test(departmentField.value.trim())) {
        departmentField.classList.add('is-invalid');
        isValid = false;
    } else {
        departmentField.classList.remove('is-invalid');
    }

    if (!namePattern.test(buildingField.value.trim())) {
        buildingField.classList.add('is-invalid');
        isValid = false;
    } else {
        buildingField.classList.remove('is-invalid');
    }

    if (!roomPattern.test(roomNumberField.value.trim())) {
        roomNumberField.classList.add('is-invalid');
        isValid = false;
    } else {
        roomNumberField.classList.remove('is-invalid');
    }

    if (!namePattern.test(labNameField.value.trim())) {
        labNameField.classList.add('is-invalid');
        isValid = false;
    } else {
        labNameField.classList.remove('is-invalid');
    }

    if (!namePattern.test(professorField.value.trim())) {
        professorField.classList.add('is-invalid');
        isValid = false;
    } else {
        professorField.classList.remove('is-invalid');
    }

    if (!namePattern.test(supervisorField.value.trim())) {
        supervisorField.classList.add('is-invalid');
        isValid = false;
    } else {
        supervisorField.classList.remove('is-invalid');
    }

    if (isValid) {
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
}

// Function to clear form fields
function clearForm() {
    document.getElementById('laboratoryForm').reset();
}

// Function to render the table with laboratory data
function renderTable(filteredLabs = laboratories) {
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
                            <button class="btn btn-sm btn-warning" onclick="editLab(${index})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteLab(${index})">Delete</button>
                        </td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
}

// Search function
function searchRoom() {
    const searchValue = document.getElementById('searchRoom').value.trim().toLowerCase();
    const filteredLabs = laboratories.filter(lab => lab.roomNumber.toLowerCase().includes(searchValue));
    renderTable(filteredLabs);
}

// Function to edit laboratory details
function editLab(index) {
    editingIndex = index;
    const lab = laboratories[index];

    // Fill the modal with current lab details
    document.getElementById('editDepartment').value = lab.department;
    document.getElementById('editBuilding').value = lab.building;
    document.getElementById('editRoomNumber').value = lab.roomNumber;
    document.getElementById('editLabName').value = lab.labName;
    document.getElementById('editProfessor').value = lab.professor;
    document.getElementById('editSupervisor').value = lab.supervisor;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

// Save the edited laboratory details
function saveEdit() {
    const updatedLab = {
        department: document.getElementById('editDepartment').value.trim(),
        building: document.getElementById('editBuilding').value.trim(),
        roomNumber: document.getElementById('editRoomNumber').value.trim(),
        labName: document.getElementById('editLabName').value.trim(),
        professor: document.getElementById('editProfessor').value.trim(),
        supervisor: document.getElementById('editSupervisor').value.trim(),
    };

    laboratories[editingIndex] = updatedLab;
    alert('Laboratory updated successfully!');
    renderTable();
    const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
    editModal.hide();
}

// Delete a laboratory
function deleteLab(index) {
    deletingIndex = index;
    const lab = laboratories[index];
    document.getElementById('deleteLabDetails').innerText = `Department: ${lab.department}, Building: ${lab.building}, Room: ${lab.roomNumber}, Lab: ${lab.labName}, Professor: ${lab.professor}, Supervisor: ${lab.supervisor}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Confirm delete
function confirmDelete() {
    laboratories.splice(deletingIndex, 1);
    alert('Laboratory deleted successfully!');
    renderTable();
    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    deleteModal.hide();
}

</script>
@endsection
