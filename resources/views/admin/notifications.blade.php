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
        margin-bottom: 20px; /* Increased margin to add more space between notifications */
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
</style>

<div class="text-center mb-4">
    <h1 class="display-5">Notifications</h1>
    <hr class="my-4">
</div>

<!-- Active Notifications Section -->
<h3>Active Notifications</h3>
<div id="activeNotificationsList">
    <!-- Sorted by date from newest to oldest -->

    <!-- 1. Label 5 Months -->
    <div class="notification-item unread" id="notification_1">
        <div class="notification-header">Label 5 Months - Lab 203</div>
        <div class="notification-body">Maria Gomez has notified that the label for Sodium Chloride in Lab 203 has reached 5 months without a pickup request.</div>
        <div class="notification-details">Label ID: 12345, Room: 203, Chemical: Sodium Chloride</div>
        <div class="notification-timestamp">Oct 25, 2024, 10:15 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(1)">Mark as Read</button>
        </div>
    </div>

    <!-- 2. Pickup Request Submitted -->
    <div class="notification-item unread" id="notification_3">
        <div class="notification-header">Pickup Request - Lab 305</div>
        <div class="notification-body">John Doe has submitted a pickup request for hazardous chemicals in Lab 305, including Hydrochloric Acid.</div>
        <div class="notification-details">Pickup ID: 67890, Room: 305, Chemical: Hydrochloric Acid, Pickup Timeframe: Mon-Fri, 8am to 4pm</div>
        <div class="notification-timestamp">Oct 24, 2024, 4:45 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(3)">Mark as Read</button>
        </div>
    </div>

    <!-- 3. Pickup Invalidation -->
    <div class="notification-item unread" id="notification_4">
        <div class="notification-header">Pickup Invalidation - Lab 203</div>
        <div class="notification-body">John Doe has invalidated the previous pickup request for Methanol in Lab 203.</div>
        <div class="notification-details">Pickup ID: 54321, Room: 203, Chemical: Methanol</div>
        <div class="notification-timestamp">Oct 23, 2024, 3:15 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(4)">Mark as Read</button>
        </div>
    </div>

    <!-- 4. New Chemical Added -->
    <div class="notification-item unread" id="notification_5">
        <div class="notification-header">New Chemical Added - Lab 203</div>
        <div class="notification-body">Maria Gomez has added a new chemical, Sodium Chloride, to the database for Lab 203.</div>
        <div class="notification-details">Room: 203, Chemical: Sodium Chloride</div>
        <div class="notification-timestamp">Oct 22, 2024, 11:20 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(5)">Mark as Read</button>
        </div>
    </div>

    <!-- 5. Label 5 1/2 Months -->
    <div class="notification-item unread" id="notification_2">
        <div class="notification-header">Label 5 1/2 Months - Lab 203</div>
        <div class="notification-body">Admin: Immediate action needed. Maria Gomez reports the label for Acetone in Lab 203 has passed 5 1/2 months without pickup.</div>
        <div class="notification-details">Label ID: 54321, Room: 203, Chemical: Acetone</div>
        <div class="notification-timestamp">Oct 20, 2024, 9:30 AM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(2)">Mark as Read</button>
        </div>
    </div>

    <!-- 6. Lab Exceeds Limit -->
    <div class="notification-item unread" id="notification_6">
        <div class="notification-header">Lab Exceeds Limit - Lab 203</div>
        <div class="notification-body">Maria Gomez reports Lab 203 exceeds 55 gallons of liquid waste or 1 quart of P-listed materials.</div>
        <div class="notification-details">Room: 203, Building: Luchetti, Chemical: P-listed</div>
        <div class="notification-timestamp">Oct 19, 2024, 2:00 PM</div>
        <div class="notification-actions">
            <button class="btn btn-success mark-as-read" onclick="markAsRead(6)">Mark as Read</button>
        </div>
    </div>
</div>

<!-- Read Notifications Section -->
<h3 class="mt-5">Read Notifications</h3>
<div id="readNotificationsList">
    <!-- Read notifications will be appended here after marking as read -->
</div>

@endsection

@section('scripts')
<script>
    // Mark a notification as read
    function markAsRead(notificationId) {
        const notificationItem = document.getElementById('notification_' + notificationId);
        notificationItem.classList.remove('unread');
        const markAsReadButton = notificationItem.querySelector('.mark-as-read');
        
        if (markAsReadButton) {
            markAsReadButton.remove(); // Remove the "Mark as Read" button
        }

        // Move notification to "Read Notifications" section
        document.getElementById('readNotificationsList').appendChild(notificationItem);
    }
</script>
@endsection
