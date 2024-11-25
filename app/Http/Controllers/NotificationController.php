<?php

namespace App\Http\Controllers;
//namespace App\Jobs;

use App\Models\PickupRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Label;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


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

    public function adminGetOverdueNotifications()
    {
        $notifications = Notification::where('send_to', 'Administrator')
            ->where('status_of_notification', operator: 0)
            ->where('notification_type', operator: 4)
            ->orderBy('created_at', 'asc') 
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications found.'], 404);
        }

        return response()->json(['Overdue Notifications:' => $notifications], 200);
    }

    public function adminGetUnreadNotifications()
    {
        $notifications = Notification::where('send_to', 'Administrator')
            ->where('status_of_notification', 0)
            ->whereNot('notification_type', operator: 4)
            ->orderBy('created_at', 'asc') 
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications found.'], 404);
        }

        return response()->json(['Active Notifications:' => $notifications], 200);
    }

    public function adminGetReadNotifications()
    {
        $notifications = Notification::where('send_to', 'Administrator')
            ->where('status_of_notification', 1)
            ->orderBy('created_at', 'asc') 
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications found.'], 404);
        }

        return response()->json(['Read Notifications:' => $notifications], 200);
    }


    public function getUserNotifications()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'User is not authenticated.'], 403);
        }
    
        $userRooms = DB::table('rooms')
            ->where('user_id', $user->id)
            ->where('lab_status', 'Assigned')
            ->pluck('room_number'); 
    
        if ($userRooms->isEmpty()) {
            return response()->json(['message' => 'No assigned rooms found for this user.'], 404);
        }
    
        $notifications = Notification::whereIn('send_to', $userRooms) 
            ->where('status_of_notification', 0) 
            ->where('notification_type', 1)
            ->orderBy('created_at', 'asc') 
            ->get();
    
        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications found.'], 404);
        }
    
        return response()->json(['todo_list' => $notifications], 200);
    }


    public function countUserNotifications()
    {
        $user = Auth::user();
    
        $userRooms = DB::table('rooms')
            ->where('user_id', $user->id)
            ->where('lab_status', 'Assigned')
            ->pluck('room_number');
    
        $notificationCount = Notification::whereIn('send_to', $userRooms)
            ->where('status_of_notification', 0) 
            ->whereIn('notification_type', [1, 2])
            ->count();
    
        return response()->json(['count' => $notificationCount]);
    }
    


    
    public function todoList()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'User is not authenticated.'], 403);
        }
    
        $userRooms = DB::table('rooms')
            ->where('user_id', $user->id)
            ->where('lab_status', 'Assigned')
            ->pluck('room_number'); 
    
        if ($userRooms->isEmpty()) {
            return response()->json(['message' => 'No assigned rooms found for this user.'], 404);
        }
    
        $notifications = Notification::whereIn('send_to', $userRooms) 
            ->where('status_of_notification', 0) 
            ->where('notification_type', 2)
            ->orderBy('created_at', 'asc') 
            ->get();
    
        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications found.'], 404);
        }
    
        return response()->json(['todo_list' => $notifications], 200);
    }
    

    public function unreadNotificationsCount()
    {
        $unreadCount = Notification::where('send_to', 'Administrator')
            ->where('status_of_notification', 0) 
            ->count();

        return response()->json($unreadCount);
    }
    

    public function getNotificationTypes()
    {
        $notificationTypes = Notification::where('send_to', 'Administrator')
            ->where('status_of_notification', 0) // Only unread notifications
            ->select('notification_type')
            ->distinct()
            ->get();
    
        return response()->json($notificationTypes);
    }

    





























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
            'message' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $notificationData = [
            'send_to' => 'admin',
            'status_of_notification' => 0, 
            'notification_type' => $validatedData['notification_type'],
            'message' => $validatedData['message'],
        ];
    
        $notification = Notification::create($notificationData);
    
        return response()->json(['notification' => $notification], 201);
    }
    

    // CHANGES NOTIFICATION STATUS TO READ
    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|integer',
        ]);
    
        $notification = Notification::find($request->notification_id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
    
        $notification->status_of_notification = 1; // Mark as read
        $notification->save();
    
        return response()->json(['message' => 'Notification marked as read'], 200);
    }
    
    

}
