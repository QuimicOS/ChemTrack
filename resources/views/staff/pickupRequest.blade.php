@extends('staff/templateStaff')

<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Pickup Request - ChemTrack')

@section('content')
    <style>
        .content-area {
            margin-left: 120px;
            padding: 1.25rem;
            margin-top: 25px;
        }
        .time-select {
            margin-top: 20px;
            padding: 15px;
            border: 2px dashed #D8BFD8;
            border-radius: 10px;
        }
        .time-select select {
            width: 100%;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 5px;
        }
        .time-select .row {
            align-items: center;
            margin-bottom: 10px;
        }
        .col-auto span {
            display: block;
            width: 30px;
            text-align: center;
        }
        .form-label {
            margin-bottom: 5px;
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
            <h1 class="display-5">Pickup Request</h1>
            <hr class="my-4">
        </div>

        <fieldset>
            <div class="mb-4">
                <label for="labelID" class="form-label">LABEL ID:<span class="text-danger">*</span></label>
                <input type="text" class="form-control w-50 mx-auto" id="labelID" placeholder="Enter Label ID" required oninput="validateLabelID()">
                <div class="invalid-feedback">Please enter a valid numeric Label ID.</div>
            </div>

            <div class="time-select">
                @php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                @endphp

                @foreach($days as $day)
                    <div class="row">
                        <div class="col">
                            <label for="{{ strtolower($day) }}Start" class="form-label">{{ $day }}</label>
                        </div>
                        <div class="col">
                            <select id="{{ strtolower($day) }}Start" class="form-control">
                                <option value="-">-</option>
                                <option value="08:00 AM">8:00 AM</option>
                                <option value="09:00 AM">9:00 AM</option>
                                <option value="10:00 AM">10:00 AM</option>
                                <option value="11:00 AM">11:00 AM</option>
                                <option value="12:00 PM">12:00 PM</option>
                                <option value="1:00 PM">1:00 PM</option>
                                <option value="2:00 PM">2:00 PM</option>
                                <option value="3:00 PM">3:00 PM</option>
                            </select>
                        </div>
                        <div class="col-auto text-center">
                            <span>to</span>
                        </div>
                        <div class="col">
                            <select id="{{ strtolower($day) }}End" class="form-control">
                                <option value="-">-</option>
                                <option value="09:00 AM">9:00 AM</option>
                                <option value="10:00 AM">10:00 AM</option>
                                <option value="11:00 AM">11:00 AM</option>
                                <option value="12:00 PM">12:00 PM</option>
                                <option value="1:00 PM">1:00 PM</option>
                                <option value="2:00 PM">2:00 PM</option>
                                <option value="3:00 PM">3:00 PM</option>
                                <option value="4:00 PM">4:00 PM</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>
        </fieldset>

        <button class="btn btn-success" id="requestBtn" disabled>REQUEST</button>
    </div>

    <!-- Modal for Review and Submit -->
    <div class="modal fade" id="confirmRequestModal" tabindex="-1" aria-labelledby="confirmRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRequestModalLabel">Confirm Pickup Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Label ID:</strong> <span id="modalLabelID"></span></p>
                    <p><strong>Requested Times:</strong></p>
                    <ul id="modalTimeList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmRequest">Confirm Request</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Validate Label ID and Time Selection
        window.validateLabelID = function () {
        const labelID = document.getElementById('labelID');
        labelID.value = labelID.value.replace(/\D/g, ''); // Allow only numeric input
        validateForm(); // Ensure the form validation is called
        };


        function validateForm() {
        const labelValue = document.getElementById('labelID').value;
        const isValidLabelID = labelValue !== ''; // Valid if not empty

        let isTimeSelectionValid = true;
        let hasValidDay = false;

        const timeRegex = /^(\d{1,2}):(\d{2})\s?(AM|PM)$/; // Regex to validate time format (e.g., 8:00 AM)
        
        days.forEach(day => {
            const start = document.getElementById(day + 'Start').value;
            const end = document.getElementById(day + 'End').value;

            // Check if one dropdown is filled but the other is not
            if ((start === '-' && end !== '-') || (start !== '-' && end === '-')) {
                isTimeSelectionValid = false;
            }

            // Check if both start and end times are selected
            if (start !== '-' && end !== '-') {
                hasValidDay = true;

                // Validate time format
                if (!timeRegex.test(start) || !timeRegex.test(end)) {
                    isTimeSelectionValid = false;
                } else {
                    // Parse times to check the order
                    const [startHours, startMinutes, startPeriod] = start.match(timeRegex).slice(1);
                    const [endHours, endMinutes, endPeriod] = end.match(timeRegex).slice(1);

                    // Convert 12-hour format to 24-hour format for comparison
                    const startTimeInMinutes = 
                        (parseInt(startHours) % 12 + (startPeriod === 'PM' ? 12 : 0)) * 60 + parseInt(startMinutes);
                    const endTimeInMinutes = 
                        (parseInt(endHours) % 12 + (endPeriod === 'PM' ? 12 : 0)) * 60 + parseInt(endMinutes);

                    // If the start time is greater than or equal to the end time, the timeframe is invalid
                    if (startTimeInMinutes >= endTimeInMinutes) {
                        isTimeSelectionValid = false;
                    }
                }
            }
        });

    // Disable or enable the request button based on validation
    document.getElementById('requestBtn').disabled = !(isValidLabelID && isTimeSelectionValid && hasValidDay);
}


        document.getElementById('labelID').addEventListener('input', validateLabelID);
        days.forEach(day => {
            document.getElementById(day + 'Start').addEventListener('change', validateForm);
            document.getElementById(day + 'End').addEventListener('change', validateForm);
        });

        // Initial validation call to ensure button state is set
        validateForm();

        // Show modal and populate with selected times
        document.getElementById('requestBtn').addEventListener('click', function () {
            document.getElementById('modalLabelID').innerText = document.getElementById('labelID').value;
            const modalTimeList = document.getElementById('modalTimeList');
            modalTimeList.innerHTML = '';
        
            days.forEach(day => {
                const start = document.getElementById(day + 'Start').value;
                const end = document.getElementById(day + 'End').value;
                if (start !== '-' && end !== '-') {
                    const li = document.createElement('li');
                    li.textContent = `${day.charAt(0).toUpperCase() + day.slice(1)}: ${start} to ${end}`;
                    modalTimeList.appendChild(li);
                }
            });

            const confirmRequestModal = new bootstrap.Modal(document.getElementById('confirmRequestModal'));
            confirmRequestModal.show();
        });

       // Function to create a pickup request and post data to the server
    function createPickupRequest(labelID, timeframe) {
    console.log("Create Pickup Request Function Called");

    const requestData = {
        label_id: labelID,
        timeframe: timeframe
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/createPickupRequest`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log("Response Status:", response.status);
        return response.json().then(data => {
            if (!response.ok) {
                // If the response is not OK, throw the error message
                const errorMessage = data.errors 
                    ? Object.values(data.errors).flat().join(', ')  // Combine validation errors
                    : data.error || data.message || 'An error occurred.';
                throw new Error(errorMessage);
            }
            return data;
        });
    })
    .then(data => {
        console.log('Pickup request created successfully:', data);
        alert('Pickup request created successfully!');
        resetForm();
    })
    .catch(error => {
        console.error('Error:', error.message);
        alert(`Failed to create pickup request: ${error.message}`);
    });

}


// Function to reset the form after successful submission
function resetForm() {
    document.getElementById('labelID').value = '';
    days.forEach(day => {
        document.getElementById(day + 'Start').value = '-';
        document.getElementById(day + 'End').value = '-';
    });
    validateForm(); // Revalidate to disable the button if necessary
}

// Triggering the createPickupRequest function on confirm button click
document.getElementById('confirmRequest').addEventListener('click', function () {
    const labelID = document.getElementById('labelID').value;
    let timeframe = '';

    days.forEach(day => {
        const start = document.getElementById(day + 'Start').value;
        const end = document.getElementById(day + 'End').value;
        if (start !== '-' && end !== '-') {
            timeframe += `${day.charAt(0).toUpperCase() + day.slice(1)}: ${start} to ${end}, `;
        }
    });

    timeframe = timeframe.slice(0, -2); // Remove trailing comma
    createPickupRequest(labelID, timeframe); // Call the function with label ID and timeframe
});

    });
</script>
@endsection

