@extends('professor.templateProfessor')

@section('title', 'Role Request - ChemTrack')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

    <style>
        {{-- Inline CSS to style the form and layout --}} .content-area {
            margin-left: 120px;
            padding: 1.25rem;
            margin-top: 25px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
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
        <div class="text-center mb-4">
            <!-- Title -->
            <h1 class="display-5">User Request</h1>
            <hr class="my-4">
        </div>

        <fieldset>
            <!-- Form Section (Add User) -->
            <form id="roleRequestForm" novalidate>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" placeholder="Enter department" required>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label for="roomNumber" class="form-label">Room Number</label>
                        <div id="roomNumberContainer">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control room-number" id="roomNumber"
                                    placeholder="Enter room number (e.g., B-257)" required>
                                <button type="button" class="btn btn-outline-success"
                                    onclick="addRoomNumberField()">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role"
                            value="Teaching Assistant/Lab Technician/Student" readonly>
                    </div>
                </div>

                <div class="text-center">
                    {{-- Submit button, initially disabled --}}
                    <button type="button" class="btn btn-success" id="submitBtn" disabled>Submit Request</button>
                </div>
            </form>
        </fieldset>
    </div>
@endsection

@section('scripts')
    <script>
        // Function to validate the form
        function validateRoleRequestForm() {
            const nameField = document.getElementById('name'); 
            const lastNameField = document.getElementById('lastName'); 
            const emailField = document.getElementById('email'); 
            const departmentField = document.getElementById('department'); 
            const roomNumberFields = document.querySelectorAll('.room-number'); 
            const submitButton = document.getElementById('submitBtn'); 

            // Check if all fields are filled
            const isValid =
                nameField.value.trim() !== '' && 
                lastNameField.value.trim() !== '' &&
                emailField.value.trim() !== '' && 
                departmentField.value.trim() !== '' && 
                Array.from(roomNumberFields).every(field => field.value.trim() !==
                ''); // Ensures all room number fields are filled

            submitButton.disabled = !isValid; // Enables the submit button only if all fields are valid
        }



        // Function to add a new room number field
        function addRoomNumberField() {
            const roomNumberContainer = document.getElementById(
            'roomNumberContainer'); 
            const newFieldGroup = document.createElement('div'); 
            newFieldGroup.classList.add('input-group', 'mb-2'); 

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control room-number';
            newInput.placeholder = 'Enter room number (e.g., B-257)'; 
            newInput.required = true; 

            newInput.addEventListener('input', validateRoleRequestForm); // Attaches validation logic to the new input field

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-outline-danger'; 
            removeButton.textContent = '-'; 
            removeButton.onclick = () => {
                newFieldGroup.remove(); 
                validateRoleRequestForm();
            };

            newFieldGroup.appendChild(newInput);
            newFieldGroup.appendChild(removeButton);

            roomNumberContainer.appendChild(newFieldGroup);
            validateRoleRequestForm(); // Re-validates the form
        }


        // Function to submit the form
        function submitRoleRequest() {
            const nameField = document.getElementById('name'); 
            const lastNameField = document.getElementById('lastName'); 
            const emailField = document.getElementById('email'); 
            const departmentField = document.getElementById('department'); 
            const roomNumberFields = document.querySelectorAll('.room-number');

            // Prepares the form data in a structured format
            const roleRequestData = {
                user: {
                    name: nameField.value.trim(), 
                    last_name: lastNameField.value.trim(), 
                    email: emailField.value.trim(), 
                    department: departmentField.value.trim(), 
                },
                rooms: Array.from(roomNumberFields).map(field => ({
                    room_number: field.value.trim(),
                })),
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content'); // Retrieves the CSRF token

            // Sends a POST request with the form data
            fetch('/professors/users', {
                    method: 'POST', 
                    headers: {
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(roleRequestData), // Converts the form data to JSON
                })
                .then(response => {
                    if (!response.ok) {
                        // If the response is not OK, parse the error response
                        return response.json().then(errorData => {
                            throw errorData;
                        });
                    }
                    return response.json(); // Parses the successful response as JSON
                })
                .then(data => {
                    // Success: Notify the user and reset the form
                    alert('User added successfully!');
                    document.getElementById('roleRequestForm').reset(); 
                    validateRoleRequestForm(); 
                })
                .catch(error => {
                    // Handle errors
                    if (error.errors) {
                        // Specific validation errors
                        if (error.errors['user.email']) {
                            alert('The email provided is already in use.');
                        } else if (error.errors['rooms.0.room_number']) {
                            alert('One or more rooms are invalid.');
                        } else {
                            alert('Please check the form and try again.');
                        }
                    } else if (error.message) {
                        alert(error.message);
                    } else {
                        alert('An unexpected error occurred.');
                    }
                });
        }


        // Attach validation to input fields
        document.getElementById('name').addEventListener('input', validateRoleRequestForm);
        document.getElementById('lastName').addEventListener('input', validateRoleRequestForm);
        document.getElementById('email').addEventListener('input', validateRoleRequestForm);
        document.getElementById('department').addEventListener('input', validateRoleRequestForm);
        document.querySelector('.room-number').addEventListener('input', validateRoleRequestForm);

        // Attach submit functionality
        document.getElementById('submitBtn').addEventListener('click', submitRoleRequest);
    </script>
@endsection
