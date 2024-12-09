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
                                <input type="text" class="form-control room-number"
                                    placeholder="Enter Lab (e.g., S-122)">
                                <button type="button" class="btn btn-outline-success"
                                    onclick="addRoomNumberField()">+</button>
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
                    <button type="button" class="btn btn-success" onclick="validateForm()" disabled>Add User</button>
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

        <!-- Deny user modal -->
        <div class="modal fade" id="denyUserModal" tabindex="-1" aria-labelledby="denyUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="denyUserModalLabel">Confirm Denial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to deny the following user?
                        <ul>
                            <li><strong>Email:</strong> <span id="denyUserEmail"></span></li>
                            <li><strong>Requested Role:</strong> <span id="denyUserRole"></span></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDenyButton" class="btn btn-danger">Deny User</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Search Section for Editing Users -->
        <fieldset>
            <div class="row mb-3 mt-5">
                <div class="col-md-8">
                    <h3>
                        <label for="searchUsername" class="form-label">Search User to Edit</label>
                        <input type="text" class="form-control" id="searchUsername"
                            placeholder="Enter username (e.g., name.lastname)">
                    </h3>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" onclick="searchUsername()">Search</button>
                </div>
            </div>

            <!-- Edit Users Table -->
            <div id="editUsersTableContainer" class="table-container mt-3" style="display: none;">
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
                        <!-- Default Empty Row -->
                        <tr>
                            <td colspan="4" class="text-center">No users found. Use the search bar to find a user.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <!-- Edit user modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
            aria-hidden="true">
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
                            <button type="button" class="btn btn-outline-success" onclick="addEditRoomNumberField()">+
                                Add Room</button>
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



        <!-- Delete user modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
            aria-hidden="true">
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
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteButton" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Certified Users Button -->
        <div class="text-center mt-5">
            <button type="button" class="btn btn-primary" onclick="toggleCertifiedUsers()">Show Certified Users</button>
        </div>

        <!-- Certified Users Fieldset -->
        <fieldset id="certifiedUsersFieldset" style="display: none;">
            <div class="table-container mt-3">
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
            const namePattern = /^[\s\S]+$/; 
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
            const roomNumberPattern = /^[\s\S]+$/; 

            document.addEventListener('DOMContentLoaded', () => {
                // Attach event listeners to all fields for validation
                const fieldsToValidate = document.querySelectorAll(
                    '#firstName, #lastName, #email, #department, #role, .room-number'
                );
                fieldsToValidate.forEach((field) => {
                    field.addEventListener('input', toggleSubmitButton);
                    field.addEventListener('change', toggleSubmitButton); // For dropdowns like role
                });

                // Run the toggleSubmitButton function on page load in case fields are prefilled
                toggleSubmitButton();
            });


            // Toggle submit button based on all validations
            function toggleSubmitButton() {
                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const email = document.getElementById('email').value.trim();
                const department = document.getElementById('department').value.trim();
                const role = document.getElementById('role').value;
                const roomNumberFields = document.querySelectorAll('.room-number');

                // Validate fields
                const isValid =
                    firstName !== '' &&
                    lastName !== '' &&
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && // Email validation
                    department !== '' &&
                    role !== '' &&
                    Array.from(roomNumberFields).every(field => field.value.trim() !== ''); // Validate all room numbers

                // Enable or disable the Add button
                const addButton = document.querySelector('button[onclick="validateForm()"]');
                if (addButton) {
                    addButton.disabled = !isValid; // Disable button if any field is invalid
                }
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

                // Validate fields
                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const email = document.getElementById('email').value.trim();
                const department = document.getElementById('department').value.trim();
                const role = document.getElementById('role').value;
                const roomNumberFields = document.querySelectorAll('.room-number');
                const roomNumbers = Array.from(roomNumberFields)
                    .map(field => field.value.trim())
                    .filter(room => room !== "") // Filter out empty room numbers
                    .map(room => ({
                        room_number: room
                    })); // Map to required format

                isValid =
                    firstName !== '' &&
                    lastName !== '' &&
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && // Email validation
                    department !== '' &&
                    role !== '' &&
                    roomNumbers.length > 0;

                // Disable/Enable Add button based on validation
                const addButton = document.querySelector('button[onclick="validateForm()"]');
                addButton.disabled = !isValid;

                // If form is not valid, return early
                if (!isValid) return;

                // Send request if form is valid
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const roleRequestData = {
                    user: {
                        name: firstName,
                        last_name: lastName,
                        email: email,
                        department: department,
                        role: role,
                    },
                    rooms: roomNumbers,
                };

                fetch('/AdminnewUsers', {
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
                        addButton.disabled = true; // Disable button after successful submission
                    })
                    .catch(error => {
                        // Check if there's an error message and display it in an alert
                        if (error.message) {
                            alert(error.message);
                        } else {
                            alert('An unexpected error occurred. Please try again.');
                        }
                        console.error('Error:', error); // Keep for debugging
                    });
            }



            //Function that shows a drop down menu of the rooms (Pending)
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
                if (!tableBody) return; // Ensure the element exists

                tableBody.innerHTML = ''; // Clear existing table rows

                fetch('/Adminusers/requested', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error fetching requested users');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.requested_users || data.requested_users.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="5" class="text-center">No requested users found</td></tr>`;
                            return;
                        }

                        data.requested_users.forEach(user => {
                            const row = `
                    <tr>
                        <td>${user.email}</td>
                        <td>${user.room_numbers || 'N/A'}</td>
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
                        tableBody.innerHTML =
                            `<tr><td colspan="5" class="text-center">No requested users found at the moment</td></tr>`;
                    });
            }


            // Function to accept a user request
            function acceptRequest(userId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch(`/AdminuserStatus/${userId}`, {
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


            //Function to deny a request
            function denyRequest(userId) {
                // Fetch user details to populate modal
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/Adminusers/${userId}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch user details for denial');
                        }
                        return response.json();
                    })
                    .then(user => {
                        // Populate modal fields
                        document.getElementById('denyUserEmail').textContent = user.email || 'N/A';
                        document.getElementById('denyUserRole').textContent = user.role || 'N/A';

                        // Attach user ID to the confirm button
                        const confirmDenyButton = document.getElementById('confirmDenyButton');
                        confirmDenyButton.onclick = () => confirmDeny(userId);

                        // Show the modal
                        const denyModal = new bootstrap.Modal(document.getElementById('denyUserModal'));
                        denyModal.show();
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Error fetching user details for denial.');
                    });
            }

            //Funtion to confirm a user
            function confirmDeny(userId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/AdminuserInvalid/${userId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to deny user');
                        }
                        return response.json();
                    })
                    .then(() => {
                        alert('User request denied');

                        // Hide the deny modal
                        const denyModal = bootstrap.Modal.getInstance(document.getElementById('denyUserModal'));
                        denyModal.hide();

                        // Clear modal content
                        document.getElementById('denyUserEmail').textContent = '';
                        document.getElementById('denyUserRole').textContent = '';

                        // Refresh the table
                        renderSubmittedRequestsTable();
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Failed to deny user request.');
                    });
            }

            // Function to render edit users table based on search results
            function renderEditUsersTable(userList) {
                const tableContainer = document.getElementById('editUsersTableContainer');
                const tableBody = document.getElementById('searchResultsTableBody');
                tableBody.innerHTML = ''; // Clear previous results

                if (!userList || userList.length === 0) {
                    // Hide the table if no results
                    tableContainer.style.display = 'none';
                    return;
                }

                // Show the table
                tableContainer.style.display = 'block';

                userList.forEach(user => {
                    const roomNumbers = user.room_numbers ?
                        Array.isArray(user.room_numbers) ? user.room_numbers.join(', ') : user.room_numbers :
                        'N/A';

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
            }

            // Function to search for a user by a email
            function searchUsername() {
                const searchValue = document.getElementById('searchUsername').value.trim();

                if (!searchValue) {
                    alert("Please enter a username to search.");
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/Adminusers/search/${encodeURIComponent(searchValue)}`, {
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
                        renderEditUsersTable([]); // Hide the table if no user is found
                    });
            }

            //Function to show the edit modal
            function openEditModal(userId) {
                //console.log('Opening edit modal for user ID:', userId); // Debug the passed userId

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/Adminusers/${userId}`, {
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
                        //console.log('Fetched User Data:', user); // Debug fetched user data

                        // Populate modal fields
                        document.getElementById('editUserId').value = user.user_id; // Use user.user_id here
                        document.getElementById('editRole').value = user.role;

                        const roomNumbers = user.room_numbers ?
                            user.room_numbers.split(',').map(room => room.trim()) :
                            []; // Parse room numbers into an array

                        //console.log('Parsed Room Numbers:', roomNumbers); // Debug parsed room numbers
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

            //Function to save the changes
            function saveEdit() {
                const userId = document.getElementById('editUserId').value; // Retrieve user ID

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

                fetch(`/Adminusers/${userId}`, {
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

                        // Close the modal
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                        if (editModal) {
                            editModal.hide();
                        }

                        // Refresh the table by performing the same search again
                        const searchValue = document.getElementById('searchUsername').value.trim();
                        if (searchValue) {
                            searchUsername(); // Refresh table with the current search term
                        }
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

                fetch(`/Adminusers/${userId}`, { // Adjusted endpoint to match the delete by ID route
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
                        // Hide the delete modal
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
                        deleteModal.hide();
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
                const certifiedFieldset = document.getElementById('certifiedUsersFieldset');
                certifiedFieldset.style.display = (certifiedFieldset.style.display === 'none' || !certifiedFieldset.style
                    .display) ? 'block' : 'none';

                // Fetch data only when the fieldset is displayed
                if (certifiedFieldset.style.display === 'block') {
                    fetchCertifiedUsers();
                }
            }

            // Fetch certified users from the backend and render the table
            function fetchCertifiedUsers() {
                const tableBody = document.getElementById('certifiedUsersTableBody');
                tableBody.innerHTML = ''; // Clear existing rows

                // Fetch certified users
                fetch('/Adminusers/certified', {
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
                            tableBody.innerHTML =
                                `<tr><td colspan="4" class="text-center">No certified users found</td></tr>`;
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
                        tableBody.innerHTML =
                            `<tr><td colspan="4" class="text-center">Error fetching certified users</td></tr>`;
                    });
            }



            // PDF Generation for Certified Users
            function generatePDF() {
                const {
                    jsPDF
                } = window.jspdf;

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
                    head: [
                        ["Name", "Last Name", "Completion Date", "Department"]
                    ],
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
