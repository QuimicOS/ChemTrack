@extends('admin.templateAdmin')

@section('title', 'Notifications - ChemTrack')

@section('content')
<style>
    .content-area {
        margin-left: 270px;
        padding: 1.25rem;
        margin-top: 70px;
    }

    .notification-item {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .notification-item.unread {
        background-color: #e9ecef;
    }

    .notification-item .notification-header {
        font-weight: bold;
    }

    .notification-actions {
        margin-top: 10px;
    }

    .admin-notification {
        border-left: 5px solid red; /* Indicates admin-specific notifications */
    }

</style>

<div class="text-center mb-4">
    <h1 class="display-5">Notifications</h1>
    <hr class="my-4">
</div>

<!-- Notifications List -->
<div id="notificationsList">
    <!-- Example of different types of notifications -->

    <!-- 1. Labels 5 months (all users of the lab) -->
    <div class="notification-item unread" id="notification_1">
        <div class="notification-header">Label 5 Months</div>
        <div class="notification-body">The label for Lab 203 has reached 5 months without a pickup request.</div>
        <div class="notification-details">Label ID: 12345, Created by: Maria Gomez</div>
        <div class="notification-timestamp">Oct 25, 2024, 10:15 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(1)">Mark as Read</button>
        </div>
    </div>

    <!-- 2. Labels 5 1/2 months (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_2">
        <div class="notification-header">Label 5 1/2 Months</div>
        <div class="notification-body">The label for Lab 203 has passed 5 1/2 months without a pickup request. Immediate action is needed.</div>
        <div class="notification-details">Label ID: 12345, Created by: Maria Gomez</div>
        <div class="notification-timestamp">Oct 20, 2024, 9:30 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(2)">Mark as Read</button>
        </div>
    </div>

    <!-- 3. Pickup Request (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_3">
        <div class="notification-header">Pickup Request Submitted</div>
        <div class="notification-body">A pickup request has been submitted for hazardous chemicals from Lab 203.</div>
        <div class="notification-details">Pickup ID: 54321, Submitted by: John Doe</div>
        <div class="notification-timestamp">Oct 24, 2024, 4:45 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(3)">Mark as Read</button>
        </div>
    </div>

    <!-- 4. Pickup Invalidation (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_4">
        <div class="notification-header">Pickup Invalidation</div>
        <div class="notification-body">The pickup request for hazardous materials has been invalidated for Lab 203.</div>
        <div class="notification-details">Pickup ID: 54321, Invalidated by: John Doe</div>
        <div class="notification-timestamp">Oct 23, 2024, 3:15 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(4)">Mark as Read</button>
        </div>
    </div>

    <!-- 5. New Chemical Added (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_5">
        <div class="notification-header">New Chemical Added</div>
        <div class="notification-body">A new chemical has been added to the database for Lab 203: Sodium Chloride.</div>
        <div class="notification-details">Added by: Maria Gomez</div>
        <div class="notification-timestamp">Oct 22, 2024, 11:20 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(5)">Mark as Read</button>
        </div>
    </div>

    <!-- 6. User Request (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_6">
        <div class="notification-header">User Role Request</div>
        <div class="notification-body">A user role request has been submitted by Maria Gomez for Lab 203.</div>
        <div class="notification-details">Requested by: Maria Gomez</div>
        <div class="notification-timestamp">Oct 21, 2024, 9:10 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(6)">Mark as Read</button>
        </div>
    </div>

    <!-- 7. Lab exceeds 55 gallons or 1 quart of P-listed materials (admins only) -->
    <div class="notification-item unread admin-notification" id="notification_7">
        <div class="notification-header">Lab Exceeds Limit</div>
        <div class="notification-body">Lab 203 has exceeded 55 gallons of liquid waste or holds 1 quart of P-listed hazardous materials.</div>
        <div class="notification-details">Lab Room: L-203, Building: Luchetti, Professor Investigator: Dr. Ortiz</div>
        <div class="notification-timestamp">Oct 19, 2024, 2:00 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(7)">Mark as Read</button>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    // Mark a notification as read
    function markAsRead(notificationId) {
        const notificationItem = document.getElementById('notification_' + notificationId);
        notificationItem.classList.remove('unread');
    }

    // Remove a notification from the list
    function removeNotification(notificationId) {
        const notificationItem = document.getElementById('notification_' + notificationId);
        notificationItem.remove();
    }
</script>
@endsection
