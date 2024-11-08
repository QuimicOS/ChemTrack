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
    public function show(Request $request, $id)
    {
        // Get the authenticated admin's ID
        $adminId = Auth::id();

        // Find the label by ID where 'created_by' is the authenticated admin's ID
        $label = Label::where('id', $id)
                    ->where('created_by', $adminId) // assuming 'created_by' column stores the admin's ID
                    ->first();

        // Check if the label exists and is created by this admin
        if (!$label) {
            return response()->json(['error' => 'Label not found or access denied'], 404);
        }

        return response()->json($label, 200);
    }





    /////////////////// PUT - Update a label by ID (Update operation)/////////////////////////////////////////
    // public function updateLabelInfo(Request $request, $id)
    // {
    //     $label = Label::find($id);
    //     if (!$label) {
    //         return response()->json(['error' => 'Label not found'], 404);
    //     }

    //     // Validate and update label data
    //     $validatedData = $request->validate(Label::$LabelPutRules);

    //     $label->update($validatedData);
    //     return response()->json($label);
    // }









    //update a label if a specified user is attached to a specified lab
    public function update(Request $request, $id)
    {
        // Find the label by its ID
        $label = Label::find($id);
        if (!$label) {
            return response()->json(['error' => 'Label not found'], 404);
        }
    
        // Get the authenticated user and their associated lab
        $user = Auth::user();
        $userLabId = $user->lab_id; // Assuming `laboratories_id` is the column linking user to their lab
    
        // Check if the label's lab matches the user's lab
        if ($label->lab_id !== $userLabId) { 
            return response()->json(['error' => 'Unauthorized: You do not have permission to edit this label'], 403);
        }
    
        // Validate input and update the label (add your validation rules as needed)
        $request->validate(Label::$LabelPutRules);
    
        $label->update($request->all());
    
        return response()->json([
            'message' => 'Label updated successfully',
            'label' => $label
        ], 200);
    }









    // ///////////////////////// DELETE - Remove a label by ID (Delete operation)//////////////////////////////////////////////
    // public function deleteLabel($id)
    // {
    //     $label = Label::find($id);
    //     if (!$label) {
    //         return response()->json(['error' => 'Nothing here'], 404);
    //     }

    //     $label->delete();
    //     return response()->json(['success' => 'Label deleted successfully']);
    // }


// ///////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////




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









    //change status to invalid, need to add the message to explain why the invalidation
    // public function invalidateLabel(Request $request, $id){
    //     //ask the user message
    //     $request->validate([
    //         'message' => 'required|string|max:255'
    //     ]);


    //     $label = Label::find($id);
    //     if (!$label) {
    //         return response()->json(['error' => 'Label not found'], 404);
    //     }

    //     $label->update([
    //         'status_of_label' => 'INVALID',
    //         'message' => $request->message
        
    //     ]);
    //     return response()->json([
    //         'success' => 'Label updated successfully to Invalid',
    //         'message' => $request->message
    //     ]);

    // }











    public function invalidateLabel(Request $request, $id)
{
    // Validate the provided message
    $request->validate([
        'message' => 'required|string|max:255'
    ]);

    // Retrieve the label by ID
    $label = Label::find($id);
    if (!$label) {
        return response()->json(['error' => 'Label not found'], 404);
    }

    // Get the authenticated user and their laboratory ID
    $user = Auth::user();
    $userLabId = $user->lab_id; // Assuming the user has a `laboratories_id` field indicating their lab

    // Check if the user's lab matches the label's lab
    if ($label->lab_id !== $userLabId) { 
        return response()->json(['error' => 'Unauthorized: You do not have permission to invalidate this label'], 403);
    }

    // Invalidate the label and store the message
    $label->update([
        'status_of_label' => 'INVALID',
        'message' => $request->message
    ]);

    return response()->json([
        'success' => 'Label updated successfully to Invalid',
        'message' => $request->message
    ], 200);
}

  




    
        




















///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// STATISTICS para FRONTEND/////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


