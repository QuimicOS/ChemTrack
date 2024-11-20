@extends('admin.templateAdmin')

@section('title', 'Role Management')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
        <h1 class="display-5">Role Management</h1>
        <hr class="my-4">
    </div>
    <fieldset>
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
            <!-- Room Number Field with Add Button -->
            <div class="col-md-4">
                <label for="laboratory" class="form-label">Room Number</label>
                <div id="roomNumberContainer">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control room-number" placeholder="Enter Lab (e.g., S-122)">
                        <button type="button" class="btn btn-outline-success" onclick="addRoomNumberField()">+</button>
                    </div>
                </div>
                <small class="text-danger" id="laboratoryError"></small>
            </div>

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
    </fieldset>

    <!-- Submitted Requests Table -->
    <fieldset>
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
    </fieldset>

    <!-- Search Section for Editing Users -->
    <fieldset>
    <div class="row mb-3 mt-5">
        <div class="col-md-8">
            <label for="searchUsername" class="form-label">Search Username</label>
            <input type="text" class="form-control" id="searchUsername" placeholder="Enter username (e.g., name.lastname)">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="searchUsername()">Search</button>
        </div>
    </div>
    </fieldset>

    <!-- Edit Users Table -->
    <fieldset>
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
    </fieldset>

    <div class="modal" id="editUserModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId"> <!-- Hidden userId field -->
                    <div class="mb-3">
                        <label for="editLaboratory" class="form-label">Room Number</label>
                        <input type="text" id="editLaboratory" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <input type="text" id="editRole" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteButton" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    

    <!-- Show Certified Users Button -->
    <fieldset>
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
    </fieldset>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>

// Function to add new room fields
function addRoomNumberField() {
    const roomNumberContainer = document.getElementById('roomNumberContainer');
    const newInputGroup = document.createElement('div');
    newInputGroup.classList.add('input-group', 'mb-2');

    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.className = 'form-control room-number';
    newInput.placeholder = 'Enter room number (e.g., S-122)';

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-outline-danger';
    removeButton.textContent = '-';
    removeButton.onclick = () => newInputGroup.remove();

    newInputGroup.appendChild(newInput);
    newInputGroup.appendChild(removeButton);
    roomNumberContainer.appendChild(newInputGroup);
}
// Regular expressions for validation
const namePattern = /^[a-zA-Z\s]+$/; // Only letters and spaces for names and department
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const roomNumberPattern = /^[a-zA-Z0-9]{1,5}-[a-zA-Z0-9]{1,5}$/; // Room number format

// Toggle submit button based on all validations
function toggleSubmitButton() {
    const nameField = document.getElementById('name');
    const lastNameField = document.getElementById('lastName');
    const emailField = document.getElementById('email');
    const departmentField = document.getElementById('department');
    const roomNumberFields = document.querySelectorAll('.room-number');
    const roleField = document.getElementById('role');
    const submitButton = document.getElementById('submitBtn');

    // Check if all fields are valid
    const isValid = 
        namePattern.test(nameField.value.trim()) &&
        namePattern.test(lastNameField.value.trim()) &&
        emailPattern.test(emailField.value.trim()) &&
        namePattern.test(departmentField.value.trim()) &&
        Array.from(roomNumberFields).every(field => roomNumberPattern.test(field.value.trim())) &&
        roleField.value; // Ensures role is selected

    submitButton.disabled = !isValid;  // Enable button only if all fields are valid
}

