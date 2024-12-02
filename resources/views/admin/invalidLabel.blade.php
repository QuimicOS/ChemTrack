@extends('admin/templateAdmin')

@section('title', 'Invalidate Label - ChemTrack')

@section('content')
<style>
    /* Content area */
    .content-area {
        margin-left: 260px;
        padding: 1.25rem;
        margin-top: 70px; /* Push content to be right below the navbar */
    }
    .table-container {
        margin-top: 20px;
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
    .content {
        margin-top: 40px;
    }
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Invalidate Label</h1>
    <hr class="my-4">
</div>

<!-- Form Inputs -->
<fieldset>
<div class="mb-4">
    <label for="labelID" class="form-label">LABEL ID:<span class="text-danger">*</span></label>
    <input type="text" class="form-control w-50 mx-auto" id="labelID" placeholder="Enter Label ID" required oninput="validateLabelID()">
    <div id="labelIDFeedback" class="invalid-feedback text-center">
        Please enter a valid numeric Label ID.
    </div>
</div>

<div class="mb-4">
    <label for="reason" class="form-label">Reason for Invalidation <span class="text-danger">*</span></label>
    <textarea class="form-control w-50 mx-auto" id="reason" rows="4" placeholder="Enter reason..." required oninput="validateReason()"></textarea>
    <div id="reasonFeedback" class="invalid-feedback text-center">
        The reason must be at least 4 characters long.
    </div>
</div>
</fieldset>
<div class="text-center">
    <button class="btn btn-danger" id="invalidateBtn" onclick="showModal()" disabled>Invalidate</button>
</div>

<!-- Modal for Invalidation Confirmation -->
<div class="modal fade" id="invalidateModal" tabindex="-1" aria-labelledby="invalidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invalidateModalLabel">Confirm Invalidation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Label ID:</strong> <span id="modalLabelID"></span></p>
                <p><strong>Reason:</strong> <span id="modalReason"></span></p>
                <p>Are you sure you want to invalidate this label?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="invalidateLabel()">Confirm Invalidate</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure only numeric characters in Label ID input
    function validateLabelID() {
        const labelIDInput = document.getElementById('labelID');
        const feedback = document.getElementById('labelIDFeedback');

        // Replace non-numeric characters
        labelIDInput.value = labelIDInput.value.replace(/\D/g, '');

        // Check if Label ID is valid (non-empty numeric value)
        if (labelIDInput.value) {
            labelIDInput.classList.remove('is-invalid');
            feedback.style.display = 'none';
        } else {
            labelIDInput.classList.add('is-invalid');
            feedback.style.display = 'block';
        }
        validateForm();
    }

    // Validate Reason for Invalidation input
    function validateReason() {
        const reason = document.getElementById('reason').value;
        const feedback = document.getElementById('reasonFeedback');
        
        // Check if reason length is at least 4 characters
        if (reason.length >= 4) {
            document.getElementById('reason').classList.remove('is-invalid');
            feedback.style.display = 'none';
        } else {
            document.getElementById('reason').classList.add('is-invalid');
            feedback.style.display = 'block';
        }
        validateForm();
    }

    // Enable Invalidate button only if both inputs are valid
    function validateForm() {
        const labelID = document.getElementById('labelID').value;
        const reason = document.getElementById('reason').value;
        const invalidateBtn = document.getElementById('invalidateBtn');

        // Enable the Invalidate button only if both fields are valid
        if (labelID && reason.length >= 4) {
            invalidateBtn.disabled = false;
        } else {
            invalidateBtn.disabled = true;
        }
    }

    // Show modal for invalidation confirmation
    function showModal() {
        const labelID = document.getElementById('labelID').value;
        const reason = document.getElementById('reason').value;

        // Update modal content with the entered data
        document.getElementById('modalLabelID').textContent = labelID;
        document.getElementById('modalReason').textContent = reason;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('invalidateModal'));
        modal.show();
    }

    // Invalidate label and download JSON with invalidation info
// Invalidate label and send PUT request to backend
function invalidateLabel() {
    const labelID = document.getElementById('labelID').value;
    const reason = document.getElementById('reason').value;

    // Create the JSON data to send
    const jsonData = {
        message: reason
    };

    // Send PUT request to backend
    fetch(`/invalid/${labelID}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            // 'Authorization': `Bearer ${localStorage.getItem('token')}` // Include token if using token authentication
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Label has been successfully invalidated!');

            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('invalidateModal'));
            modal.hide();
            document.getElementById('labelID').value = '';
            document.getElementById('reason').value = '';
            validateForm(); // Disable button after reset
        } else {
            alert(data.error || 'An error occurred while invalidating the label.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while invalidating the label.');
    });
}

</script>
@endsection
