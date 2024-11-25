<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Laboratory;
use App\Models\Content;
use App\Models\Notification;
use App\Models\PickupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class LabelController extends Controller
{

    //CRUD OPERATIONS



    // GET all labels (Read operation)
    public function index()
    {
        return response()->json(Label::all());
    }


    public function store(Request $request)
{
    $validatedData = $request->validate([
        'label.created_by' => 'required|string',
        'label.date_created' => 'required|date',
        'label.department' => 'required|string',
        'label.building' => 'required|string',
        'label.room_number' => 'required|string',
        'label.lab_name' => 'required|string',
        'label.principal_investigator' => 'required|string',
        'label.container_size' => 'required|string',
        'label.quantity' => 'required|numeric',
        //'label.label_size' => 'required|string',
        'label.units' => 'required|string',
        'content' => 'array|nullable',
        'content.*.chemical_name' => 'required_with:content|string',
        'content.*.cas_number' => 'required_with:content|string',
        'content.*.percentage' => 'required_with:content|numeric|min:0|max:100',
    ]);

    DB::beginTransaction();
    try {
        // Create the label
        $labelData = $validatedData['label'];
        $label = Label::create($labelData);

        // Create associated content
        if (!empty($validatedData['content'])) {
            foreach ($validatedData['content'] as $chemical) {
                Content::create([
                    'label_id' => $label->label_id,
                    'chemical_name' => $chemical['chemical_name'],
                    'cas_number' => $chemical['cas_number'],
                    'percentage' => $chemical['percentage'],
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'data' => $label], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}




   //////////////////// // POST - Create a new label (Create operation)///////////////////////////////////////
    public function createLabel(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate(Label::$LabelPostRules);

        // Create and return the new label
        $label = Label::create($validatedData);
        return response()->json($label, 201); // 201 Created
    }







//////////////////// EDIT LABEL FRONT END////////////////////////////////////

    /////////////////// a label by ID /////////////////////////////////////////
    public function updateLabel(Request $request, $id)
{
    try {
        // Step 1: Validate the request data
        $data = $request->validate([
            'quantity' => 'required|numeric',
            'units' => 'required|string',
            'chemicals' => 'required|array',
            'chemicals.*.chemical_name' => 'required|string',
            'chemicals.*.cas_number' => 'required|string',
            'chemicals.*.percentage' => 'required|numeric',
        ]);

        // Step 2: Fetch the label and its room number
        $label = Label::findOrFail($id);

        // Check if the label status is "Pending"
        if ($label->status_of_label !== 1) {
            return response()->json(['error' => 'Only pending labels can be edited.'], 403);
        }

        // Fetch the room number associated with the label
        $roomNumber = $label->room_number;

        // Step 3: Ensure the user has access to the room or is an administrator
        $user = auth()->user(); // Get the currently authenticated user

        // If the user is an administrator, allow them to edit the label
        if ($user->role === 'Administrator') {
            Log::info("Administrator (User ID: {$user->id}) is editing label ID $id");
        } else {
            // Otherwise, check if the user is associated with the room
            $room = DB::table('rooms')
                ->where('room_number', $roomNumber)
                ->where('user_id', $user->id)
                ->first();

            if (!$room) {
                Log::warning("User ID {$user->id} is not authorized to edit label ID $id in room $roomNumber");
                return response()->json(['error' => 'You do not have permission to edit this label.'], 403);
            }
        }

        // Step 4: Update the label itself
        $label->update([
            'quantity' => $data['quantity'],
            'units' => $data['units'],
        ]);

        // Debug: Log label update
        Log::info("Label ID $id updated successfully with quantity: {$data['quantity']} and units: {$data['units']}");

        // Step 5: Handle the chemicals in the 'contents' table
        $submittedCasNumbers = array_column($data['chemicals'], 'cas_number');

        // Fetch current chemicals linked to the label
        $currentChemicals = DB::table('contents')
            ->where('label_id', $id)
            ->pluck('cas_number', 'id'); // Pluck CAS numbers with their IDs

        // Determine chemicals to delete
        $chemicalsToDelete = $currentChemicals->filter(function ($casNumber) use ($submittedCasNumbers) {
            return !in_array($casNumber, $submittedCasNumbers);
        });

        // Debug: Log chemicals to delete
        Log::info("Chemicals to delete: " . implode(', ', $chemicalsToDelete->values()->toArray()));

        // Delete chemicals no longer in the request
        if ($chemicalsToDelete->isNotEmpty()) {
            DB::table('contents')->whereIn('id', $chemicalsToDelete->keys())->delete();
        }

        // Step 6: Add or update chemicals
        foreach ($data['chemicals'] as $chemical) {
            $existingChemical = DB::table('contents')
                ->where('label_id', $id)
                ->where('cas_number', $chemical['cas_number'])
                ->first();

            if ($existingChemical) {
                // Update existing chemical
                DB::table('contents')->where('id', $existingChemical->id)->update([
                    'chemical_name' => $chemical['chemical_name'],
                    'percentage' => $chemical['percentage'],
                    'updated_at' => now(),
                ]);
                // Debug: Log chemical update
                Log::info("Updated chemical with CAS: {$chemical['cas_number']} in label ID $id");
            } else {
                // Insert new chemical
                DB::table('contents')->insert([
                    'label_id' => $id,
                    'chemical_name' => $chemical['chemical_name'],
                    'cas_number' => $chemical['cas_number'],
                    'percentage' => $chemical['percentage'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // Debug: Log new chemical insertion
                Log::info("Added new chemical with CAS: {$chemical['cas_number']} to label ID $id");
            }
        }

        // Return success response
        return response()->json(['success' => true]);
    } catch (ValidationException $e) {
        // Log validation errors
        Log::error("Validation error: " . json_encode($e->errors()));
        return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
    } catch (Exception $e) {
        // Log general errors
        Log::error("Error updating label: " . $e->getMessage());
        return response()->json(['error' => 'An error occurred while updating the label'], 500);
    }
}
    

    











    //////////////////// Search LABEL FRONT END////////////////////////////////////

    /////////////////// a label by ID /////////////////////////////////////////
    public function searchLabelById($id)
    {
        // Retrieve the label by label_id
        $label = DB::table('label')
            ->where('label_id', $id)
            ->first();
    
        // Check if the label exists
        if (!$label) {
            return response()->json(['error' => 'Label not found'], 404);
        }
    
        // Retrieve related contents for the label
        $contents = DB::table(table: 'contents')
            ->where('label_id', $id)
            ->select('chemical_name', 'cas_number', 'percentage')
            ->get();
    
        // Combine label data with contents
        $labelData = (array) $label;
        $labelData['contents'] = $contents;
    
        // Return label data with contents
        return response()->json($labelData, 200);
    }















    














    ////////////////////////Design methods/////////////////////////////////////
    ///////////////////////////////////////////////////////////////////


    //change the label status to ACTIVE
    public function updateLabelStatusActive($id){
        $label = Label::find($id);
        if (!$label) {
            return response()->json(['error' => 'not found'], 404);
        }

        $label->update(['status_of_label' => 'Active']);
        return response()->json(['success' => 'Label updated successfully to Active']);
    }


    //change the label TO PENDING
    public function updateLabelStatusPending($id){
        $label = Label::find($id);
        if (!$label) {
            return response()->json(['error' => 'nothing found'], 404);
        }

        $label->update(['status_of_label' => 'Pending']);
        return response()->json(['success' => 'Label updated successfully to Pending']);


    }

    //change the label TO COMPLETED
    public function updateLabelStatusCompleted($id){
        $label = Label::find($id);
        if (!$label) {
            return response()->json(['error' => 'Label not existent'], 404);
        }

        $label->update(['status_of_label' => 'Completed']);
        return response()->json(['success' => 'Label updated successfully to Completed']);

    }


    //Mark as completed
    public function checkPickupRequest(Request $request)
    {
        $labelId = $request->input('label_id');
    
        // Check if a pickup request exists for the label
        $pickupExists = PickupRequest::where('label_id', $labelId)->exists();
    
        if (!$pickupExists) {
            return response()->json(['message' => 'Pickup request not found'], 404);
        }
    
        // Mark notification as done if pickup request exists
        Notification::where('label_id', $labelId)
            ->update(['status_of_notification' => 1]);
    
        return response()->json(['message' => 'Notification marked as done'], 200);
    }









    //----------------------------INVALID A LABEL------------------------------------------------------//
    // change status to invalid, need to add the message to explain why the invalidation
    public function invalidateLabel(Request $request, $id)
    {
        // Validate the provided message
        $request->validate([
            'message' => 'required|string|max:255',
        ]);
    
        try {
            // Fetch the authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized: User not authenticated'], 401);
            }
    
            // Fetch the label by ID
            $label = DB::table('label')->where('label_id', $id)->first();
            if (!$label) {
                return response()->json(['error' => 'Label not found'], 404);
            }
    
            // Check if the label is already invalidated
            if ($label->status_of_label === 0) { // Assuming 0 means "Invalid"
                return response()->json(['error' => 'Label is already invalidated'], 400);
            }
    
            // If user is not an Administrator, validate access to the label's room number
            if ($user->role !== 'Administrator') {
                // Check if the user has permission for the label's room number
                $hasAccess = DB::table('rooms')
                    ->where('user_id', $user->id)
                    ->where('room_number', $label->room_number)
                    ->exists();
    
                if (!$hasAccess) {
                    return response()->json(['error' => 'Unauthorized: User does not have permission to invalidate this label'], 403);
                }
            }
    
            // Update the label to "Invalid"
            $updatedRows = DB::table('label')
                ->where('label_id', $id)
                ->update([
                    'status_of_label' => 0, // Set to invalid
                    'message' => $request->message,
                ]);
    
            if ($updatedRows) {
                return response()->json([
                    'success' => 'Label updated successfully to Invalid',
                    'message' => $request->message,
                ], 200);
            } else {
                return response()->json(['error' => 'Error invalidating label'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error invalidating label', 'details' => $e->getMessage()], 500);
        }
    }
    
    



//     public function invalidateLabel(Request $request, $id)
// {
//     // Validate the provided message
//     $request->validate([
//         'message' => 'required|string|max:255'
//     ]);

//     // Retrieve the label by ID
//     $label = Label::find($id);
//     if (!$label) {
//         return response()->json(['error' => 'Label not found'], 404);
//     }

//     // Get the authenticated user and their laboratory ID
//     $user = Auth::user();
//     $userLabId = $user->lab_id; // Assuming the user has a `laboratories_id` field indicating their lab

//     // Check if the user's lab matches the label's lab
//     if ($label->lab_id !== $userLabId) { 
//         return response()->json(['error' => 'Unauthorized: You do not have permission to invalidate this label'], 403);
//     }

//     // Invalidate the label and store the message
//     $label->update([
//         'status_of_label' => 'INVALID',
//         'message' => $request->message
//     ]);

//     return response()->json([
//         'success' => 'Label updated successfully to Invalid',
//         'message' => $request->message
//     ], 200);
// }

  




    
        






























































































///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// STATISTICS para FRONTEND/////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// public function calculateTotalWeight()
// {
//     // Define solid units for categorization
//     $solidUnits = ['Grams', 'Kilograms', 'Pounds'];

//     // Get the date 30 days ago
//     $thirtyDaysAgo = Carbon::now()->subDays(30);

//     // Get all labels created in the last 30 days
//     $labels = Label::where(DB::raw('date(label.date_created)'), '>=', $thirtyDaysAgo)->get();

//     // Initialize variable for total solid weight
//     $totalSolidWeight = 0;

//     // Loop through labels to sum quantities based on units
//     foreach ($labels as $label) {
//         if (in_array(strtolower($label->units), $solidUnits)) {
//             $totalSolidWeight += $label->quantity;
//         }
//     }

//     // Return the total solid weight as a response
//     return response()->json([
//         'total_solid_weight' => $totalSolidWeight,
//     ]);
// }

public function calculateTotalWeight()
{
    $totalWeightKg = Label::where('date_created', '>=', now()->subDays(30))
        ->whereIn('units', ['Grams', 'Kilograms', 'Pounds'])
        ->selectRaw("
            SUM(
                CASE
                    WHEN units = 'Grams' THEN quantity / 1000
                    WHEN units = 'Pounds' THEN quantity * 0.453592
                    WHEN units = 'Kilograms' THEN quantity
                    ELSE 0
                END
            ) AS total_weight_kg
        ")
        ->value('total_weight_kg');

    return response()->json(['total_weight_kg' => $totalWeightKg], 200);
}



public function calculateTotalVolume()
{
    $totalVolumeLiters = Label::where('date_created', '>=', now()->subDays(30))
        ->whereIn('units', ['Milliliters', 'Liters', 'Gallons'])
        ->selectRaw("
            SUM(
                CASE
                    WHEN units = 'Milliliters' THEN quantity / 1000
                    WHEN units = 'Gallons' THEN quantity * 3.78541
                    WHEN units = 'Liters' THEN quantity
                    ELSE 0
                END
            ) AS total_volume_liters
        ")
        ->value('total_volume_liters');

    return response()->json(['total_volume_liters' => $totalVolumeLiters], 200);
}





  














 

////////////////////UNWANTED MATERIAL SUMMARY////////////////////////////// 


//WORKS

public function unwantedMaterialSummary(Request $request)
{
    try {
        $query = DB::table('label')
            ->join('contents', 'label.label_id', '=', 'contents.label_id')
            ->select(
                'contents.chemical_name',
                DB::raw("SUM(CASE 
                    WHEN label.units = 'Milliliters' THEN (label.quantity * (contents.percentage / 100)) / 1000 -- Convert ml to L
                    WHEN label.units = 'Gallons' THEN (label.quantity * (contents.percentage / 100)) * 3.78541 -- Convert gal to L
                    WHEN label.units = 'Liters' THEN (label.quantity * (contents.percentage / 100)) -- Already in L
                    ELSE 0 END) AS total_liquid_quantity"),
                DB::raw("SUM(CASE 
                    WHEN label.units = 'Grams' THEN (label.quantity * (contents.percentage / 100)) / 1000 -- Convert g to kg
                    WHEN label.units = 'Pounds' THEN (label.quantity * (contents.percentage / 100)) * 0.453592 -- Convert lbs to kg
                    WHEN label.units = 'Kilograms' THEN (label.quantity * (contents.percentage / 100)) -- Already in kg
                    ELSE 0 END) AS total_solid_quantity")
            );

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween(DB::raw('date(label.date_created)'), [$request->start_date, $request->end_date]);
        }

        // Filter by chemical name if provided
        if ($request->filled('chemical_name')) {
            $query->where('contents.chemical_name', $request->chemical_name);
        }

        $query->groupBy('contents.chemical_name');

        $results = $query->get();

        return response()->json($results, 200);
    } catch (\Exception $e) {
        \Log::error("Unwanted Material Summary Error: " . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}








        ////LABELS CREATED ON THE LAST 7 DAYS
    public function countLabelsLast7Days()
    {

        $sevenDaysAgo = now()->subWeek();
        $today = now();

        // Count the labels created in the last 7 days
        $labelCount = Label::whereBetween(DB::raw('date(label.date_created)'), [$sevenDaysAgo,$today])->count();

        // Return the count in a JSON response
        return response()->json(['label_count' => $labelCount],200);
    }



















////// fro what was this?



public function getLabInfoByRoomNumber($roomNumber)
{
    // Find the lab by room number
    $lab = Laboratory::where('room_number', $roomNumber)->first();

    // If no lab is found, return an empty response
    if (!$lab) {
        return response()->json(['error' => 'Lab not found'], 404);
    }

    // Return the lab name and professor investigator
    return response()->json([
        'lab_name' => $lab->lab_name,
        'professor_investigator' => $lab->professor_investigator
    ]);
}













  




//////////////////////////////// MEMORANDUM/////////////////////////////////
public function memorandum(Request $request)
{
    // Validate the input data
    $validatedData = $request->validate([
        'label_id' => 'required|integer', // Mandatory search by label ID
    ]);

    // Fetch the label along with related contents where status is Completed
    $results = Label::query()
        ->select('label.label_id', 'label.container_size', 'contents.chemical_name', 'contents.percentage')
        ->join('contents', 'label.label_id', '=', 'contents.label_id')
        ->where('label.label_id', $validatedData['label_id']) // Filter by label ID
        ->where('label.status_of_label', 2) // Only include completed labels
        ->get();

    // Return the search results in JSON format
    return response()->json($results, 200);
}














    //// get the label close to the 6 month mark
    public function getLabelsNearSixMonths()
    {
        // Calculate the date range for 5 months and 5.5 months ago
        $fiveMonthsAgo = Carbon::now()->subMonths(5);
        $fiveAndHalfMonthsAgo = Carbon::now()->subMonths(5)->subDays(15);

        // Query labels where accumulation_date is between 5 and 5.5 months ago
        $labels = Label::whereBetween(DB::raw('date(label.date_created / 1000, "unixepoch")'), [$fiveAndHalfMonthsAgo, $fiveMonthsAgo])->get();

        // Return the result
        return response()->json([
            'message' => 'Labels close to 6 months accumulation date',
            'labels' => $labels
        ], 200);
    }









 

}
