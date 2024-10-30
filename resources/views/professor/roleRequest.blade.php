@extends('professor.templateProfessor')

@section('title', 'Role Request - ChemTrack')

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
</style>

<div class="content-area container">
    <!-- Title -->
    <div class="text-center mb-4">
        <h1 class="display-5">Role Request</h1>
        <hr class="my-4">
    </div>

    <!-- Form Section (Role Request) -->
    <form id="roleRequestForm" novalidate>
        <div class="row mb-5">
            <!-- Name Field -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Enter name" required>
                <div class="invalid-feedback">Please enter a valid name (letters only).</div>
            </div>
            <!-- Last Name Field -->
            <div class="col-md-6">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
                <div class="invalid-feedback">Please enter a valid last name (letters only).</div>
            </div>
        </div>

        <div class="row mb-5">
            <!-- Email Field -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                <div class="invalid-feedback">Please enter a valid email address (e.g., example@example.com).</div>
            </div>
            <!-- Department Field -->
            <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Enter department" required>
                <div class="invalid-feedback">Department must contain only letters.</div>
            </div>
        </div>

        <div class="row mb-5">
            <!-- Room Number Field -->
            <div class="col-md-6">
                <label for="roomNumber" class="form-label">Room Number</label>
                <input type="text" class="form-control" id="roomNumber" placeholder="Enter room number (e.g., B-257)" required>
                <div class="invalid-feedback">Room Number must be in format: 1-5 alphanumeric, hyphen, and 1-5 alphanumeric characters (e.g., FALZ-001B).</div>
            </div>
            <!-- Static Role Field -->
            <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" id="role" value="Teaching Assistant/Technician/Student" readonly>
            </div>
        </div>

        <!-- Submit Request Button (Initially Disabled) -->
        <div class="text-center">
            <button type="button" class="btn btn-primary" id="submitBtn" disabled>Submit Request</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
// Function to enable or disable submit button based on validation
function toggleSubmitButton() {
    const nameField = document.getElementById('name');
    const lastNameField = document.getElementById('lastName');
    const emailField = document.getElementById('email');
    const departmentField = document.getElementById('department');
    const roomNumberField = document.getElementById('roomNumber');
    const submitButton = document.getElementById('submitBtn');

    // Check if all fields are valid
    if (nameField.value && lastNameField.value && emailField.value && departmentField.value && roomNumberField.value) {
        submitButton.disabled = false;  // Enable button if all fields have values
    } else {
        submitButton.disabled = true;   // Disable button if any field is empty
    }
}

// Function to validate and download as JSON
function validateAndDownloadJSON() {
    const nameField = document.getElementById('name');
    const lastNameField = document.getElementById('lastName');
    const emailField = document.getElementById('email');
    const departmentField = document.getElementById('department');
    const roomNumberField = document.getElementById('roomNumber');

    let isValid = true;

    // Basic name validation (letters only, no numbers or special characters)
    const namePattern = /^[a-zA-Z\s]+$/;
    if (!namePattern.test(nameField.value.trim())) {
        nameField.classList.add('is-invalid');
        isValid = false;
    } else {
        nameField.classList.remove('is-invalid');
    }

    if (!namePattern.test(lastNameField.value.trim())) {
        lastNameField.classList.add('is-invalid');
        isValid = false;
    } else {
        lastNameField.classList.remove('is-invalid');
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailField.value.trim())) {
        emailField.classList.add('is-invalid');
        isValid = false;
    } else {
        emailField.classList.remove('is-invalid');
    }

    // Department validation (letters only)
    if (!namePattern.test(departmentField.value.trim())) {
        departmentField.classList.add('is-invalid');
        isValid = false;
    } else {
        departmentField.classList.remove('is-invalid');
    }

    // Room number validation (1-5 alphanumeric, hyphen, and 1-5 alphanumeric characters)
    const roomNumberPattern = /^[a-zA-Z0-9]{1,5}-[a-zA-Z0-9]{1,5}$/;
    if (!roomNumberPattern.test(roomNumberField.value.trim())) {
        roomNumberField.classList.add('is-invalid');
        isValid = false;
    } else {
        roomNumberField.classList.remove('is-invalid');
    }

    // If form is valid, generate and download the JSON
    if (isValid) {
        const roleRequestData = {
            name: nameField.value.trim(),
            lastName: lastNameField.value.trim(),
            email: emailField.value.trim(),
            department: departmentField.value.trim(),
            roomNumber: roomNumberField.value.trim(),
            role: "Teaching Assistant/Technician/Student",  // Static role
        };

        // Convert the JSON object to string
        const jsonString = JSON.stringify(roleRequestData, null, 4);

        // Create a blob from the JSON string and trigger download
        const blob = new Blob([jsonString], { type: 'application/json' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'roleRequestData.json';
        a.click();

        // Clean up URL.createObjectURL
        URL.revokeObjectURL(url);

        // Clear the form fields
        document.getElementById('roleRequestForm').reset();
        toggleSubmitButton();  // Disable the button again after submission
    }
}

// Attach event listeners to input fields to toggle the submit button state
document.getElementById('name').addEventListener('input', toggleSubmitButton);
document.getElementById('lastName').addEventListener('input', toggleSubmitButton);
document.getElementById('email').addEventListener('input', toggleSubmitButton);
document.getElementById('department').addEventListener('input', toggleSubmitButton);
document.getElementById('roomNumber').addEventListener('input', toggleSubmitButton);
</script>
@endsection
