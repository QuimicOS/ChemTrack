@extends('template')

@section('title', 'Contact Us - ChemTrack')

@section('content')
    <style>
        .contact-us-table {
            width: 100%;
            margin-top: 3px;
            font-family: 'Roboto', sans-serif;
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

        .content-area {
            margin-left: 132px;
            padding: 1.30rem;
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