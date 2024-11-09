@extends('admin/templateAdmin')

@section('title', 'Search Label - ChemTrack')

@section('content')
  <style>
    .content-area { 
        margin-left: 260px;
        padding: 1.25rem;
        margin-top: 70px; /* Push content to be right below the navbar */
    }

    /* Hide the form fields initially */
    .form-section, .table-container {
        display: none;
    }

    /* Red border for invalid input */
    .is-invalid {
        border-color: #dc3545;
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

    /* Styling to keep the label input and button compact */
    .search-container {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-start; /* Align items to the start */
    }
    .search-container input {
        min-width: 300px; /* Set a constant width for the input */
        max-width: 300px; /* Prevent it from expanding */
    }
    .search-container button {
        width: 100px; /* Set width for the button */
        height: 38px; /* Ensures the button aligns with input height */
    }
    .search-container label {
        white-space: nowrap; /* Prevent line break in label */
    }
  </style>

  <div class="text-center mb-4">
    <h1 class="display-5">Search Label</h1>
    <hr class="my-4">
  </div>

  <!-- Label ID Searchbar and Search Button -->
  <div class="search-container mb-5">
    <label for="labelID" class="form-label">Label ID <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="labelID" placeholder="Enter Label ID" required>
    <button id="searchButton" class="btn btn-primary" disabled>Search</button>
    <div class="invalid-feedback" style="width: 100%;">Please enter a valid numeric label ID.</div>
  </div>

  <!-- Form Fields Section (Initially Hidden) -->
  <div class="form-section">
    <!-- Block 1: Basic Information -->
    <fieldset>
      <legend>Basic Information</legend>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="createdBy" class="form-label">Created by</label>
            <input type="text" class="form-control" id="createdBy" readonly>
          </div>
          <div class="mb-3">
            <label for="dateCreated" class="form-label">Date Created</label>
            <input type="text" class="form-control" id="dateCreated" readonly>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="status" class="form-label">Label Status</label>
            <input type="text" class="form-control" id="status" readonly>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message (if any)</label>
            <input type="text" class="form-control" id="message" readonly>
          </div>
        </div>
      </div>
    </fieldset>

    <!-- Block 2: Location and Lab Details -->
    <fieldset>
      <legend>Location and Lab Details</legend>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" readonly>
          </div>
          <div class="mb-3">
            <label for="building" class="form-label">Building</label>
            <input type="text" class="form-control" id="building" readonly>
          </div>
          <div class="mb-3">
            <label for="roomNumber" class="form-label">Room Number</label>
            <input type="text" class="form-control" id="roomNumber" readonly>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="labName" class="form-label">Laboratory Name</label>
            <input type="text" class="form-control" id="labName" readonly>
          </div>
          <div class="mb-3">
            <label for="principalInvestigator" class="form-label">Principal Investigator</label>
            <input type="text" class="form-control" id="principalInvestigator" readonly>
          </div>
        </div>
      </div>
    </fieldset>

    <!-- Block 3: Quantity and Container Capacity -->
    <fieldset>
      <legend>Quantity and Container Capacity</legend>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="quantity" class="form-label">Added Quantity</label>
            <input type="text" class="form-control" id="quantity" readonly>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="containerCapacity" class="form-label">Container Capacity</label>
            <input type="text" class="form-control" id="containerCapacity" readonly>
          </div>
        </div>
      </div>
    </fieldset>
  </div>

  <!-- Table Section (Initially Hidden) -->
  <div class="table-container">
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th scope="col">Chemical Name</th>
          <th scope="col">CAS Number</th>
          <th scope="col">Percentage</th>
        </tr>
      </thead>
      <tbody>
        <!-- Table rows will be dynamically added here -->
      </tbody>
    </table>
  </div>

  <script>
    // Only allow numeric input in Label ID field
    document.getElementById('labelID').addEventListener('keydown', function (event) {
      const allowedKeys = ["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"];
      const isNumeric = /^[0-9]$/.test(event.key);

      if (!isNumeric && !allowedKeys.includes(event.key)) {
        event.preventDefault();
      }
    });

    // Enable or disable the search button based on Label ID input
    document.getElementById('labelID').addEventListener('input', function () {
      const labelID = document.getElementById('labelID').value;

      // Check if the input is numeric only
      const isNumeric = /^\d+$/.test(labelID);
      
      // Disable search button if the input is empty or contains non-numeric characters
      document.getElementById('searchButton').disabled = !isNumeric;

      // Toggle invalid class based on input validity
      if (!isNumeric) {
        document.getElementById('labelID').classList.add('is-invalid');
      } else {
        document.getElementById('labelID').classList.remove('is-invalid');
      }
    });

    // Handle search button click to load and display label data
    document.getElementById('searchButton').addEventListener('click', function () {
      const labelID = document.getElementById('labelID').value;

      // Define a mapping between label IDs and JSON files
      const jsonFileMapping = {
        '12345': '/json/labelData1.json',
        '67890': '/json/labelData2.json',
        '11223': '/json/labelData3.json'
      };

      // Check if the entered label ID corresponds to a JSON file
      if (jsonFileMapping[labelID]) {
        // Fetch the appropriate JSON file based on the label ID
        fetch(jsonFileMapping[labelID])
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json(); // Ensure the response is parsed as JSON
          })
          .then(data => {
            // Populate form fields with the data from JSON
            document.getElementById('createdBy').value = data.created_by;
            document.getElementById('department').value = data.department;
            document.getElementById('building').value = data.building;
            document.getElementById('roomNumber').value = data.room_number;
            document.getElementById('labName').value = data.lab_name;
            document.getElementById('dateCreated').value = data.date_created;
            document.getElementById('principalInvestigator').value = data.principal_investigator;
            document.getElementById('quantity').value = data.quantity + " " + data.units;
            document.getElementById('status').value = data.status;
            document.getElementById('message').value = data.message;

            // Show the form and table sections
            document.querySelector('.form-section').style.display = 'block';
            document.querySelector('.table-container').style.display = 'block';

            // Clear the table first
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '';

            // Populate the table with chemicals from JSON data
            data.chemicals.forEach(chemical => {
              const row = `
                <tr>
                  <td>${chemical.chemical_name}</td>
                  <td>${chemical.cas_number}</td>
                  <td>${chemical.percentage}%</td>
                </tr>
              `;
              tableBody.innerHTML += row;
            });
          })
          .catch(error => {
            console.error('Error fetching JSON:', error);
            alert('An error occurred while fetching the data.'); // Alert user of any error
          });
      } else {
        alert('Label not found!'); // Alert if label ID is not found
      }
    });
  </script>
@endsection
