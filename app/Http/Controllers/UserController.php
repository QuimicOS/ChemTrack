<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
    public function store(Request $request)
    {
    
        $user = User::create($request->all());

        return response()->json($user, 201); // 201 Created
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



    // DELETE - Remove a user by ID (Delete operation)
    // public function destroy($id)
    // {
    //     $user = User::find($id);
    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     $user->delete();
    //     return response()->json(['success' => 'User deleted successfully']);
    // }

















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




















//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////// PEdido por Orlangel para views
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////








 






//     // GET para buscar por email

    public function searchByEmail(Request $request)
    {
        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }





//     //GEt wherre user certification status = true


    public function searchCertifiedUsers(Request $request)
    {
        $query = User::where('certification_status', true);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->filled('lastname')) {
            $query->where('lastname', 'like', '%' . $request->input('lastname') . '%');
        }
        if ($request->filled('certification_date')) {
            $query->whereDate('certification_date', $request->input('certification_date'));
        }
        if ($request->filled('department')) {
            $query->where('department', 'like', '%' . $request->input('department') . '%');
        }

        $users = $query->get();

        return response()->json($users);
    }







////////CONTROLLER that will create a new user from scratch with the user_status always set to "requested" when the role is professor
    public function createStaffUser(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'department' => 'required|string|max:255',
            'room_number' => 'required|exists:laboratories,room_number'
        ]);

        // Set default attributes
        $user = new User();
        $user->name = $validatedData['name'];
        $user->last_name = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->department = $validatedData['department'];
        $user->room_number = $validatedData['room_number'];
        $user->role = 'staff'; // Always "staff" for this function
        $user->certification_status = false; // Default false
        $user->user_status = 'requested'; // Default "requested"

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
            'role' => 'required|string|in:professor', 

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
