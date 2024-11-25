<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /////////////////////////////////////////////////////////////
    ////////////////////////////CRUD operationd//////////////////
    /////////////////////////////////////////////////////////////


    // GET all users (Read operation)
    public function getUserDetails()
    {
        return response()->json(User::all());
    }




    // GET a single user by ID (Read operation)
    public function getUserDetailsByID($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }





    // POST - Create a new user (Create operation)/////////////
    public function createUser(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'user.name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:user,email',
            'user.role' => 'nullable|string|max:255',
            'user.department' => 'nullable|string|max:255',
            'rooms' => 'array|nullable', // Validate room numbers as an array
            'rooms.*.room_number' => 'required|string|exists:laboratory,room_number', // Each room must exist in `laboratory`
        ]);
    
        DB::beginTransaction();
        try {
            // Create the user with default certification and user status
            $userData = $validatedData['user'];
            $user = new User();
            $user->name = $userData['name'];
            $user->last_name = $userData['last_name'];
            $user->email = $userData['email'];
            $user->role = $userData['role'] ?? null;
            $user->department = $userData['department'] ?? null;
            $user->user_status = 'Accepted'; // Default status
            $user->certification_status = 0; // Default certification status
            $user->save();
    
            // Handle room associations in the pivot table
            if (!empty($validatedData['rooms'])) {
                foreach ($validatedData['rooms'] as $room) {
                    DB::table('rooms')->insert([
                        'user_id' => $user->id,
                        'room_number' => $room['room_number'], // Room number from request
                        'lab_status' => null, // Default to null or set a specific value if needed
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'User created successfully!', 'user' => $user], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create user.', 'error' => $e->getMessage()], 500);
        }
    }
    
    






    // PUT - Update an existing user (Update operation)
    public function updateUserProfile(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate and update user data
        $validatedData = $request->validate(User::$PutRules);

        $user->update($validatedData);
        return response()->json($user);
    }







    public function deleteUserById($id)
    {
        // Find the user by ID
        $user = User::find($id);
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
    
        // Delete the user
        $user->delete();
    
        return response()->json(['message' => "User with ID {$id} deleted successfully."], 200);
    }









// /////////////////////////DESIGN////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


//Changes user role to professor 
    public function changeUserRoleProfessor(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['role' => 'Professor']);
        return response()->json($user);
        

    }


    //Changes user role to Admin
    public function changeUserRoleAdmin(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['role' => 'Adminisrator']);
        return response()->json($user);
        

    }


//Changes user role to Staff
    public function changeUserRoleStaff(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['role' => 'Staff']);
        return response()->json($user);
        

    }



    //Authenticates user
    public function authenticateUser(Request $request, $id)
    {
        // Retrieve the user by ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Validate that the request includes completion status and completion date
        $validatedData = $request->validate([
            'completion_status' => 'required|boolean',
            'completion_date' => 'required|date'
        ]);
    
        // Check if completion status is TRUE
        if ($validatedData['completion_status'] !== true) {
            return response()->json(['error' => 'User has not completed the requirements'], 400);
        }
    
        // Update user status to 'Accepted' and store completion date
        $user->update([
            'user_status' => 'Accepted',
            'completion_status' => true,
            'completion_date' => $validatedData['completion_date']
        ]);
    
        return response()->json([
            'message' => 'User authenticated successfully',
            'user' => $user
        ]);
    }
    



    // User status to DENIED
    public function invalidatesUser(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['user_status' => 'Denied']);
        return response()->json($user);

    }

    // User status to REQUESTED
    public function requestedUser(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['user_status' => 'Requested']);
        return response()->json($user);

    }





    // method to register assigned labs of a user_id
    public function getAssignedLabs($userId)
    {
        // Find the user by ID
        $user = User::find($userId);
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Get the lab_id from the user table
        $labId = $user->lab_id;
    
        // Find the laboratory based on the lab_id
        $lab = Laboratory::find($labId);
    
        // Check if the laboratory exists
        if (!$lab) {
            return response()->json(['error' => 'Laboratory not found'], 404);
        }
    
        // Return the laboratory details as a JSON response
        return response()->json($lab);
    }























 


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



//// GET para buscar por email GOOD/////////////////////////////
public function searchUserByEmail($email)
{
    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json(['error' => 'Invalid email format'], 400);
    }

    // Find the user by email with their associated room numbers
    $user = User::where('email', $email)
        ->leftJoin('rooms', 'user.id', '=', 'rooms.user_id') // Join with rooms table
        ->select(
            'user.id',
            'user.email',
            'user.role',
            'user.created_at',
            DB::raw('GROUP_CONCAT(rooms.room_number, ", ") as room_numbers') // Concatenate room numbers
        )
        ->groupBy('user.id', 'user.email', 'user.role', 'user.created_at') // Group by user fields
        ->first();

    // If user is not found, return an error response
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Return the user details with room numbers
    return response()->json($user, 200);
}






//     //GEt wherre user certification status = true


public function getCertifiedUsers()
{
    // Fetch all users with certification_status = true
    $certifiedStudents = User::where('certification_status', true)->get();

    // Check if there are any certified students
    if ($certifiedStudents->isEmpty()) {
        return response()->json(['message' => 'No certified students found'], 404);
    }

    // Return the certified students
    return response()->json(['certified_students' => $certifiedStudents], 200);
}

