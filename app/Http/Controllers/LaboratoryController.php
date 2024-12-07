<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaboratoryController extends Controller
{
    /////////////////////////////////////////////////////////////
    ////////////////////// CRUD OPERATIONS //////////////////////
    /////////////////////////////////////////////////////////////


        // GET all laboratories
        // public function getAllLabs()
        // {
        //     $labs = Laboratory::all();
        //     return response()->json($labs);
        // }


        public function index()
        {
            // Fetch all records from the 'laboratory' table
            $laboratories = DB::table('laboratory')->get();
    
            // Pass the data to a view
            return view('admin.laboratories.index', compact('laboratories'));
        }



    
        // GET a single laboratory by ID 
        public function getLabDetails($id)
        {
            $lab = Laboratory::find($id);
            if (!$lab) {
                return response()->json(['error' => 'Lab not found'], 404);
            }
    
            return response()->json($lab);
        }
    
        public function getAllLabs()
        {
            // Retrieve the authenticated user
            $user = Auth::user();
        
            // Define the query for laboratories
            $labsQuery = Laboratory::select('department', 'building_name', 'room_number', 'lab_name', 'professor_investigator')
                ->where('lab_status', 'Assigned'); // Only get active labs
        
            // If the user is not an administrator, restrict results to their assigned rooms
            if ($user->role !== 'Administrator') {
                $labsQuery->whereIn('room_number', function ($query) use ($user) {
                    $query->select('room_number')
                        ->from('rooms')
                        ->where('user_id', $user->id);
                });
            }
        
            // Execute the query
            $labs = $labsQuery->get();
        
            // Return the response as JSON
            return response()->json($labs, 200);
        }

    
        // Fetch laboratory by room number to autofill lab name and investigator
        public function getLabByRoomNumber($room_number)
        {
            $lab = Laboratory::where('room_number', $room_number)->first();
    
            if ($lab) {
                return response()->json($lab);
            } else {
                return response()->json(['error' => 'Laboratory not found'], 404);
            }
        }








        // POST - Add a new laboratory (Create operation)
        public function addLab(Request $request)
        {
            // Validate incoming request data
            $validatedData = $request->validate(Laboratory::$AdminPostRulesForLab);
    
            $lab = Laboratory::create($validatedData);
            return response()->json($lab, 201); // 201 Created
        }















    
        // PUT - Update an existing laboratory (Update operation)
        public function editLab(Request $request, $lab_id)
        {
            $lab = Laboratory::find($lab_id);
            if (!$lab) {
                return response()->json(['error' => 'Lab not '], 404);
            }
    
            // Validate and update laboratory data
            $validatedData = $request->validate(Laboratory::$AdminPutRulesForLab);
    
            $lab->update($validatedData);
            return response()->json($lab);
        }
    











        // INVALIDATE - Remove a laboratory by ID (Delete operation)
        public function invalidateLab($lab_id)
        {
            $lab = Laboratory::find($lab_id);
            if (!$lab) {
                return response()->json(['error' => ' not found'], 404);
            }
    
            $lab->update(['lab_status' => 'Inactive']);
            return response()->json(['success' => 'Laboratory invalidated successfully']);
        }
    










        // Additional: Search laboratories by room number
        public function searchByRoomNumber(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'room_number' => 'required|string|max:255',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['debug' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }
        
            // Retrieve the validated room number and normalize it to lowercase
            $roomNumber = strtolower($request->query('room_number'));
        
            // Perform a case-insensitive search using LOWER
            $laboratories = Laboratory::whereRaw('LOWER(room_number) = ?', [$roomNumber])
                ->where('lab_status', 'Assigned') // Filter by active labs
                ->select('id', 'department', 'building_name', 'room_number', 'lab_name', 'professor_investigator', 'department_director')
                ->get();
        
            if ($laboratories->isEmpty()) {
                return response()->json([
                    'debug' => 'No laboratories found',
                    'error' => 'Lab not found',
                ], 404);
            }
        
            return response()->json($laboratories, 200);
        }
        
        
        




        ////////////Design methods////////////////////
       ///////////////////////////////////////////////

    public function assignLabSupervisor(Request $request, $labId)
{
    // Validate the request to ensure the email
    $request->validate([
        'email' => 'required|string',
        // 'lastname' => 'required|string',
    ]);

    // Find the laboratory by lab_id
    $lab = Laboratory::find($labId);

    // Check if the laboratory exists
    if (!$lab) {
        return response()->json(['error' => 'Laboratory not found'], 404);
    }

    // Search for the user by name and lastname
    $user = User::where('email', $request->email)
                ->first();

    // Check if the user exists
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Assign the user as the lab supervisor
    $lab->lab_supervisor = $user->id;
    $lab->save();

    // Return a success message
    return response()->json([
        'message' => 'User successfully assigned as lab supervisor.',
        'lab' => $lab,
        'supervisor' => $user
    ], 200);
}




    
  


}
