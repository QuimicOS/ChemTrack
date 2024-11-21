@extends('template')

@section('title', 'ACCESS DENIED!!!')

@section('content')
<style>
    .content-area {
        margin-left: 130px;
        padding: 1.30rem;
    }

    .card-container {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        width: 70%;
        padding: 40px;
        background-color: #f8f9fa;
        border-radius: 15px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .contact-us-table {
        width: 100%;
        margin-top: 20px;
        font-family: 'Roboto', sans-serif;
        background-color: #f5f5f5;
        border-radius: 10px;
        border-collapse: collapse;
    }

    .contact-us-table th,
    .contact-us-table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .admins {
        font-size: 1.5rem;
        text-align: center;
        margin: 20px 0;
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

<!-- Main Content -->
<div class="card-container">
    <div class="card">
        <div class="text-center mb-4">
            <h1 class="display-5 text-danger d-flex align-items-center justify-content-center">
                <!-- Smile Emoji GIF on the left -->
                <img src="{{ asset('photos/clipart-smile-smile-gif-2-1372497514.gif') }}" 
                     alt="Smile Emoji" 
                     style="width: 50px; height: 50px; margin-right: 10px;">
                
                <!-- Main Text -->
                <b>ACCESS DENIED!!!</b>
                
                <!-- Access Denied GIF on the right -->
                <img src="{{ asset('photos/clipart-smile-smile-gif-2-1372497514.gif') }}" 
                     alt="Access Denied GIF" 
                     style="width: 50px; height: 50px; margin-left: 10px;">
            </h1>
            <h2 class="text-primary">Why are you here? What are you trying to do? Hmmm...</h2>
            <hr class="my-4">
        </div>
        
        

        <div class="notice-content mb-4" style="line-height: 1.7;">
            <p class="lead">
                Your access to the ChemTrack web app has been denied! This is because you are not a registered user.
            </p>
            <p class="lead">
                Please contact the Oficina de Salud, Seguridad Ocupacional y Ambiental (OSSOA) for more details or assistance.
            </p>
        </div>

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

        <a href="https://www.uprm.edu/saludseguridad/" class="more-info-button">CLICK HERE FOR MORE INFORMATION (OSSOA PAGE)</a>
    </div>
</div>

@endsection
