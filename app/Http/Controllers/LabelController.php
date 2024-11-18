<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Laboratory;
use App\Models\Chemical;
use Illuminate\Support\Facades\Auth;

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






   //////////////////// // POST - Create a new label (Create operation)///////////////////////////////////////
    public function createLabel(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate(Label::$LabelPostRules);

        // Create and return the new label
        $label = Label::create($validatedData);
        return response()->json($label, 201); // 201 Created
    }







  ///////////////////  // GET a single label by ID (Read operation)///////////////////////////////////////////////////////////////
    // public function show($id)
    // {
    //     $label = Label::find($id);
    //     if (!$label) {
    //         return response()->json(['error' => 'Label not found'], 404);
    //     }
    //     return response()->json($label);
    // }




    //this will return only the labels created by the user logged in
// public function show(Request $request, $id)
// {
//     // Check if the user is authenticated
//     $user = Auth::user();
//     if (!$user) {
//         return response()->json(['error' => 'Unauthorized'], 401);
//     }

//     // Get the authenticated admin's email
//     $adminEmail = $user->email;

//     // Find the label by ID where 'created_by' is the authenticated admin's email
//     $label = Label::with(['laboratory', 'chemicals'])
//                   ->where('label_id', $id)
//                   ->where('created_by', $adminEmail) // assuming 'created_by' column stores the admin's email
//                   ->first();

//     // Check if the label exists and is created by this admin
//     if (!$label) {
//         return response()->json(['error' => 'Label not found or access denied'], 404);
//     }

//     return response()->json($label, 200);
// }




//////////////////// EDIT LABEL FRONT END////////////////////////////////////

    /////////////////// a label by ID /////////////////////////////////////////
    public function updateLabel(Request $request, $id)
{
    // Retrieve the label by label_id
    $label = Label::where('label_id', $id)->first();
    
    // Check if the label exists
    if (!$label) {
        return response()->json(['error' => 'Label not found'], 404);
    }

    // Validate the request data, including contents
    $request->validate([
        ...Label::$LabelPutRules,
        'chemicals' => 'required|array', // Ensure chemicals array is provided
        'chemicals.*.chemical_name' => 'required|string',
        'chemicals.*.cas_number' => 'required|string',
        'chemicals.*.percentage' => 'required|numeric|min:0|max:100'
    ]);

    // Update label with validated data
    $label->update($request->all());

    // Clear previous contents for this label
    DB::table('contents')->where('label_id', $id)->delete();

    // Insert updated chemicals into contents table
    $chemicals = $request->input('chemicals');
    foreach ($chemicals as $chemical) {
        DB::table('contents')->insert([
            'label_id' => $id,
            'chemical_name' => $chemical['chemical_name'],
            'cas_number' => $chemical['cas_number'],
            'percentage' => $chemical['percentage'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // Return a success message
    return response()->json(['success' => 'Label and contents updated successfully'], 200);
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















    








/////////////////////////////////////// CREATE LABEL//////////////////////////////////////////////////////






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

















///////////////////// INVALID A LABEL??????????????????????????????
    // change status to invalid, need to add the message to explain why the invalidation
    public function invalidateLabel(Request $request, $id)
    {
        // Validate the provided message
        $request->validate([
            'message' => 'required|string|max:255'
        ]);
    
        $updatedRows = DB::table('label')
        ->where('label_id', $id)
        ->update([
            'status_of_label' => 0, // Assuming 0 is the STATUS_INVALID
            'message' => $request->message
        ]);

    if ($updatedRows) {
        return response()->json([
            'success' => 'Label updated successfully to Invalid',
            'message' => $request->message
        ], 200);
    } else {
        return response()->json(['error' => 'Failed to update label or label not found'], 500);
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


public function calculateTotalWeight()
{
    // Define solid units for categorization
    $solidUnits = ['g', 'kg', 'lb'];

    // Get the date 30 days ago
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    // Get all labels created in the last 30 days
    $labels = Label::where(DB::raw('date(label.date_created / 1000, "unixepoch")'), '>=', $thirtyDaysAgo)->get();

    // Initialize variable for total solid weight
    $totalSolidWeight = 0;

    // Loop through labels to sum quantities based on units
    foreach ($labels as $label) {
        if (in_array(strtolower($label->units), $solidUnits)) {
            $totalSolidWeight += $label->quantity;
        }
    }

    // Return the total solid weight as a response
    return response()->json([
        'total_solid_weight' => $totalSolidWeight,
    ]);
}


public function calculateTotalVolume()
{
    // Define liquid units for categorization
    $liquidUnits = ['mL', 'L', 'gal'];

    // Get the date 30 days ago
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    // Get all labels created in the last 30 days
    $labels = Label::where(DB::raw('date(label.date_created / 1000, "unixepoch")'), '>=', $thirtyDaysAgo)->get();

    // Initialize variable for total liquid volume
    $totalLiquidVolume = 0;

    // Loop through labels to sum quantities based on units
    foreach ($labels as $label) {
        if (in_array(strtolower($label->units), $liquidUnits)) {
            $totalLiquidVolume += $label->quantity;
        }
    }

    // Return the total liquid volume as a response
    return response()->json([
        'total_liquid_volume' => $totalLiquidVolume,
    ]);
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
                'label.units',
                DB::raw('SUM(label.quantity * (contents.percentage / 100)) AS total_contributed_quantity'),
                DB::raw('date(label.date_created / 1000, "unixepoch") AS readable_date')

            );

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween(DB::raw('date(label.date_created / 1000, "unixepoch")'), [$request->start_date, $request->end_date]);
        }

        // Filter by chemical name if provided
        if ($request->filled('chemical_name')) {
            $query->where('contents.chemical_name', $request->chemical_name);
        }

        $query->groupBy('contents.chemical_name', 'label.units', 'readable_date');

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
        $labelCount = Label::whereBetween(DB::raw('date(label.date_created / 1000, "unixepoch")'), [$sevenDaysAgo,$today])->count();

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

    // Fetch the label along with related contents
    $results = Label::query()
        ->select('label.label_id', 'label.container_size', 'contents.chemical_name', 'contents.percentage')
        ->join('contents', 'label.label_id', '=', 'contents.label_id')
        ->where('label.label_id', $validatedData['label_id']) // Filter by label ID
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