public function calculateVolumeAndWeight()
{
    // Define solid and liquid units for categorization
    $solidUnits = ['g', 'kg', 'lb']; 
    $liquidUnits = ['mL', 'L', 'gal']; 

    // Get the date 30 days ago
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    // Get all labels created in the last 30 days
    $labels = Label::where('created_at', '>=', $thirtyDaysAgo)->get();

    // Initialize variables
    $totalSolidWeight = 0;
    $totalLiquidVolume = 0;

    // Loop through labels to sum quantities based on units
    foreach ($labels as $label) {
        if (in_array(strtolower($label->units), $solidUnits)) { //strlower to lower and upper case to insesitivity
            $totalSolidWeight += $label->quantity;
        } elseif (in_array(strtolower($label->units), $liquidUnits)) {
            $totalLiquidVolume += $label->quantity;
        }
    }

    // Return the totals as a response
    return response()->json([
        'total_solid_weight' => $totalSolidWeight,
        'total_liquid_volume' => $totalLiquidVolume,
    ]);
}




  














 

////////////////////UNWANTED MATERIAL SUMMARY////////////////////////////// NO QUIERO SABER DE ELLA, pregunbtarle a victor
public function unwantedMaterialSummary(Request $request)
{
    // Validate the request to ensure correct data types
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'chemical_name' => 'nullable|string',
    ]);

    // Start building the query
    $query = DB::table('label')
                ->join('chemicals', 'label.chemical_id', '=', 'chemicals.id')
                ->select('chemicals.chemical_name','label.units', DB::raw('SUM(label.quantity) as total_quantity'))
                ->groupBy('chemicals.chemical_name,label.units')
                ->orderBy('chemicals.chemical_name', 'asc');

    // Apply date range filter using 'created_at' if provided
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('label.created_at', [$request->start_date, $request->end_date]);
    }

    // Apply chemical name filter if provided
    if ($request->chemical_name) {
        $query->where('chemicals.chemical_name', 'like', '%' . $request->chemical_name . '%');
    }

    // Execute the query and get the summary
    $summary = $query->get();

    // Return the summary as a JSON response
    return response()->json($summary);
}






        ////LABELS CREATED ON THE LAST 7 DAYS
    public function countLabelsLast7Days()
    {
        // Get the date 7 days ago from now
        // $sevenDaysAgo = Carbon::now()->subDays(7);

        // // Count the labels created in the last 7 days
        // $labelCount = Label::where('created_at', '>=', $sevenDaysAgo)->count();

        // // Return the count in a JSON response
        // return response()->json(['label_count' => $labelCount]);

        $sevenDaysAgo = now()->subWeek();
        $today = now();

        // Count the labels created in the last 7 days
        $labelCount = Label::whereBetween('created_at', [$sevenDaysAgo,$today])->count();

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
        $validatedData = $request->validate([
            'id' => 'nullable|integer', // Optional search by label ID
            'chemical_name' => 'nullable|string|max:255', // Optional search by chemical name
            'container_size' => 'nullable|numeric', // Optional search by container size (capacity)
        ]);

        $query = Label::query();

        // Filter by ID if provided
        if ($request->filled('id')) {
            $query->where('id', $validatedData['id']);
        }

        // Filter by chemical name if provided
        if ($request->filled('chemical_name')) {
            $query->where('chemical_name', 'like', '%' . $validatedData['chemical_name'] . '%');
        }

        // Filter by container size (capacity) if provided
        if ($request->filled('container_size')) {
            $query->where('container_size', $validatedData['container_size']);
        }

        // Execute the query and get the results
        $label = $query->get();

        // Return the search results in JSON format
        return response()->json($label);
    }


    //// get the label close to the 6 month mark
    public function getLabelsNearSixMonths()
    {
        // Calculate the date range for 5 months and 5.5 months ago
        $fiveMonthsAgo = Carbon::now()->subMonths(5);
        $fiveAndHalfMonthsAgo = Carbon::now()->subMonths(5)->subDays(15);

        // Query labels where accumulation_date is between 5 and 5.5 months ago
        $labels = Label::whereBetween('accumulation_start_date', [$fiveAndHalfMonthsAgo, $fiveMonthsAgo])->get();

        // Return the result
        return response()->json([
            'message' => 'Labels close to 6 months accumulation date',
            'labels' => $labels
        ], 200);
    }









 

}
