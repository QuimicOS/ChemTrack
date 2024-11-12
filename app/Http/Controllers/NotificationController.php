<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NotificationController extends Controller
{
// ------------------------------------------------------------------------------------------------------
// ---------------------------------------------API METHODS----------------------------------------------
// ------------------------------------------------------------------------------------------------------

    public function getAll() // GET ALL
    {
        $notifications = Notification::all();
        return response()->json($notifications, 200);
    }

    public function get($id) // GET ID
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json($notification, 200);
    }

    public function create(Request $request) // CREATE
    {
        $validatedData = $request->validate([
            'send_to' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'status_of_notification' => 'required|integer|in:0,1',
            'notification_type' => 'required|integer',
        ]);

        $notification = Notification::create(array_merge($validatedData, ['created_at' => now()]));
        return response()->json($notification, 201);
    }

    public function update(Request $request, $id) // PUT
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $validatedData = $request->validate([
            'send_to' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'status_of_notification' => 'required|integer|in:0,1', 
            'notification_type' => 'required|integer'
        ]);

        $notification->update(array_merge($validatedData, ['created_at' => $notification->created_at]));

        return response()->json($notification, 200);
    }

    public function destroy($id) // DELETE
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully'], 200);
    }

// ------------------------------------------------------------------------------------------------------
// -------------------------------------------FRONTEND METHODS-------------------------------------------
// ------------------------------------------------------------------------------------------------------ 

    // GET ALL NOTIFICATIONS WITH STATUS UNREAD PER USER
    public function getPendingNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $user = User::where('name', $validatedData['user_name'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $notifications = Notification::where('user_id', $user->id)
            ->where('status_of_notification', 0) // 0 represents Unread
            ->orderBy('created_at', 'asc')
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No unread notifications found for the specified user.'], 404);
        }

        return response()->json(['unread_notifications' => $notifications], 200);
    }


    // CREATES A NEW NOTIFICATION
    public function triggerNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|integer', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $notificationData = [
            'created_at' => now(),
            'status_of_notification' => 0, 
            'notification_type' => $validatedData['notification_type'],
        ];

        $notificationData['send_to'] = 'Admin'; 
        $notificationData['message'] = 'Message';

        $notification = Notification::create($notificationData);

        return response()->json(['notification' => $notification], 201);
    }

    // CHANGES NOTIFICATION STATUS TO READ
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $notification = Notification::find($validatedData['notification_id']);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->update(['status_of_notification' => 1]); 

        $orderedResponse = [
            'status_of_notification' => $notification->status_of_notification == 1 ? 'Read' : 'Unread',
            'message' => $notification->message,
        ];

        return response()->json($orderedResponse, 200);
    }

}
