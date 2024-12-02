<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\PickupRequest;
use App\Models\Label;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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

    // Verify input data
    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }
    $validatedData = $validator->validated();

    // Verify Authenticated User
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User is not authenticated.'], 401);
    }

    // Verify Label existence
    $label = Label::find($validatedData['label_id']);
    if (!$label) {
        return response()->json(['success' => false, 'error' => 'Label not found'], 404);
    }

    // Verify Label Status
    if ($label->status_of_label == 0) {
        return response()->json(['success' => false, 'error' => 'The label is invalid and cannot be used for a Pickup Request.'], 400);
    }

    // Verify Room Number Authorization
    if ($user->role === 'Administrator') {
        Log::info("Administrator (User ID: {$user->id}) is creating a Pickup Request for Label ID {$label->id}");
    } else {
        // Check if user is associated with the room
        $room = DB::table('rooms')
            ->where('room_number', $label->room_number)
            ->where('user_id', $user->id)
            ->first();

        if (!$room) {
            Log::warning("User ID {$user->id} is not authorized to create a Pickup Request for Label ID {$label->id} in Room {$label->room_number}");
            return response()->json(['error' => 'You do not have permission to create a Pickup Request for this label.'], 403);
        }
    }

    // Check for existing pickup requests
    $existingPickupRequest = PickupRequest::where('label_id', $validatedData['label_id'])
        ->where('status_of_pickup', '!=', 0) // Ignore invalidated requests
        ->first();

    if ($existingPickupRequest) {
        return response()->json(['success' => false, 'error' => 'A valid Pickup Request already exists for this label.'], 409);
    }

    // Create a new pickup request
    $pickupRequest = PickupRequest::create([
        'label_id' => $validatedData['label_id'],
        'timeframe' => $validatedData['timeframe'],
        'status_of_pickup' => 2, // Pending status
        'completion_date' => null,
        'completion_method' => null,
    ]);

    Log::info("Pickup Request created by User ID {$user->id} for Label ID {$label->id}");

    return response()->json(['success' => true, 'data' => $pickupRequest], 201);
}

    

    

    // INVALIDATE PICKUP REQUEST
    public function invalidatePickupRequest(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'pickup_id' => 'required|exists:pickup,id', // Correct table name here
            'message' => 'required|string|max:255'
        ]);

        // Find the pickup request
        $pickupRequest = PickupRequest::find($request->pickup_id); // Ensure the model points to the correct table

        if (!$pickupRequest) {
            return response()->json(['success' => false, 'message' => 'Pickup request not found'], 404);
        }

        // Update the status and save the invalidation message
        $pickupRequest->status_of_pickup = 0; // Set to 'Invalid'
        $pickupRequest->message = $request->message;
        $pickupRequest->save();

        return response()->json(['success' => true, 'message' => 'Pickup request invalidated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
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

    $pickupRequest = PickupRequest::find($request->id); 

    $label = Label::find($pickupRequest->label_id);

    $pickupRequest->status_of_pickup = 1; 
    $pickupRequest->completion_method = $request->completion_method;
    $pickupRequest->completion_date = now();
    $pickupRequest->save();

    $label->status_of_label = 2; 
    $label->save();

    return response()->json(['success' => true, 'message' => 'Pickup request completed successfully.']);
}

    // Searches Pickup Requests by Laboratory, Status, and Completion Method    
    public function searchPickupRequests()
{
    $pickupRequests = PickupRequest::with(['label.laboratory', 'user'])->get();

    if ($pickupRequests->isEmpty()) {
        return response()->json(['message' => 'No pickup requests found.'], 404);
    }

    $totalCount = $pickupRequests->count();

    $formattedPickupRequests = $pickupRequests->map(function ($pickup) {
        $sixMonthsFromStart = $pickup->label && $pickup->label->accumulation_start_date 
            ? Carbon::parse($pickup->label->accumulation_start_date)->addMonths(6) 
            : null;

        $chemicals = DB::table('contents')
            ->where('label_id', $pickup->label_id)
            ->pluck('chemical_name');

        return [
            'Pickup Request ID' => $pickup->id,
            'Label ID' => $pickup->label_id,
            'Requested By Email' => $pickup->user ? $pickup->user->email : 'N/A', // Use user_id to get the email
            'Request Date' => $pickup->created_at->format('Y-m-d'),
            'Chemicals' => $chemicals,
            'Building Name' => $pickup->label->building ?? null,
            'Room Number' => $pickup->label->room_number ?? 'N/A',
            'Quantity' => $pickup->label ? $pickup->label->quantity . ' ' . $pickup->label->units : 'N/A',
            'Container Size' => $pickup->label->container_size ?? 'N/A',
            'Timeframe' => $pickup->timeframe,
            'Status' => match($pickup->status_of_pickup) {
                0 => 'Invalid',
                1 => 'Completed',
                2 => 'Pending',
                3 => 'Overdue',
                default => 'Unknown'
            },
            'Completion Method' => $pickup->completion_method ?? 'N/A',
            'Pickup Due' => $sixMonthsFromStart ? $sixMonthsFromStart->format('M d, Y') : 'N/A'
        ];
    })->sortBy('pickup_due')->values();

    return response()->json([
        'total_reports' => $totalCount,
        'pickup_requests' => $formattedPickupRequests
    ], 200);
}

    

    // COUNT PICKUP REQUESTS WITH PENDING STATUS
    public function countPendingPickupRequests()
    {
        $pickupCount = PickupRequest::where('status_of_pickup', 2)->count();

        return response()->json($pickupCount, 200);
    }

    // RETURNS DATABASE INFORMATION THROUGH FOREIGN KEYSpublic function getAllPickupRequests()
    public function getAllPickupRequests()
    {
        // Retrieve the authenticated user
        $user = Auth::user();
    
        // If the user is an administrator, retrieve all pickup requests
        if ($user->role === 'Administrator') {
            $pickupRequests = PickupRequest::with(['label.user', 'label.laboratory'])->get();
        } else {
            // For non-administrator users, retrieve only the pickup requests for labs they are part of
            $pickupRequests = PickupRequest::whereHas('label', function ($query) use ($user) {
                $query->whereIn('room_number', function ($subQuery) use ($user) {
                    $subQuery->select('room_number')
                        ->from('rooms')
                        ->where('user_id', $user->id);
                });
            })->with(['label.user', 'label.laboratory'])->get();
        }
    
        // Map the data for response
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
                'Message' => $pickupRequest->message, // Include message
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
