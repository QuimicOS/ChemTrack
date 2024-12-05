@extends('staff/templateStaff')

<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Notifications - ChemTrack')

@section('content')
<style>
    .notification-item {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .notification-item.unread {
        background-color: #e9ecef;
    }

    .notification-header {
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .notification-body {
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .notification-timestamp {
        font-size: 0.8rem;
        color: #888;
    }

    .notification-actions {
        margin-top: 10px;
    }
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Notifications</h1>
    <hr class="my-4">
</div>

<!-- Pickups Due Section -->
<h3 class="mt-5">Labels Due For Pickup Request</h3>
<div id="todoList">
    <!-- Read notifications will be dynamically loaded here -->
</div>

<!-- Active Notifications Section -->
<h3>Invalidated Labels and Pickup Requests</h3>
<div id="userNotificationsList">
    <!-- Active notifications will be dynamically loaded here -->
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications('/todoList', 'todoList');
    fetchNotifications('/notificationUserUnreads', 'userNotificationsList');
});

function fetchNotifications(url, containerId) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(`Fetched data for ${containerId}:`, data);

            const notifications = Object.values(data)[0];
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Clear container

            if (!Array.isArray(notifications) || notifications.length === 0) {
                container.innerHTML = '<p>No notifications found.</p>';
                return;
            }

            notifications.forEach(notification => {
                const div = document.createElement('div');
                div.className = `notification-item ${notification.status_of_notification === 0 ? 'unread' : ''}`;
                div.id = `notification_${notification.id}`;
                div.innerHTML = `
                    <p class="notification-body">${notification.message}</p>
                    <p class="notification-timestamp">Created At: ${new Date(notification.created_at).toLocaleString()}</p>
                    <div class="notification-actions">
                        <button onclick="markAsDone(${notification.id}, ${notification.label_id})" class="btn btn-success">Mark as Done</button>
                    </div>
                `;
                container.appendChild(div);
            });
        })
        .catch(error => {
            console.error(`Error fetching ${containerId}:`, error);
            const container = document.getElementById(containerId);
            container.innerHTML = `<p>Error loading notifications. Please try again later.</p>`;
        });
}

function markAsDone(notificationId, labelId) {
    console.log(`Checking pickup request for label ${labelId}...`);

    fetch(`/checkPickupRequest`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ label_id: labelId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.message === "Pickup request not found") {
                alert("Please make a pickup request for this label.");
                return;
            }

            alert("Notification marked as done successfully.");
            // Remove the notification from the To-Do list
            const item = document.getElementById(`notification_${notificationId}`);
            if (item) {
                item.remove();
            }
        })
        .catch(error => {
            console.error(`Error checking pickup request for label ${labelId}:`, error);
            alert("An error occurred. Please try again.");
        });
}

</script>
@endsection
