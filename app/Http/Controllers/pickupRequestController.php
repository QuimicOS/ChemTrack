<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\PickupRequest;
use App\Models\Label;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'pickup_date' => 'required|date',
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
            'pickup_date' => 'required|date',
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();

        if (!Label::where('id', $validatedData['label_id'])->exists()) {
            return response()->json(['error' => 'Label not found'], 404);
        }

        $label = Label::find($validatedData['label_id']);
        if (!$label) {
            return response()->json(['error' => 'Label not found'], 404);
        }
    
        $pickupRequest = PickupRequest::create(array_merge(
            $validatedData,
            [
                'status_of_pickup' => 2, 
                'pickup_date' => null,
                'completion_method' => null,
            ]
        ));
    
        return response()->json($pickupRequest, 201);
    }

    // INVALIDATE PICKUP REQUEST
    public function deletePickupRequest(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $pickupRequest = PickupRequest::find($validatedData['pickup_id']);
        if (!$pickupRequest) {
            return response()->json(['error' => 'Pickup request not found'], 404);
        }
    
        $pickupRequest->update(['status_of_pickup' => 0]);
    
        return response()->json($pickupRequest->fresh(), 200);
    }

    //CHANGE STATUS OF LABEL AND PICKUP TO COMPLETED
    public function completePickupRequest(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required|integer',
            'completion_method' => 'required|string', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
    
        $pickupRequest = PickupRequest::find($validatedData['pickup_id']);
        if (!$pickupRequest) {
            return response()->json(['error' => 'Pickup request not found'], 404);
        }

        $pickupRequest->status_of_pickup = 1;
        $pickupRequest->pickup_date = now(); 
        $pickupRequest->completion_method = $validatedData['completion_method'];
        $pickupRequest->save();
    
        if ($pickupRequest->label) {
            $pickupRequest->label->status_of_label = 1;
            $pickupRequest->label->save();
        }
    
        return response()->json([
            'pickup_request' => $pickupRequest->fresh(), 
            'label' => $pickupRequest->label,
        ], 200);
    }

    // Searches Pickup Requests by Laboratory, Status, and Completion Method    
    public function searchPickupRequests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_name' => 'nullable|string', 
            'status_of_pickup' => 'nullable|integer|in:0,1,2,3',
            'completion_method' => 'nullable|string|in:Clean Out,Regular'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $query = PickupRequest::where('status_of_pickup', '!=', 0); 

        if (!empty($validatedData['building_name'])) {
            $query->whereHas('label.laboratory', function ($query) use ($validatedData) {
                $query->where('building_name', $validatedData['building_name']);
            });
        }

        if (isset($validatedData['status_of_pickup'])) {
            $query->where('status_of_pickup', $validatedData['status_of_pickup']);
        } else {
            $query->orderByRaw("FIELD(status_of_pickup, 3, 2, 1)"); 
        }

        if (!empty($validatedData['completion_method'])) {
            $query->where('completion_method', $validatedData['completion_method']);
        }

        $pickupRequests = $query->with(['label.laboratory', 'label.chemical', 'label.user'])->get();

        if ($pickupRequests->isEmpty()) {
            return response()->json(['message' => 'No pickup requests found for the specified criteria.'], 404);
        }

        $totalCount = $pickupRequests->count();

        $formattedPickupRequests = $pickupRequests->map(function ($pickup) {
            $sixMonthsFromStart = Carbon::parse($pickup->label->accumulation_start_date)->addMonths(6);
            
            return [
                'Pickup Request ID' => $pickup->id,
                'Label ID' => $pickup->label_id,
                'Requested By' => $pickup->label->laboratory->professor_investigator ?? 'N/A',
                'Request Date' => $pickup->created_at->format('Y-m-d'), 
                'Chemicals' => $pickup->label->chemical->chemical_name ?? 'N/A',
                'Building Name' => $pickup->label->laboratory->building_name ?? 'N/A',
                'Room Number' => $pickup->label->laboratory->room_number ?? 'N/A',
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
        })
        ->sortBy('pickup_due')
        ->values();

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

    // RETURNS DATABASE INFORMATION THROUGH FOREIGN KEYS
    public function getPickupDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required|integer|exists:pickup,id', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $pickupRequest = PickupRequest::with(['label', 'chemical', 'user', 'laboratory'])->find($validatedData['pickup_id']);
    
        if (!$pickupRequest) {
            return response()->json(['message' => 'Pickup request not found'], 404);
        }
    
        $data = [
            'Pickup ID' => $pickupRequest->id,
            'Label ID' => $pickupRequest->label ? $pickupRequest->label->id : null,
            'Chemical Name' => $pickupRequest->chemical ? $pickupRequest->chemical->chemical_name : null,
            'Building Name' => $pickupRequest->laboratory ? $pickupRequest->laboratory->building_name : null,
            'Room Number' => $pickupRequest->laboratory ? $pickupRequest->laboratory->room_number : null,
            'Container Size' => $pickupRequest->label ? $pickupRequest->label->container_size : null,
            'Request Date' => $pickupRequest->request_date,
            'Pickup Date' => $pickupRequest->pickup_date,
            'Status' => $pickupRequest->status_of_pickup,
        ];
    
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