// Add additional room number field
function addRoomNumberField() {
    const roomNumberContainer = document.getElementById('roomNumberContainer');
    const newFieldGroup = document.createElement('div');
    newFieldGroup.classList.add('input-group', 'mb-2');

    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.className = 'form-control room-number';
    newInput.placeholder = 'Enter room number (e.g., B-257)';
    newInput.required = true;

    // Add event listener for validation
    newInput.addEventListener('input', toggleSubmitButton);

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-outline-danger';
    removeButton.textContent = '-';
    removeButton.onclick = () => {
        newFieldGroup.remove();
        toggleSubmitButton(); // Re-validate form after removal
    };

    newFieldGroup.appendChild(newInput);
    newFieldGroup.appendChild(removeButton);

    roomNumberContainer.appendChild(newFieldGroup);
    toggleSubmitButton(); // Re-validate form
}

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
    if (!/^[a-zA-Z\s]+$/.test(firstName)) {
        document.getElementById('firstNameError').textContent = 'First name must contain only letters.';
        isValid = false;
    }
    if (!/^[a-zA-Z\s]+$/.test(lastName)) {
        document.getElementById('lastNameError').textContent = 'Last name must contain only letters.';
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        document.getElementById('emailError').textContent = 'Invalid email format.';
        isValid = false;
    }

    // Validate Department
    const department = document.getElementById('department').value.trim();
    if (!/^[a-zA-Z\s]+$/.test(department)) {
        document.getElementById('departmentError').textContent = 'Department must contain only letters.';
        isValid = false;
    }

    // Validate Role
    const role = document.getElementById('role').value;
    if (!role) {
        document.getElementById('roleError').textContent = 'Please select a role.';
        isValid = false;
    }

    // Get Room Number (Optional)
    const roomNumber = document.querySelector('.room-number').value.trim();

    // Submit form if valid
    if (isValid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/newUsers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                name: firstName,
                last_name: lastName,
                email: email,
                department: department,
                room_number: roomNumber || null,
                role: role,
            }),
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        console.error('Validation errors:', errorData);
                        throw new Error('Failed to add user.');
                    });
                }
                return response.json();
            })
            .then(data => {
                alert('User added successfully!');
                document.getElementById('roleForm').reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add user. Please try again.');
            });
    }
}

// Function to render submitted requested users
function renderSubmittedRequestsTable() {
    const tableBody = document.getElementById('submittedRequestsTableBody');
    tableBody.innerHTML = ''; // Clear existing table rows

    // Fetch users with 'requested' status
    fetch('/users/requested', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Error fetching requested users');
                });
            }
            return response.json(); // Parse JSON if response is OK
        })
        .then(data => {
            // Check if data is empty
            if (!data.requested_users || data.requested_users.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No requested users found</td></tr>`;
                return;
            }

            // Populate the table with user data
            data.requested_users.forEach(user => {
                const row = `
                    <tr>
                        <td>${user.email}</td>
                        <td>${user.room_number || 'N/A'}</td>
                        <td>${user.role || 'N/A'}</td>
                        <td>${new Date(user.created_at).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="acceptRequest(${user.id})">Accept</button>
                            <button class="btn btn-danger btn-sm" onclick="denyRequest(${user.id})">Deny</button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error:', error.message);
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">Error fetching requested users</td></tr>`;
        });
}





// Function to accept a user request
function acceptRequest(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`/userStatus/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            completion_status: true,
            completion_date: new Date().toISOString().split('T')[0], // Today's date
        }),
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    console.error('Validation errors:', errorData);
                    throw new Error('Failed to accept user request');
                });
            }
            return response.json();
        })
        .then(data => {
            alert('User request accepted successfully!');
            renderSubmittedRequestsTable(); // Refresh the table
        })
        .catch(error => {
            console.error(error);
            alert(error.message);
        });
}



function denyRequest(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/userInvalid/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to deny user request');
            }
            return response.json();
        })
        .then(() => {
            alert('User request denied');
            renderSubmittedRequestsTable(); // Refresh the table
        })
        .catch(error => {
            console.error(error);
            alert('Failed to deny user request');
        });
}


// Function to render edit users table based on search results
function renderEditUsersTable(userList) {
    const tableBody = document.getElementById('searchResultsTableBody');
    tableBody.innerHTML = ''; // Clear previous results

    userList.forEach(user => {
        const row = `<tr>
            <td>${user.email}</td>
            <td>${user.room_number || 'N/A'}</td>
            <td>${user.role}</td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="openEditModal('${user.id}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal('${user.id}')">Delete</button>
            </td>
        </tr>`;
        tableBody.innerHTML += row;
    });

    // Show a message if no users match the search
    if (userList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center">No users found</td></tr>`;
    }
}



