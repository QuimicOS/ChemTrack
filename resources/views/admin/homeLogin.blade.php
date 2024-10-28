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
      body {
        background-color: #E4E4E4;
        font-size: 1rem;
      }

      * {
        border-radius: 10px;
      }

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

      .uprm-portico-banner {
        position: relative;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        padding-top: 1rem;
        max-width: 100%;
      }

      .uprm-portico-banner img {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        border-radius: 10px;
      }

      .uprm-portico-banner-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-family: 'Roboto', sans-serif;
        font-size: 5vw;
        color: #fff;
        text-align: center;
      }

      .hero-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1.25rem;
        background-color: #f0f0f0;
        border-radius: 10px;
        padding: 2rem;
      }

      .hero-left {
        flex: 1;
        text-align: center;
      }

      .hero-left img {
        margin: 0 auto 1rem auto;
        display: block;
      }

      .hero-left .btn {
        margin-top: 1rem;
      }

      .hero-right {
        flex: 1;
        text-align: center;
      }

      .gray-box {
        background-color: #f0f0f0;
        padding: 1.25rem;
        border-radius: 10px;
        text-align: center;
        font-size: 1rem;
      }

      .ossoa-image {
        max-width: 100%;
        border-radius: 10px;
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

      .content-area {
        margin-left: 17vw;
        padding: 1.25rem;
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
          <img class="home-icon me-2" alt="Home Icon" src="{{ asset('photo/home-breadcrumbs.png') }}">
          <span class="help4">Home</span>
        </div>
        <nav class="navbar navbar-expand">
          <ul class="navbar-nav">
          </ul>
        </nav>
        <div class="log-in-parent">
          <button class="btn btn-primary">LOG IN</button>
        </div>
      </header>

      <!-- Sidebar -->
      <div class="menu-main">
          <div class="logo">
              <b class="material-dashboard-2">CHEMTRACK UPRM</b>
          </div>

          <div class="line-divider"></div>

          <div class="dashboard-block">
              Dashboard
          </div>

          <div class="menu-section">
              <a href="#" class="menu-button">
                  <div class="menu-item">Home</div>
              </a>
              <a href="{{ url('/contact') }}" class="menu-button">
                  <div class="menu-item">Contact us</div>
              </a>
              <a href="{{ url('/about') }}" class="menu-button">
                  <div class="menu-item">About Us</div>
              </a>
              <button class="btn btn-outline-light mt-4">Login</button>
          </div>
      </div>

      <!-- Main Content Area -->
      <div class="content-area">
        <div class="uprm-portico-banner position-relative">
          <img src="{{asset('photos/UPRM-portico-banner.png')}}" class="uprm-portico-banner-1-icon img-fluid" alt="UPRM Banner">
          <div class="uprm-portico-banner-text">CHEMTRACK</div>
        </div>

        <div class="hero-container">
          <div class="hero-left">
            <img class="logochemtrack-transparent-1-icon img-fluid mb-3" alt="ChemTrack Logo" src="{{ asset('photo/Logo ChemTrack.jpg') }}">
            <div class="chemtrack-its-a">
              ChemTrack is a webApp designed for tracking, identifying, notifying about orders or pickups, and reporting on unwanted materials at the University of Puerto Rico, Mayagüez Campus.
            </div>
            <button class="btn btn-primary mt-3">See More</button>
          </div>

          <div class="hero-right">
            <img class="ossoa-image img-fluid" src="{{ asset('photo/ossoa logo.png') }}" alt="OSSOA Logo">
          </div>
        </div>
      </div>

      <!-- Footer Section -->
      <footer class="footerprimary text-center text-muted">
        <p>© 2024 ChemTrack UPRM. All rights reserved.</p>
        <nav class="d-flex justify-content-center">
          <a href="{{ url('/contact') }}" class="text-muted mx-3">Contact Us</a>
          <a href="{{ url('/about') }}" class="text-muted mx-3">About Us</a>
        </nav>
      </footer>
    </div>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  </body>
</html>
