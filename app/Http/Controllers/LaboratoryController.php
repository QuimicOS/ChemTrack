<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Models\User;

use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    /////////////////////////////////////////////////////////////
    ////////////////////// CRUD OPERATIONS //////////////////////
    /////////////////////////////////////////////////////////////


        // GET all laboratories
        public function getAllLabs()
        {
            $labs = Laboratory::all();
            return response()->json($labs);
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
    
            $lab->update(['lab_status' => 'Invalid']);
            return response()->json(['success' => 'Laboratory invalidated successfully']);
        }
    










        // Additional: Search laboratories by room number
        public function searchByRoomNumber(Request $request)
        {

            // Retrieve the validated search term
            $roomNumber = $request->input('room');

            $roomNumber = Laboratory::where('room_number', $roomNumber)->first();
    
            if ($roomNumber) {
                return response()->json($roomNumber);
            } else {
                return response()->json(['message' => 'Room not found'], 404);
            }
        
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