// Function to search for a user by a email
function searchUsername() {
    const searchValue = document.getElementById('searchUsername').value.trim();

    if (!searchValue) {
        alert("Please enter a username to search.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/users/search/${encodeURIComponent(searchValue)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Active user not found');
            }
            return response.json();
        })
        .then(user => {
            renderEditUsersTable([user]);
        })
        .catch(error => {
            console.error(error);
            alert('Active user not found or an error occurred.');
            renderEditUsersTable([]); // Clear the table if no user is found
        });
}



// Open edit modal with selected user info
function openEditModal(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/users/${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('User not found');
            }
            return response.json();
        })
        .then(user => {
            // Populate the modal
            document.getElementById('editLaboratory').value = user.room_number;
            document.getElementById('editRole').value = user.role;

            // Store the userId in a hidden field
            document.getElementById('editUserId').value = user.id;

            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        })
        .catch(error => {
            console.error(error);
            alert('Failed to fetch user details.');
        });
}





// Save edited changes
function saveEdit() {
    const userId = document.getElementById('editUserId').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const updatedData = {
        room_number: document.getElementById('editLaboratory').value,
        role: document.getElementById('editRole').value,
    };

    fetch(`/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(updatedData),
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    console.error('Validation errors:', errorData);
                    alert(errorData.error || 'Failed to update user.');
                    throw new Error('Validation failed.');
                });
            }
            return response.json();
        })
        .then(data => {
            alert('User updated successfully!');
            renderSubmittedRequestsTable();
        })
        .catch(error => {
            console.error(error);
        });
}





// Open delete confirmation modal
function openDeleteModal(userId) {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    document.getElementById('confirmDeleteButton').onclick = () => confirmDelete(userId);
    deleteModal.show();
}

// Confirm and delete a user
function confirmDelete(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/userInvalid/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete user');
            }
            return response.json();
        })
        .then(data => {
            alert('User status set to Inactive');
            renderSubmittedRequestsTable(); // Refresh table
        })
        .catch(error => {
            console.error(error);
            alert('Failed to delete user.');
        });
}




// Show an alert modal
function showAlert(message) {
    document.getElementById('alertModalBody').textContent = message;
    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
    alertModal.show();
}

// Toggle the display of the certified users container
function toggleCertifiedUsers() {
    const certifiedContainer = document.getElementById('certifiedUsersContainer');
    if (certifiedContainer.style.display === 'none' || !certifiedContainer.style.display) {
        certifiedContainer.style.display = 'block';
        fetchCertifiedUsers(); // Fetch and render certified users
    } else {
        certifiedContainer.style.display = 'none';
    }
}

// Fetch certified users from the backend and render the table
function fetchCertifiedUsers() {
    const tableBody = document.getElementById('certifiedUsersTableBody');
    tableBody.innerHTML = ''; // Clear existing rows

    // Fetch certified users
    fetch('/users/certified', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Error fetching certified users');
                });
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            const certifiedStudents = data.certified_students;

            // If no certified users are found, show a message
            if (!certifiedStudents || certifiedStudents.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center">No certified users found</td></tr>`;
                return;
            }

            // Populate the table with certified users
            certifiedStudents.forEach(user => {
                const row = `
                    <tr>
                        <td>${user.name || 'N/A'}</td>
                        <td>${user.last_name || 'N/A'}</td>
                        <td>${user.certification_date ? new Date(user.certification_date).toLocaleDateString() : 'N/A'}</td>
                        <td>${user.department || 'N/A'}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error fetching certified users:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Error fetching certified users</td></tr>`;
        });
}

// PDF Generation for Certified Users
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Certified Users", 10, 10);

    let y = 20;
    const tableRows = document.querySelectorAll('#certifiedUsersTableBody tr');
    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowText = Array.from(cells).map(cell => cell.textContent).join(' - ');
        doc.text(rowText, 10, y);
        y += 10;
    });

    doc.save("Certified_Users.pdf");
}



// Render initial data
document.addEventListener('DOMContentLoaded', renderSubmittedRequestsTable);
</script>
@endsection
