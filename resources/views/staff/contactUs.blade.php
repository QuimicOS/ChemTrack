@extends('staff/templateStaff')

@section('title', 'Contact Us - ChemTrack')

@section('content')
    <style>
        .content-area {
            margin-left: 150px;
            padding: 1.25rem;
            margin-top: 20px; /* Push content to be right below the navbar */
        }

        /* UPRM Portico Banner */
        .uprm-portico-banner {
            position: relative;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            padding-top: 1rem;
            max-width: 100%;
            text-align: center;
        }

        .uprm-portico-banner img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Dynamic font size for the banner text */
        .uprm-portico-banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Roboto', sans-serif;
            font-size: 5vw; /* Dynamic scaling based on viewport width */
            min-font-size: 64px; /* Minimum font size */
            max-font-size: 64px; /* Maximum font size */
            color: #fff;
            text-align: center;
        }

        /* Contact Us Table */
        .contact-us-table {
            width: 100%;
            margin-top: 3px;
            background-color: #f5f5f5;
            border-radius: 10px;
        }

        .contact-us-table th, .contact-us-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .admins {
            font-size: 1.5rem;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .more-info-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            text-transform: uppercase;
            text-decoration: none;
        }

        .more-info-button:hover {
            background-color: #0056b3;
        }
    </style>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- UPRM Banner with Contact Us Text inside -->
        <div class="uprm-portico-banner">
            <img class="uprm-portico-banner-1-icon img-fluid" alt="UPRM Banner" src="{{ asset('photos/UPRM-portico-banner.png') }}">
            <div class="uprm-portico-banner-text">CONTACT US</div>
        </div>

        <!-- Contact Us Admins Section -->
        <div class="admins">Admins</div>
        <table class="contact-us-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Extension</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Santiago, Damaris - Directora Interina</td>
                    <td>damaris.santiago1@upr.edu</td>
                    <td>3506</td>
                </tr>
                <tr>
                    <td>Ayala, Luis</td>
                    <td>luis.ayala4@upr.edu</td>
                    <td>3506</td>
                </tr>
                <tr>
                    <td>Lozada, William</td>
                    <td>william.lozada@upr.edu</td>
                    <td>3506</td>
                </tr>
            </tbody>
        </table>

        <!-- More Information Button -->
        <a href="https://www.uprm.edu/saludseguridad/" class="more-info-button">CLICK HERE FOR MORE INFORMATION (OSSOA PAGE)</a>
    </div>
@endsection
