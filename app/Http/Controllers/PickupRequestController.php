<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\PickupRequest;
use App\Models\Label;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PickupRequestController extends Controller
{

// ------------------------------------------------------------------------------------------------------
// ---------------------------------------------API METHODS----------------------------------------------
// ------------------------------------------------------------------------------------------------------

    public function getAll() //GET ALL
    {
        $pickupRequest = PickupRequest::all();
        return response()->json($pickupRequest, 200);
    }

    public function find($id) //GET ID
    {
        $pickupRequest = PickupRequest::find($id);
        
        if (!$pickupRequest) {
            return response()->json(['message' => 'Pickup Request not found'], 404);
        }
    
        return response()->json($pickupRequest, 200);
    }

    public function create(Request $request) //POST
    {
        $validatedData = $request->validate([
            'completion_date' => 'required|date',
            'status_of_pickup' => 'required|integer|in:0,1,2,3',
            'timeframe' => 'required|string|max:255',
            'completion_method' => 'required|string|max:255'
        ]);
    
        $pickupRequest = PickupRequest::create($validatedData);
    
        return response()->json($pickupRequest, 201);
    }
    

    public function update(Request $request, $id) //PUT
    {
        $pickupRequest = PickupRequest::find($id);
    
        if (!$pickupRequest) {
            return response()->json(['message' => 'Pickup Request not found'], 404);
        }
    
        $validatedData = $request->validate([
            'completion_date' => 'required|date',
            'status_of_pickup' => 'required|integer|in:0,1,2,3', 
            'timeframe' => 'required|string|max:255',
            'completion_method' => 'required|string|max:255',
        ]);
    
        $pickupRequest->update($validatedData);
    
        return response()->json($pickupRequest, 200);
    }

    public function destroy($id) //DELETE
    {
        $pickupRequest = PickupRequest::find($id);
    
        if (!$pickupRequest) {
            return response()->json(['message' => 'Pickup Request not found'], 404);
        }
    
        $pickupRequest->delete();
    
        return response()->json(['message' => 'Pickup Request invalidated successfully'], 200);
    }    

// ------------------------------------------------------------------------------------------------------
// -------------------------------------------FRONTEND METHODS-------------------------------------------
// ------------------------------------------------------------------------------------------------------ 

    // Creates a Pickup Request using a valid LABEL_ID
    public function createPickupRequest(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required|integer',
            'timeframe' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $label = Label::find($validatedData['label_id']);
        if (!$label) {
            return response()->json(['success' => false, 'error' => 'Label not found'], 404);
        }
    
        $pickupRequest = PickupRequest::create(array_merge(
            $validatedData,
            [
                'status_of_pickup' => 2, 
                'completion_date' => null,
                'completion_method' => null,
            ]
        ));
    
        return response()->json(['success' => true, 'data' => $pickupRequest], 201);
    }
    

    // INVALIDATE PICKUP REQUEST
    public function invalidatePickupRequest(Request $request)
{
    $pickupRequest = PickupRequest::find($request->pickup_id);
    
    if (!$pickupRequest) {
        return response()->json(['success' => false, 'message' => 'Pickup request not found'], 404);
    }
    
    $pickupRequest->status_of_pickup = 0; // Set to 'Invalid' status
    $pickupRequest->save();
    
    return response()->json(['success' => true, 'message' => 'Pickup request invalidated successfully']);
}


    //CHANGE STATUS OF LABEL AND PICKUP TO COMPLETED
    public function completePickupRequest(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:pickup,id',  // Changed pickup_id to id
        'completion_method' => 'required|string|in:Clean Out,Regular'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $pickupRequest = PickupRequest::find($request->id);  // Changed pickup_id to id
    $pickupRequest->status_of_pickup = 1; // Set status to 'Completed'
    $pickupRequest->completion_method = $request->completion_method;
    $pickupRequest->completion_date = now();
    $pickupRequest->save();

    return response()->json(['success' => true, 'message' => 'Pickup request completed successfully.']);
}

    // Searches Pickup Requests by Laboratory, Status, and Completion Method    
    public function searchPickupRequests()
    {
        $pickupRequests = PickupRequest::with(['label.laboratory', 'label.user'])->get();
    
        if ($pickupRequests->isEmpty()) {
            return response()->json(['message' => 'No pickup requests found.'], 404);
        }
    
        $totalCount = $pickupRequests->count();
    
        $formattedPickupRequests = $pickupRequests->map(function ($pickup) {
            $sixMonthsFromStart = Carbon::parse($pickup->label->accumulation_start_date)->addMonths(6);
    
            $chemicals = DB::table('contents')
                ->where('label_id', $pickup->label_id)
                ->pluck('chemical_name');
    
            return [
                'Pickup Request ID' => $pickup->id,
                'Label ID' => $pickup->label_id,
                'Requested By' => $pickup->label->laboratory->professor_investigator ?? 'N/A',
                'Request Date' => $pickup->created_at->format('Y-m-d'),
                'Chemicals' => $chemicals,
                'Building Name' => $pickup->label->building ?? null,
                'Room Number' => $pickup->label->room_number ?? 'N/A',
                'Quantity' => $pickup->label->quantity . ' ' . $pickup->label->units,
                'Container Size' => $pickup->label->container_size,
                'Timeframe' => $pickup->timeframe,
                'Status' => match($pickup->status_of_pickup) {
                    0 => 'Invalid',
                    1 => 'Completed',
                    2 => 'Pending',
                    3 => 'Overdue',
                    default => 'Unknown'
                },
                'Completion Method' => $pickup->completion_method ?? 'N/A',
                'pickup_due' => $sixMonthsFromStart->format('M d, Y')
            ];
        })->sortBy('pickup_due')->values();
    
        return response()->json([
            'total_reports' => $totalCount,
            'pickup_requests' => $formattedPickupRequests
        ], 200);
    }
    

    
    // COUNT PICKUP REQUESTS WITH PENDING STATUS
    public function listPickupRequests() 
    {    
        $pickupRequests = PickupRequest::where('status_of_pickup', 2)->get();
    
        $pickupCount = $pickupRequests->count();
    
        if ($pickupRequests->isNotEmpty()) {
            return response()->json(['pickup_count' => $pickupCount, 'pickup_requests' => $pickupRequests], 200);
        } 
    
        return response()->json(['message' => 'No pickup requests found with the specified status.'], 404);
    }

    // RETURNS DATABASE INFORMATION THROUGH FOREIGN KEYSpublic function getAllPickupRequests()
    public function getAllPickupRequests()
    {
        $pickupRequests = PickupRequest::with(['label.user', 'label.laboratory'])->get();

        $data = $pickupRequests->map(function ($pickupRequest) {
            $chemicals = DB::table('contents')
                ->where('label_id', $pickupRequest->label_id)
                ->pluck('chemical_name');

            return [
                'Pickup ID' => $pickupRequest->id,
                'Label ID' => $pickupRequest->label ? $pickupRequest->label->label_id : null,
                'Chemical(s)' => $chemicals, 
                'Building Name' => $pickupRequest->label ? $pickupRequest->label->building : null,
                'Room Number' => $pickupRequest->label ? $pickupRequest->label->room_number : null,
                'Container Size' => $pickupRequest->label ? $pickupRequest->label->container_size : null,
                'Request Date' => $pickupRequest->created_at,
                'Completion Date' => $pickupRequest->completion_date,
                'Status' => $pickupRequest->status_of_pickup,
            ];
        });

        return response()->json($data, 200);
    }
    
    

    // RETURNS PICKUP REQUEST STATUS
    public function getPickupStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required|integer|exists:pickup,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $pickupRequest = PickupRequest::find($validatedData['pickup_id']);

        if (!$pickupRequest) {
            return response()->json(['message' => 'Pickup request not found'], 404);
        }

        $statusNames = [
            0 => 'Invalid',
            1 => 'Completed',
            2 => 'Pending',
            3 => 'Overdue'
        ];

        $statusName = $statusNames[$pickupRequest->status_of_pickup] ?? 'Unknown';

        return response()->json(['status' => $statusName], 200);
    }

  
}
