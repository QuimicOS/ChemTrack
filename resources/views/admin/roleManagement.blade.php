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
                <select class="form-select" id="role">
                    <option value="" disabled selected>Select Role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Professor">Professor</option>
                    <option value="Staff">Teaching Assistant/Lab Technician/Student</option>
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
            <h3>
            <label for="searchUsername" class="form-label">Search User to Edit</label>
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

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden User ID Field -->
                    <input type="hidden" id="editUserId">
    
                  
                    <!-- Room Numbers -->
                    <div class="mb-3">
                        <label for="editRoomNumbers" class="form-label">Room Numbers</label>
                        <div id="editRoomNumberContainer">
                            <!-- Existing room number fields will be populated dynamically -->
                        </div>
                        <button type="button" class="btn btn-outline-success" onclick="addEditRoomNumberField()">+ Add Room</button>
                    </div>

     
    
                    <!-- Role Dropdown -->
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole">
                            <option value="" disabled selected>Select Role</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Professor">Professor</option>
                            <option value="Staff">Teaching Assistant/Lab Technician/Student</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="saveEdit()">Save Changes</button>
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
        <button type="button" class="btn btn-primary" onclick="toggleCertifiedUsers()">Show Certified Users</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>

let labData = [];
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

// Define form fields
const firstNameField = document.getElementById('firstName');
const lastNameField = document.getElementById('lastName');
const emailField = document.getElementById('email');
const departmentField = document.getElementById('department');
const roomNumberField = document.getElementById('roomNumber');
const roleField = document.getElementById('role');
const addUserBtn = document.getElementById('addUserBtn');

// Function to validate and add new user
function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.getElementById('firstNameError').textContent = '';
    document.getElementById('lastNameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('departmentError').textContent = '';
    document.getElementById('roleError').textContent = '';
    document.getElementById('laboratoryError').textContent = '';

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

    // Validate Room Numbers
    const roomNumberFields = document.querySelectorAll('.room-number');
    const roomNumbers = Array.from(roomNumberFields)
        .map(field => field.value.trim())
        .filter(room => room !== "") // Filter out empty room numbers
        .map(room => ({ room_number: room })); // Map to required format

    // Submit form if valid
    if (isValid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Payload structured to match the backend
        const roleRequestData = {
            user: {
                name: firstName,
                last_name: lastName,
                email: email,
                department: department,
                role: role,
            },
            rooms: roomNumbers, // Send only valid rooms
        };

        fetch('/newUsers', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify(roleRequestData),
})
.then(response => {
    if (!response.ok) {
        return response.json().then(errorData => {
            throw errorData; // Pass the error data to the catch block
        });
    }
    return response.json();
})
.then(data => {
    alert('User added successfully!');
    document.getElementById('roleForm').reset();
})
.catch(error => {
    if (error.invalid_rooms) {
        document.getElementById('laboratoryError').textContent = `Invalid rooms: ${error.invalid_rooms.join(', ')}`;
    } else if (error.message) {
        alert(error.message);
    } else {
        alert('An unexpected error occurred. Please try again.');
    }
});


    }
}





