@extends('admin.templateAdmin')

@section('title', 'Role Management')

@section('content')
<style>
    .content-area {
        margin-left: 115px;
        padding: 1.25rem;
        margin-top: 25px;
    }

    .table-container {
        margin-top: 20px;
    }

    .filter-container {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
    }
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Role Management</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Add User) -->
    <form id="roleForm">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" placeholder="Enter First Name">
                <small class="text-danger" id="firstNameError"></small>
            </div>
            <div class="col-md-4">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter Last Name">
                <small class="text-danger" id="lastNameError"></small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Enter Email">
                <small class="text-danger" id="emailError"></small>
            </div>
            <div class="col-md-4">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Enter Department">
                <small class="text-danger" id="departmentError"></small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="laboratory" class="form-label">Room Number</label>
                <input type="text" class="form-control" id="laboratory" placeholder="Enter Lab (e.g., S-122)">
                <small class="text-danger" id="laboratoryError"></small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role">
                    <option value="" disabled selected>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Professor">Professor</option>
                    <option value="TA">Teaching Assistant/Lab Technician/Student</option>
                </select>
                <small class="text-danger" id="roleError"></small>
            </div>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-success" onclick="validateForm()">Add</button>
        </div>
    </form>

    <!-- Submitted Requests Table -->
    <div class="table-container mt-5">
        <h3>Submitted Requests</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Laboratories</th>
                    <th>Requested Role</th>
                    <th>Submission Date</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody id="submittedRequestsTableBody">
                <!-- Submitted requests data will populate here -->
            </tbody>
        </table>
    </div>

    <!-- Search Section for Editing Users -->
    <div class="row mb-3 mt-5">
        <div class="col-md-8">
            <label for="searchUsername" class="form-label">Search Username</label>
            <input type="text" class="form-control" id="searchUsername" placeholder="Enter username (e.g., name.lastname)">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-secondary w-100" onclick="searchUsername()">Search</button>
        </div>
    </div>

    <!-- Edit Users Table -->
    <div class="table-container mt-3">
        <h3>Edit Users</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Laboratories</th>
                    <th>Role</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody id="searchResultsTableBody">
                <!-- Edit Users search results will populate here -->
            </tbody>
        </table>
    </div>

    <!-- Show Certified Users Button -->
    <div class="text-center mt-5">
        <button type="button" class="btn btn-info" onclick="toggleCertifiedUsers()">Show Certified Users</button>
    </div>

    <!-- Certified Users Table (Initially Hidden) -->
    <div class="table-container mt-3" id="certifiedUsersContainer" style="display: none;">
        <h3>Certified Users</h3>
        <table class="table table-bordered table-hover" id="certifiedUsersTable">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Last Name</th>
                    <th>Completion Date</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody id="certifiedUsersTableBody">
                <!-- Certified users data will populate here -->
            </tbody>
        </table>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-primary" onclick="generatePDF()">Generate PDF</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
let users = [
    { username: 'john.doe@upr.edu', laboratories: 'S-122', role: 'Admin', date: '2024-10-18' },
    { username: 'alice.smith@upr.edu', laboratories: 'CH-001', role: 'TA', date: '2024-10-17' }
];

let submittedRequests = [
    { username: 'paul.green@upr.edu', laboratories: 'S-101', role: 'Professor', date: '2024-10-19' },
    { username: 'susan.blue@upr.edu', laboratories: 'PH-220', role: 'TA', date: '2024-10-18' }
];

// Dummy certified users data
let certifiedUsers = [
    { firstName: 'Emma', lastName: 'Johnson', completionDate: '2024-09-30', department: 'Chemistry' },
    { firstName: 'Liam', lastName: 'Brown', completionDate: '2024-10-05', department: 'Biology' },
    { firstName: 'Olivia', lastName: 'Wilson', completionDate: '2024-10-10', department: 'Physics' }
];

let editIndex = -1;

