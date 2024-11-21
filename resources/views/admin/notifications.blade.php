@extends('admin.templateAdmin')

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

<!-- Overdue Notifications Section -->
<h3 class="mt-5">Overdue Notifications</h3>
<div id="overdueNotificationsList">
    <!-- Overdue notifications will be dynamically loaded here -->
</div>

<!-- Active Notifications Section -->
<h3>Active Notifications</h3>
<div id="activeNotificationsList">
    <!-- Active notifications will be dynamically loaded here -->
</div>

<!-- Read Notifications Section -->
<h3 class="mt-5">Read Notifications</h3>
<div id="readNotificationsList">
    <!-- Read notifications will be dynamically loaded here -->
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications('/notificationAdminActives', 'activeNotificationsList', true);
    fetchNotifications('/notificationAdminRead', 'readNotificationsList', false);
    fetchNotifications('/notificationAdminOverdues', 'overdueNotificationsList', false);
});

// Map notification types to their respective titles
const notificationTitles = {
    0: "New Pickup Request",
    1: "Invalidated Pickup Request",
    2: "Label Due For Pickup (5-Month Warning)",
    3: "Label Without Pickup Request (5.5-Month Warning)",
    4: "Label Expired (6-Month Warning)",
    5: "New Chemical",
    6: "User Role Requested",
    7: "Maximum Capacity Reached (55 Gallons)",
    8: "P Material Capacity Reached (1 Quart)"
};

function fetchNotifications(url, containerId, allowMarkAsRead) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(`Fetched data for ${containerId}:`, data);

            // Extract notifications array
            const notifications = Object.values(data)[0];
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Clear container

            if (!Array.isArray(notifications) || notifications.length === 0) {
                container.innerHTML = '<p>No notification found.</p>';
                return;
            }

            notifications.forEach(notification => {
                const div = document.createElement('div');
                div.className = `notification-item ${notification.status_of_notification === 0 ? 'unread' : ''}`;
                div.id = `notification_${notification.id}`; // Assign ID for the notification block
                div.innerHTML = `
                    <p class="notification-header">${notificationTitles[notification.notification_type] || "Unknown Type"}</p>
                    <p class="notification-body">${notification.message}</p>
                    <p class="notification-timestamp">Created At: ${new Date(notification.created_at).toLocaleString()}</p>
                    ${
                        allowMarkAsRead
                            ? `<div class="notification-actions">
                                   <button onclick="markAsRead(${notification.id})" class="btn btn-success">Mark as Read</button>
                               </div>`
                            : ''
                    }
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

function markAsRead(notificationId) {
    console.log(`Marking notification ${notificationId} as read...`);

    fetch('/notificationRead', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Backend error:', err);
                    throw new Error(err.message || 'Failed to mark as read.');
                });
            }
            return response.json();
        })
        .then(() => {
            console.log(`Notification ${notificationId} marked as read.`);
            // Move notification to "Read Notifications"
            const item = document.getElementById(`notification_${notificationId}`);
            if (item) {
                item.classList.remove('unread');
                item.querySelector('.notification-actions').remove(); // Remove button
                document.getElementById('readNotificationsList').appendChild(item);
            }
        })
        .catch(error => {
            console.error(`Error marking notification ${notificationId} as read:`, error);
            alert(`Failed to mark notification as read. Try again.`);
        });
}
</script>
@endsection