function populateLabDropdowns(data) {
        const roomNumberSelect = document.getElementById('editLaboratory');
        roomNumberSelect.innerHTML = '<option value="" selected>Select Room Number</option>';

        data.forEach(lab => {
            roomNumberSelect.add(new Option(`${lab.room_number}`, lab.room_number));
        });
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
        const tableBody = document.getElementById('submittedRequestsTableBody');
        tableBody.innerHTML = ''; // Clear existing rows

        // Check if data is empty
        if (!data.requested_users || data.requested_users.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No requested users found</td></tr>`;
            return;
        }

        // Populate the table with user data
        data.requested_users.forEach(user => {
            const roomNumbers = user.room_numbers; // Use 'room_numbers' from backend

            const row = `
                <tr>
                    <td>${user.email}</td>
                    <td>${roomNumbers}</td>
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
        const tableBody = document.getElementById('submittedRequestsTableBody');
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No requested users found</td></tr>`;
    });






        fetch('/laboratories')
            .then(response => response.json())
            .then(data => {
                labData = data;
                populateLabDropdowns(data);
            })
            .catch(error => console.error('Error fetching laboratories:', error));


            document.getElementById("editLaboratory").addEventListener("change", function () {
            const selectedRoom = this.value;
            const labDetails = labData.find(lab => lab.room_number === selectedRoom);
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
        const roomNumbers = user.room_numbers
            ? Array.isArray(user.room_numbers) ? user.room_numbers.join(', ') : user.room_numbers
            : 'N/A';

        const row = `<tr>
            <td>${user.email}</td>
            <td>${roomNumbers}</td>
            <td>${user.role || 'N/A'}</td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="openEditModal('${user.id}')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal('${user.id}')">Delete</button>
            </td>
        </tr>`;
        tableBody.innerHTML += row;
    });

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
            alert('Active user not found.');
            renderEditUsersTable([]); // Clear the table if no user is found
        });
}

function openEditModal(userId) {
    console.log('Opening edit modal for user ID:', userId); // Debug the passed userId

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
            console.log('Fetched User Data:', user); // Debug fetched user data

            // Populate modal fields
            document.getElementById('editUserId').value = user.user_id; // Use user.user_id here
            document.getElementById('editRole').value = user.role;

            const roomNumbers = user.room_numbers
                ? user.room_numbers.split(',').map(room => room.trim())
                : []; // Parse room numbers into an array

            console.log('Parsed Room Numbers:', roomNumbers); // Debug parsed room numbers
            populateEditRoomNumbers(roomNumbers);

            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        })
        .catch(error => {
            console.error(error);
            alert('Failed to fetch user details.');
        });
}





// Populate existing room numbers in edit modal
function populateEditRoomNumbers(roomNumbers) {
    const editRoomNumberContainer = document.getElementById('editRoomNumberContainer');
    editRoomNumberContainer.innerHTML = ''; // Clear existing fields

    if (!roomNumbers || roomNumbers.length === 0) {
        // Add a default empty field if no room numbers exist
        addEditRoomNumberField();
        return;
    }

    roomNumbers.forEach((room) => {
        const fieldGroup = document.createElement('div');
        fieldGroup.classList.add('input-group', 'mb-2');

        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control room-number';
        input.value = room.trim(); // Populate with existing room number
        input.placeholder = 'Enter room number (e.g., S-122)';
        input.required = true;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-outline-danger';
        removeButton.textContent = '-';
        removeButton.onclick = () => fieldGroup.remove();

        fieldGroup.appendChild(input);
        fieldGroup.appendChild(removeButton);
        editRoomNumberContainer.appendChild(fieldGroup);
    });
}


// Add new room number field in edit modal
function addEditRoomNumberField() {
    const editRoomNumberContainer = document.getElementById('editRoomNumberContainer');

    const fieldGroup = document.createElement('div');
    fieldGroup.classList.add('input-group', 'mb-2');

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control room-number';
    input.placeholder = 'Enter room number (e.g., S-122)';
    input.required = true;

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-outline-danger';
    removeButton.textContent = '-';
    removeButton.onclick = () => fieldGroup.remove();

    fieldGroup.appendChild(input);
    fieldGroup.appendChild(removeButton);
    editRoomNumberContainer.appendChild(fieldGroup);
}


function saveEdit() {
    const userId = document.getElementById('editUserId').value; // Retrieve user ID
    console.log('Editing User ID:', userId); // Debug user ID

    if (!userId) {
        alert('User ID is missing. Cannot save changes.');
        return;
    }

    const role = document.getElementById('editRole').value;

    // Gather all room numbers
    const roomNumbers = Array.from(document.querySelectorAll('#editRoomNumberContainer .room-number'))
        .map(input => input.value.trim())
        .filter(value => value); // Exclude empty values

    const updatedData = {
        role, // Role data
        room_numbers: roomNumbers, // Room numbers array
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(updatedData), // Send data as JSON
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
        .then(() => {
            alert('User updated successfully!');
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            editModal.hide();
            renderSubmittedRequestsTable(); // Refresh table
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

    fetch(`/users/${userId}`, { // Adjusted endpoint to match the delete by ID route
        method: 'DELETE', // Use DELETE method for deletion
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
            alert(`User deleted successfully.`);
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
    certifiedContainer.style.display = (certifiedContainer.style.display === 'none' || !certifiedContainer.style.display) ? 'block' : 'none';
    if (certifiedContainer.style.display === 'block') {
        fetchCertifiedUsers(); // Fetch certified users only when visible
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

    // Initialize jsPDF instance
    const doc = new jsPDF();

    // Set the title of the document
    doc.text("Certified Users", 10, 10);

    // Prepare table data
    const tableBody = document.querySelectorAll('#certifiedUsersTableBody tr');
    const rows = [];

    tableBody.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = Array.from(cells).map(cell => cell.textContent || "N/A");
        rows.push(rowData);
    });

    // Use autoTable to generate the table
    doc.autoTable({
        head: [["Name", "Last Name", "Completion Date", "Department"]],
        body: rows,
        startY: 20, // Starting Y position of the table
    });

    // Save the PDF
    doc.save("Certified_Users_Table.pdf");
}




// Render initial data
document.addEventListener('DOMContentLoaded', renderSubmittedRequestsTable);
</script>
@endsection