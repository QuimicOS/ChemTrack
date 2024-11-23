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
        body, html {
            background-color: #E4E4E4;
            font-size: 1rem;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
  
        /* Content and footer layout */
        .content-area {
            margin-left: 270px;
            padding: 1.25rem;
            margin-top: 70px;
            flex: 1; /* Allows content area to grow and push footer down */
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
            text-align: center;
            border-radius: 10px;
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin/homeAdmin') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin/aboutUs') }}">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin/contactUs') }}">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin/help') }}">Help</a></li>
                </ul>

                <!-- Notification Bell Icon -->
                <div class="dropdown me-3">
                  <button class="btn btn-light position-relative" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationCount">
                    </span>                    
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" id="dynamicNotificationsMenu" aria-labelledby="dropdownMenuButton">
                    <!-- Menu items will be dynamically populated here -->
                </ul>
                </div>

                <!-- Username and Sign Out -->
                <span class="navbar-text me-3"><b>{{Auth::user()->name}}</b></span>
                <a href="{{ route('auth.saml.logout') }}" class="btn btn-danger">Sign Out</a>
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
            <div class="menu-item">{{Auth::user()->name}}</div>
        </div>
        <div class="line-divider"></div>
        <div class="menu-section">
          <a href="{{ route('admin/searchLabel') }}" class="menu-button">Search Label</a>
          <a href="{{ route('admin/createLabel') }}" class="menu-button">Create Label</a>
          <a href="{{ route('admin/editLabel') }}" class="menu-button">Edit Label</a>
          <a href="{{ route('admin/invalidLabel') }}" class="menu-button">Invalidate Label</a>
          <a href="{{ route('admin/pickupRequest') }}" class="menu-button">Pickup Request</a>
          <a href="{{ route('admin/invalidPickup') }}" class="menu-button">Invalidate Pickup Request</a>
          <a href="{{ route('admin/pickupHistorial') }}" class="menu-button">Pickup Historial</a>
          <a href="{{ route('admin/manageChemical') }}" class="menu-button">Manage Chemical</a>
          <a href="{{ route('admin/roleManagement') }}" class="menu-button">Role Management</a>
          <a href="{{ route('admin/unwantedMaterialSummary') }}" class="menu-button">Unwanted Material Summary</a>
          <a href="{{ route('admin/unwantedMaterialMemorandum') }}" class="menu-button">Unwanted Material Memorandum</a>
          <a href="{{ route('admin/manageLaboratories') }}" class="menu-button">Manage Laboratories</a>
          <a href="{{ route('admin.manageQuiz.show') }}" class="menu-button">Manage Quiz</a>
        </div>
        <div class="line-divider"></div>

        <!-- Sign Out in Sidebar -->
        <div class="sign-out">
            <a href="{{ route('auth.saml.logout') }}" class="btn btn-danger">Sign Out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area container">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footerprimary">
        <p>Â© 2024 ChemTrack UPRM. All rights reserved.</p>
        <nav class="d-flex justify-content-center">
            <a href="{{ route('admin/contactUs') }}" class="text-muted mx-3">Contact Us</a>
            <a href="{{ route('admin/aboutUs') }}" class="text-muted mx-3">About Us</a>
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
            const notificationCountElement = document.getElementById('notificationCount');
            const menu = document.getElementById('dynamicNotificationsMenu');

            fetch('/notifications/types')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error fetching notification types: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear the menu
                    menu.innerHTML = '';

                    // Notification type titles
                    const notificationTitles = {
                        0: 'New Pickup Request(s)',
                        1: 'Invalidated Pickup Request(s)',
                        3: 'Label(s) Without Pickup',
                        4: 'Label(s) Overdue!',
                        5: 'New Chemical(s) Added',
                        6: 'New User Request(s)',
                        7: 'Maximum Capacity Reached',
                        8: 'P Materials Capacity Reached'
                    };

                    // Add menu items dynamically based on available types
                    data.forEach(notification => {
                        const type = notification.notification_type;
                        if (notificationTitles[type]) {
                            const listItem = document.createElement('li');
                            listItem.innerHTML = `
                                <a class="dropdown-item notification-link" href="{{ route('admin/notifications') }}" data-notification="${type}">
                                    ${notificationTitles[type]}
                                </a>
                            `;
                            menu.appendChild(listItem);
                        }
                    });

                    // If no notifications are available
                    if (data.length === 0) {
                        menu.innerHTML = '<li><span class="dropdown-item">No Notifications Available</span></li>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    menu.innerHTML = '<li><span class="dropdown-item text-danger">Error Loading Notifications</span></li>';
                });



            // Fetch unread notifications count
            fetch('/notificationUnreadCount')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error fetching notifications: ${response.status}`);
                    }
                    return response.json();
                })
                .then(count => {
                    if (count > 0) {
                        notificationCountElement.textContent = count; // Update the badge with the unread count
                        notificationCountElement.classList.remove('d-none'); // Show the badge
                    } else {
                        notificationCountElement.classList.add('d-none'); // Hide the badge if no notifications
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications count:', error);
                });
            let unreadCount = 3;
            const notificationBadge = document.getElementById('notificationCount');

            document.querySelectorAll('.notification-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (unreadCount > 0) unreadCount--;
                    if (unreadCount === 0) notificationBadge.style.display = 'none';
                    else notificationBadge.textContent = unreadCount;
                });
            });
        });
    </script>
</body>
</html>