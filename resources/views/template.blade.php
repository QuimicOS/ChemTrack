<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChemTrack</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->

    <style>
        /* Set background color to E4E4E4 */
        body {
            background-color: #E4E4E4;
            font-size: 1rem;
        }

        * {
            border-radius: 10px;
        }

        /* Navbar spacing */
        .navbar {
            padding-left: 1rem;
        }

        .footerprimary {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            font-size: 1rem;
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
            max-font-size: 100px; /* Maximum font size */
            color: #fff;
            text-align: center;
        }

        .menu-main {
            position: absolute;
            top: 70px;
            bottom: 70px;
            width: 15vw;
            background: linear-gradient(180deg, #3e3d45, #202020);
            padding: 1rem;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            font-size: 1rem;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }

        .logo {
            margin-bottom: 1rem;
        }

        .line-divider {
            border: 1px solid #fff;
            margin: 1rem 0;
            width: 100%;
        }

        .dashboard-block {
            background-color: #58585E;
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1rem;
            border-radius: 10px;
        }

        .menu-button {
            width: 100%;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s;
            text-align: center;
        }

        .menu-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        html {
            font-size: 16px;
        }

        @media (min-width: 1200px) {
            html {
                font-size: 18px;
            }
        }

        @media (max-width: 768px) {
            .menu-main {
                width: 30vw;
            }

            .content-area {
                margin-left: 30vw;
            }

            .uprm-portico-banner-text {
                font-size: 6vw;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid home">

    <!-- Navbar Section -->
    <header class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <div class="d-flex align-items-center">
            <img class="home-icon me-2" alt="Home Icon" src="{{ asset('photos/home-breadcrumbs.png') }}">
            <span class="help4">Home</span>
        </div>
        <nav class="navbar navbar-expand">
            <ul class="navbar-nav">
            </ul>
        </nav>
        <div class="log-in-parent">
            <a href="{{ url('loginTemp') }}" class="btn btn-primary">LOG IN</a>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="menu-main">
        <div class="logo">
            <b class="material-dashboard-2">CHEMTRACK UPRM</b>
        </div>
        <div class="line-divider"></div>

        <div class="menu-section">
            <a href="{{ url('home') }}" class="menu-button">
                <div class="menu-item">Home</div>
            </a>
            <a href="{{ url('contactUs') }}" class="menu-button">
                <div class="menu-item">Contact us</div>
            </a>
            <a href="{{ url('aboutUs') }}" class="menu-button">
                <div class="menu-item">About Us</div>
            </a>
            <a href="{{ url('loginTemp') }}" class="btn btn-primary">Login</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-area">
        @yield('content')
    </div>

    <!-- Footer Section -->
    <footer class="footerprimary text-center text-muted">
        <p>© 2024 ChemTrack UPRM. All rights reserved.</p>
        <nav class="d-flex justify-content-center">
            <a href="{{ url('contactUs') }}" class="text-muted mx-3">Contact Us</a>
            <a href="{{ url('aboutUs') }}" class="text-muted mx-3">About Us</a>
        </nav>
    </footer>
</div>

<!-- Bootstrap 5 JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
