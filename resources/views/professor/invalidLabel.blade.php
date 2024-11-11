@extends('professor.templateProfessor')

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

    .content {
        margin-top: 40px;
    }
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Invalidate Label</h1>
    <hr class="my-4">
</div>

<!-- Form Inputs -->
<div class="mb-4">
    <label for="labelID" class="form-label">Label ID <span class="text-danger">*</span></label>
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

<div class="text-center">
    <button class="btn btn-outline-danger" id="invalidateBtn" onclick="showModal()" disabled>Invalidate</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="invalidateLabel()">Confirm Invalidate</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Validate Label ID input
    function validateLabelID() {
        const labelID = document.getElementById('labelID').value;
        const feedback = document.getElementById('labelIDFeedback');
        
        // Check if Label ID is numeric Enable/Disable Search Button
        if (/^\d+$/.test(labelID)) {
            document.getElementById('labelID').classList.remove('is-invalid');
            feedback.style.display = 'none';
        } else {
            document.getElementById('labelID').classList.add('is-invalid');
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
        if (/^\d+$/.test(labelID) && reason.length >= 4) {
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
    function invalidateLabel() {
        const labelID = document.getElementById('labelID').value;
        
        // Create JSON data
        const jsonData = JSON.stringify({
            label_id: labelID,
            label_status: "INVALID",
            message: "Label has been invalidated due to " + document.getElementById('reason').value
        });

        // Create a Blob from the JSON data and download it
        const blob = new Blob([jsonData], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `invalidate_label_${labelID}.json`;
        a.click();
        URL.revokeObjectURL(url);

        // Display success message
        alert('Label has been successfully invalidated!');

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('invalidateModal'));
        modal.hide();

        // Clear the form fields
        document.getElementById('labelID').value = '';
        document.getElementById('reason').value = '';
        validateForm();  // Recheck form validity to disable the Invalidate button
    }
</script>
@endsection