public function expireUserCertification($id)
{
    // Fetch the user by ID
    $user = User::find($id);

    // Check if the user exists
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Update certification status and certification date
    $user->update([
        'certification_status' => 0, // Expired
        'certification_date' => null, // Clear certification date
    ]);

    // Return a success response
    return response()->json([
        'message' => 'Certification has expired.',
        'user' => $user,
    ]);
}



public function getRequestedUsers()
{
    $requestedUsers = DB::table('user')
        ->leftJoin('rooms', 'user.id', '=', 'rooms.user_id') // Join with rooms table
        ->select(
            'user.id',
            'user.email',
            'user.role',
            'user.created_at',
            DB::raw('GROUP_CONCAT(rooms.room_number,  ", ") as room_numbers') // Concatenate room numbers
        )
        ->where('user.user_status', 'Requested')
        ->groupBy('user.id', 'user.email', 'user.role', 'user.created_at') // Group by user fields
        ->get();

    // Check if there are any requested users
    if ($requestedUsers->isEmpty()) {
        return response()->json(['message' => 'No requested users found'], 404);
    }

    // Return the data
    return response()->json(['requested_users' => $requestedUsers], 200);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////Role request that will create a new user from scratch with the user_status always set to "requested" when the role is professor



public function createStaffUser(Request $request)
{
    $validatedData = $request->validate([
        'user.name' => 'required|string|max:255',
        'user.last_name' => 'required|string|max:255',
        'user.email' => 'required|string|email|max:255|unique:user',
        'user.department' => 'required|string|max:255',
        'rooms' => 'array|nullable', // Validate room numbers as an array
        'rooms.*.room_number' => 'required|string|exists:laboratory,room_number', // Each room must exist in `laboratory`
        'rooms.*.lab_status' => 'nullable|string|max:255', // Optional lab status
    ]);

    DB::beginTransaction();
    try {
        // Create the user with default attributes
        $userData = $validatedData['user'];
        $user = User::create([
            'name' => $userData['name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'department' => $userData['department'],
            'role' => 'Staff', // Default role
            'certification_status' => false, // Default certification status
            'user_status' => 'Requested', // Default user status
        ]);

        // Handle room associations in the pivot table
        if (!empty($validatedData['rooms'])) {
            foreach ($validatedData['rooms'] as $room) {
                DB::table('rooms')->insert([
                    'user_id' => $user->id,
                    'room_number' => $room['room_number'], // Room number from request
                    'lab_status' => $room['lab_status'] ?? null, // Optional lab status
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'data' => $user], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    
    












public function roleManagementEditUser(Request $request, $id)
{
    // Validate input data
    $validatedData = $request->validate([
        'role' => 'required|string|max:255',
        'room_numbers' => 'array', // Expect an array of room numbers
        'room_numbers.*' => 'required|string|exists:laboratory,room_number', // Validate each room number
    ]);

    DB::beginTransaction();
    try {
        // Update the user role in the `users` table
        $user = User::findOrFail($id);
        $user->role = $validatedData['role'];
        $user->save();

        // Handle room numbers
        if (isset($validatedData['room_numbers'])) {
            // Fetch current room numbers for the user
            $existingRoomNumbers = DB::table('rooms')
                ->where('user_id', $user->id)
                ->pluck('room_number')
                ->toArray();

            $newRoomNumbers = $validatedData['room_numbers'];

            // Find room numbers to add
            $roomNumbersToAdd = array_diff($newRoomNumbers, $existingRoomNumbers);

            // Find room numbers to delete
            $roomNumbersToDelete = array_diff($existingRoomNumbers, $newRoomNumbers);

            // Add new room numbers
            foreach ($roomNumbersToAdd as $roomNumber) {
                DB::table('rooms')->insert([
                    'user_id' => $user->id,
                    'room_number' => $roomNumber,
                ]);
            }

            // Delete room numbers that are no longer included
            DB::table('rooms')
                ->where('user_id', $user->id)
                ->whereIn('room_number', $roomNumbersToDelete)
                ->delete();
        }

        DB::commit();

        return response()->json(['success' => 'User updated successfully!'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to update user', 'details' => $e->getMessage()], 500);
    }
}









    public function countNewMembersLast30Days()
{
    $thirtyDaysAgo = now()->subDays(30);
    $newMemberCount = User::where('created_at', '>=', $thirtyDaysAgo)->count();

    return response()->json(['new_member_count' => $newMemberCount], 200);
}







// public function validateUserRole(Request $request)
// {
//     $user = Auth::user(); // Get the authenticated user

//     // Define the allowed roles
//     $allowedRoles = ['Administrator', 'Professor', 'Staff'];

//     // Get the requested role from the request
//     $requestedRole = $request->input('role');

//     // Check if the requested role is valid and matches the user's role
//     if (!in_array($requestedRole, $allowedRoles)) {
//         return response()->json(['error' => 'Invalid role requested.'], 400);
//     }

//     if ($user->role !== $requestedRole) {
//         return response()->json(['error' => 'Unauthorized role selection.'], 403);
//     }

//     // Role is valid and matches the user's role
//     return response()->json(['success' => true, 'message' => "Role validated as {$requestedRole}."], 200);
// }

  
 
}
