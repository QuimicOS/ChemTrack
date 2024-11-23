@extends('professor.templateProfessor')

@section('title', 'Role Request - ChemTrack')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<style>
    .content-area {
        margin-left: 120px; /* Aligns with sidebar width for consistency */
        padding: 1.25rem;
        margin-top: 25px; /* Aligns top spacing with other sections */
    }
    .form-label {
        font-weight: bold;
    }
    .btn-primary {
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
        <h1 class="display-5">Role Request</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Role Request) -->
    <fieldset>
    <form id="roleRequestForm" novalidate>
        <div class="row mb-5">
            <!-- Name Field -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Enter name" required>
            </div>
        
            <!-- Last Name Field -->
            <div class="col-md-6">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
            </div>
        </div>
        
        <div class="row mb-5">
            <!-- Email Field -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" required>
            </div>
        
            <!-- Department Field -->
            <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Enter department" required>
            </div>
        </div>
        
        <div class="row mb-5">
            <!-- Room Number Field with Add Button -->
            <div class="col-md-6">
                <label for="roomNumber" class="form-label">Room Number</label>
                <div id="roomNumberContainer">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control room-number" id="roomNumber" placeholder="Enter room number (e.g., B-257)" required>
                        <button type="button" class="btn btn-outline-success" onclick="addRoomNumberField()">+</button>
                    </div>
                </div>
            </div>
        
            <!-- Role Field -->
            <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" id="role" value="Teaching Assistant/Lab Technician/Student" readonly>
            </div>
        </div>
        

        <!-- Submit Request Button (Initially Disabled) -->
        <div class="text-center">
            <button type="button" class="btn btn-primary" id="submitBtn" disabled>Submit Request</button>
        </div>
    </fieldset>
    </form>
</div>

@endsection

@section('scripts')
<script>
// Regular expressions for validation
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
    const submitButton = document.getElementById('submitBtn');

    // Check if all fields are valid
    const isValid = 
        namePattern.test(nameField.value.trim()) &&
        namePattern.test(lastNameField.value.trim()) &&
        emailPattern.test(emailField.value.trim()) &&
        namePattern.test(departmentField.value.trim()) &&
        Array.from(roomNumberFields).every(field => roomNumberPattern.test(field.value.trim()));

    submitButton.disabled = !isValid; // Enable button only if all fields are valid
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
    removeButton.onclick = () => newFieldGroup.remove();

    newFieldGroup.appendChild(newInput);
    newFieldGroup.appendChild(removeButton);

    roomNumberContainer.appendChild(newFieldGroup);
    toggleSubmitButton(); // Re-validate form
}

// Submit form data to backend using POST request
function submitRoleRequest() {
    const nameField = document.getElementById('name');
    const lastNameField = document.getElementById('lastName');
    const emailField = document.getElementById('email');
    const departmentField = document.getElementById('department');
    const roomNumberFields = document.querySelectorAll('.room-number');

    // Prepare the request payload
    const roleRequestData = {
        user: {
            name: nameField.value.trim(),
            last_name: lastNameField.value.trim(),
            email: emailField.value.trim(),
            department: departmentField.value.trim(),
        },
        rooms: Array.from(roomNumberFields).map(field => ({
            room_number: field.value.trim(),
        }))
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Make a POST request to the server
    fetch('/professors/users', {
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
                    throw new Error(JSON.stringify(errorData.errors || errorData.message || 'Error creating user'));
                });
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            alert('User created successfully!');
            console.log('Response:', data);

            // Clear the form fields
            document.getElementById('roleRequestForm').reset();
            toggleSubmitButton(); // Disable the button again after submission
        })
        .catch(error => {
            console.error('Error creating user:', error);
            alert(`Failed to create user: ${error.message}`);
        });
}


// Attach event listeners to input fields to toggle the submit button state
document.getElementById('name').addEventListener('input', toggleSubmitButton);
document.getElementById('lastName').addEventListener('input', toggleSubmitButton);
document.getElementById('email').addEventListener('input', toggleSubmitButton);
document.getElementById('department').addEventListener('input', toggleSubmitButton);
document.querySelector('.room-number').addEventListener('input', toggleSubmitButton);

// Attach click event to submit button to handle form submission
document.getElementById('submitBtn').addEventListener('click', submitRoleRequest);

</script>
@endsection