// Function to validate and add new user
function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.getElementById('firstNameError').textContent = '';
    document.getElementById('lastNameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('departmentError').textContent = '';
    document.getElementById('laboratoryError').textContent = '';
    document.getElementById('roleError').textContent = '';

    // Validate Name and Last Name: Characters only
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    if (!/^[a-zA-Z]+$/.test(firstName)) {
        document.getElementById('firstNameError').textContent = 'First name must contain only letters.';
        isValid = false;
    }
    if (!/^[a-zA-Z]+$/.test(lastName)) {
        document.getElementById('lastNameError').textContent = 'Last name must contain only letters.';
        isValid = false;
    }

    // Validate Email: Specific format (example@upr.edu)
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._%+-]+@upr\.edu$/;
    if (!emailPattern.test(email)) {
        document.getElementById('emailError').textContent = 'Please enter a valid UPR email (example@upr.edu).';
        isValid = false;
    }

    // Validate Department: Characters only
    const department = document.getElementById('department').value.trim();
    if (!/^[a-zA-Z\s]+$/.test(department)) {
        document.getElementById('departmentError').textContent = 'Department must contain only letters.';
        isValid = false;
    }

    // Validate Laboratory: Format should be 1-5 letters, dash, and up to three alphanumeric characters
    const laboratory = document.getElementById('laboratory').value.trim();
    const labPattern = /^[A-Za-z]{1,5}-[A-Za-z0-9]{1,3}$/;
    if (!labPattern.test(laboratory)) {
        document.getElementById('laboratoryError').textContent = 'Lab format should be letters-digits (e.g., S-122).';
        isValid = false;
    }

    // Validate Role: Ensure selection
    const role = document.getElementById('role').value;
    if (!role) {
        document.getElementById('roleError').textContent = 'Please select a role.';
        isValid = false;
    }

    // If all fields are valid, proceed with adding the user
    if (isValid) {
        const newUser = { username: email, laboratories: laboratory, role: role, date: new Date().toISOString().split('T')[0] };
        users.push(newUser);  // Assuming `users` array is used to store user entries
        document.getElementById('roleForm').reset();
        alert('User successfully added!');  // Replace this with your alert modal if needed
    }
}
// Function to render submitted requests
function renderSubmittedRequestsTable() {
    const tableBody = document.getElementById('submittedRequestsTableBody');
    tableBody.innerHTML = '';
    submittedRequests.forEach((user, index) => {
        const row = `<tr>
            <td>${user.username}</td>
            <td>${user.laboratories}</td>
            <td>${user.role}</td>
            <td>${user.date}</td>
            <td>
                <button class="btn btn-success btn-sm" onclick="acceptRequest(${index})">Accept</button>
                <button class="btn btn-danger btn-sm" onclick="denyRequest(${index})">Deny</button>
            </td>
        </tr>`;
        tableBody.innerHTML += row;
    });
}

// Function to accept a user request
function acceptRequest(index) {
    const acceptedUser = submittedRequests[index];
    users.push(acceptedUser);
    submittedRequests.splice(index, 1);
    renderSubmittedRequestsTable();
    showAlert("Request accepted. User can now be searched.");
}

// Function to deny a request
function denyRequest(index) {
    submittedRequests.splice(index, 1);
    renderSubmittedRequestsTable();
    showAlert("Request denied.");
}

// Function to render edit users table based on search results
function renderEditUsersTable(userList) {
    const tableBody = document.getElementById('searchResultsTableBody');
    tableBody.innerHTML = '';
    userList.forEach((user, index) => {
        const row = `<tr>
            <td>${user.username}</td>
            <td>${user.laboratories}</td>
            <td>${user.role}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="openEditModal('${user.username}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal('${user.username}')">Delete</button>
            </td>
        </tr>`;
        tableBody.innerHTML += row;
    });
}

// Function to search for a user
function searchUsername() {
    const searchValue = document.getElementById('searchUsername').value.trim().toLowerCase();
    const searchResults = users.filter(user => user.username.toLowerCase().includes(searchValue));
    renderEditUsersTable(searchResults);
}

// Open edit modal with selected user info
function openEditModal(username) {
    editIndex = users.findIndex(user => user.username === username);
    const user = users[editIndex];
    document.getElementById('editLaboratory').value = user.laboratories;
    document.getElementById('editRole').value = user.role;
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}

// Save edited changes
function saveEdit() {
    users[editIndex].laboratories = document.getElementById('editLaboratory').value;
    users[editIndex].role = document.getElementById('editRole').value;
    showAlert("User successfully edited.");
    searchUsername(); // Refresh search results
}

// Open delete confirmation modal
function openDeleteModal(username) {
    editIndex = users.findIndex(user => user.username === username);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    deleteModal.show();
}

// Confirm and delete a user
function confirmDelete() {
    users.splice(editIndex, 1);
    showAlert("User successfully deleted.");
    searchUsername(); // Refresh search results
}

// Show an alert modal
function showAlert(message) {
    document.getElementById('alertModalBody').textContent = message;
    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
    alertModal.show();
}

function toggleCertifiedUsers() {
    const certifiedContainer = document.getElementById('certifiedUsersContainer');
    if (certifiedContainer.style.display === 'none') {
        certifiedContainer.style.display = 'block';
        renderCertifiedUsersTable();
    } else {
        certifiedContainer.style.display = 'none';
    }
}

function renderCertifiedUsersTable() {
    const tableBody = document.getElementById('certifiedUsersTableBody');
    tableBody.innerHTML = '';
    certifiedUsers.forEach(user => {
        const row = `<tr>
            <td>${user.firstName}</td>
            <td>${user.lastName}</td>
            <td>${user.completionDate}</td>
            <td>${user.department}</td>
        </tr>`;
        tableBody.innerHTML += row;
    });
}

// PDF Generation function
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Certified Users", 10, 10);
    
    let y = 20;
    certifiedUsers.forEach(user => {
        doc.text(`${user.firstName} ${user.lastName} - ${user.completionDate} - ${user.department}`, 10, y);
        y += 10;
    });

    doc.save("Certified_Users.pdf");
}

// Initial render
document.addEventListener('DOMContentLoaded', renderSubmittedRequestsTable);
</script>
@endsection
