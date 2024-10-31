@extends('professor.templateProfessor')

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
