<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icon library for bell icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
    body {
        background-color: #E4E4E4;
        font-size: 1rem;
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
      }
    
      /* Navbar styling */
      .navbar {
        padding-left: 1rem;
        padding-right: 1rem;
        height: 70px;
      }
    
      /* Sidebar Styles */
      .menu-main {
        position: fixed;
        top: 75px;
        left: 0;
        height: calc(90% - 75px);
        width: 250px;
        background: linear-gradient(180deg, #3e3d45, #202020);
        padding: 10px;
        box-sizing: border-box;
        text-align: center;
        display: flex;
        flex-direction: column;
        color: #fff;
        z-index: 1000;
        overflow-y: auto;
        margin-left: 10px;
        border-radius: 10px;
      }

      .content-area {
        margin-left: 270px;
        padding: 1.25rem;
        margin-top: 70px; /* Push content to be right below the navbar */
      }

    /* Button side bar */
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
      }
      /* Sidebar when hovering over an option */ 
      .menu-button:hover {
        background-color: rgba(255, 255, 255, 0.1);
      }
    
      .line-divider {
        border: 1px solid #fff;
        margin: 1rem 0;
        width: 100%;
      }
    
      /* Footer Styles */
      .footerprimary {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        text-align: center;
        margin-top: 20px;
        border-radius: 10px;
      }
    
      .footerprimary p {
        margin-bottom: 0;
        color: #6c757d;
      }
    
      .footerprimary nav a {
        color: #6c757d;
        margin-left: 10px;
        margin-right: 10px;
        text-decoration: none;
      }
    
      .sign-out {
        margin-top: auto;
        margin-bottom: 20px;
      }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">CHEMTRACK</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Help</a></li>
                </ul>

                <!-- Notification Bell Icon -->
                <div class="dropdown me-3">
                  <button class="btn btn-light position-relative" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
                        3
                    </span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item notification-link" href="#" data-notification="pickup">New pickup request</a></li>
                    <li><a class="dropdown-item notification-link" href="#" data-notification="lab-edit">Lab details edited</a></li>
                    <li><a class="dropdown-item notification-link" href="#" data-notification="chemical-add">Chemical added</a></li>
                  </ul>
                </div>

                <!-- Username and Sign Out -->
                <span class="navbar-text me-3"><b>juan.pablo@upr.edu</b></span>
                <a href="{{ route('home') }}" class="btn btn-outline-danger">Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="menu-main">
        <div class="logo">
            <b class="material-dashboard-2">CHEMTRACK UPRM</b>
        </div>
        <div class="line-divider"></div>
        <div class="menu-button-primary">
            <div class="menu-item">juan.pablo@upr.edu</div>
        </div>
        <div class="line-divider"></div>
        <div class="menu-section">
          <a href="#" class="menu-button">Search Label</a>
          <a href="#" class="menu-button">Create Label</a>
          <a href="#" class="menu-button">Edit Label</a>
          <a href="#" class="menu-button">Invalidate Label</a>
          <a href="#" class="menu-button">Pickup Request</a>
          <a href="#" class="menu-button">Invalidate Pickup Request</a>
          <a href="#" class="menu-button">Add Chemical</a>
        </div>
        <div class="line-divider"></div>

        <!-- Sign Out in Sidebar -->
        <div class="sign-out">
            <a href="{{ route('home') }}" class="btn btn-outline-danger">Sign Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area container">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footerprimary">
        <p>© 2024 ChemTrack UPRM. All rights reserved.</p>
        <nav class="d-flex justify-content-center">
            <a href="#" class="text-muted mx-3">Contact Us</a>
            <a href="#" class="text-muted mx-3">About Us</a>
        </nav>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    @yield('modal')
    @yield('scripts')

    <!-- JS for managing notification count -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let unreadCount = 3;  // Initial unread notification count
            const notificationBadge = document.getElementById('notificationCount');

            // Handle clicking on a notification
            document.querySelectorAll('.notification-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    // Decrement unread count
                    if (unreadCount > 0) {
                        unreadCount--;
                    }

                    // Update the badge
                    if (unreadCount === 0) {
                        notificationBadge.style.display = 'none';
                    } else {
                        notificationBadge.textContent = unreadCount;
                    }
                });
            });
        });
    </script>
</body>
</html>
