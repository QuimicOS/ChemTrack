@extends('staff/templateStaff')

@section('title', 'Pickup Request - ChemTrack')

@section('content')
    <style>
        .content-area {
            margin-left: 120px;
            padding: 1.25rem;
            margin-top: 25px; /* Push content to be right below the navbar */
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
            margin-bottom: 10px; /* Reduce space between rows */
        }

        .col-auto span {
            display: block;
            width: 30px; /* Shrink the width */
            text-align: center;
        }

        .form-label {
            margin-bottom: 5px; /* Reduce the label spacing */
        }

        .request-btn {
            margin-top: 20px;
        }

        .request-btn button {
            background-color: #1E90FF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .request-btn button:hover {
            background-color: #4682B4;
        }
    </style>

    <!-- Main Content with Time Selection -->
    <div class="content-area container">
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="display-5">Pickup Request</h1>
            <hr class="my-4">
        </div>

        <!-- Label ID Input -->
        <div class="mb-4">
            <label for="labelID" class="form-label">LABEL ID:</label>
            <input type="text" class="form-control w-50 mx-auto" id="labelID" placeholder="Enter Label ID" required>
            <div class="invalid-feedback">Please enter a valid numeric Label ID.</div>
        </div>

        <!-- Time Selection Section -->
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

        <!-- Request Button -->
        <div class="request-btn">
            <button class="btn btn-outline-primary" id="requestBtn" disabled>REQUEST</button>
        </div>
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

    <!-- Bootstrap JS and Popper.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <!-- JavaScript for Pickup Request Validation -->
    <script>
        const labelID = document.getElementById('labelID');
        const requestBtn = document.getElementById('requestBtn');
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        
        function validateForm() {
            const labelValue = labelID.value.trim();
            const isValidLabelID = /^\d+$/.test(labelValue);
            labelID.classList.toggle('is-invalid', !isValidLabelID);
            
            let isTimeSelectionValid = true;
            let hasValidDay = false;
            
            days.forEach(day => {
                const start = document.getElementById(day + 'Start').value;
                const end = document.getElementById(day + 'End').value;
                if ((start === '-' && end !== '-') || (start !== '-' && end === '-')) {
                    isTimeSelectionValid = false;
                } else if (start !== '-' && end !== '-') {
                    hasValidDay = true;
                }
            });
            
            requestBtn.disabled = !(isValidLabelID && isTimeSelectionValid && hasValidDay);
        }
        
        labelID.addEventListener('input', validateForm);
        days.forEach(day => {
            document.getElementById(day + 'Start').addEventListener('change', validateForm);
            document.getElementById(day + 'End').addEventListener('change', validateForm);
        });
        
        function downloadJSONFile() {
            let timeframe = '';
            
            days.forEach(day => {
                const start = document.getElementById(day + 'Start').value;
                const end = document.getElementById(day + 'End').value;
                if (start !== '-' && end !== '-') {
                    // Concatenate day and hours in the desired format
                    timeframe += `${day.charAt(0).toUpperCase() + day.slice(1)}: ${start} to ${end}, `;
                }
            });
    
            // Remove trailing comma and space
            timeframe = timeframe.slice(0, -2);
    
            const data = {
                labelID: labelID.value,
                timeframe: timeframe
            };
            
            const jsonContent = JSON.stringify(data, null, 2);
            const blob = new Blob([jsonContent], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
        
            const a = document.createElement('a');
            a.href = url;
            a.download = `pickup_request_${labelID.value}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        
            URL.revokeObjectURL(url);
        }
        
        requestBtn.addEventListener('click', function () {
            document.getElementById('modalLabelID').innerText = labelID.value;
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
        
        document.getElementById('confirmRequest').addEventListener('click', function () {
            downloadJSONFile();
            
            alert('Pickup Request JSON file has been downloaded.');
            const confirmRequestModal = bootstrap.Modal.getInstance(document.getElementById('confirmRequestModal'));
            confirmRequestModal.hide();
        
            labelID.value = '';
            days.forEach(day => {
                document.getElementById(day + 'Start').value = '-';
                document.getElementById(day + 'End').value = '-';
            });
            validateForm();
        });
    </script>
           
@endsection
