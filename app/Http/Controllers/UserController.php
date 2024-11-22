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
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user,email',
            'role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:255|exists:laboratory,room_number',
        ]);
    
        // Create the user with default certification status
        $user = new User();
        $user->name = $validatedData['name'];
        $user->last_name = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];
        $user->department = $validatedData['department'];
        $user->user_status = 'Accepted';
        $user->certification_status = 0; // Default certification status
        $user->room_number = $validatedData['room_number'] ?? null;
        $user->save();
    
        return response()->json(['message' => 'User created successfully!', 'user' => $user], 201);
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

    // Find the user by email
    $user = User::where('email', $email)->first();

    // If user is not found, return an error response
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Return the user details
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



public function getRequestedUsers()
{
    // Fetch all users with user_status = "requested"
    $requestedUsers = User::where('user_status', 'Requested')->get();

    // Check if there are any requested users
    if ($requestedUsers->isEmpty()) {
        return response()->json(['message' => 'No requested users found'], 404);
    }

    // Return the requested users
    return response()->json(['requested_users' => $requestedUsers], 200);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////CONTROLLER that will create a new user from scratch with the user_status always set to "requested" when the role is professor
    public function createStaffUser(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'department' => 'required|string|max:255',
            'room_number' => 'required|exists:laboratory,room_number'
        ]);

        // Set default attributes
        $user = new User();
        $user->name = $validatedData['name'];
        $user->last_name = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->department = $validatedData['department'];
        $user->room_number = $validatedData['room_number'];
        $user->role = 'Staff'; // Always "staff" for this function
        $user->certification_status = false; // Default false
        $user->user_status = 'Requested'; // Default "requested"

        // Save user to the database
        $user->save();

        // Return a response
        return response()->json([
            'message' => 'Staff user created successfully.',
            'user' => $user
        ], 201);
    }















    public function roleManagementEditUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate and update user data
        $validatedData = $request->validate([
            'room_number' => 'required|string|max:255',
            'role' => 'required|string|max:255', 

        ]);

        $user->update($validatedData);
        return response()->json($user);
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
