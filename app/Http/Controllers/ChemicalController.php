<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChemicalController extends Controller
{

// ------------------------------------------------------------------------------------------------------
// ---------------------------------------------API METHODS----------------------------------------------
// ------------------------------------------------------------------------------------------------------

    public function getAll() //GET ALL
    {
        $chemicals = Chemical::all();
        return response()->json($chemicals, 200);
    }
    
    public function get($id) //GET ID
    {
        $chemical = Chemical::find($id);
    
        if (!$chemical) {
            return response()->json(['message' => 'Chemical not found'], 404);
        }
    
        return response()->json($chemical, 200);
    }

    public function create(Request $request) //POST
    {
        $validatedData = $request->validate([
            'chemical_name' => 'required|string|max:255',
            'cas_number' => 'required|string|max:255',
            'status_of_chemical' => 'required|integer|in: 0,1',  //0 = Inactive 1= Active
        ]);
    
        $chemical = Chemical::create($validatedData);
    
        return response()->json($chemical, 201);
    }

    public function update(Request $request, $id) //PUT
    {
        $chemical = Chemical::find($id);
    
        if (!$chemical) {
            return response()->json(['message' => 'Chemical not found'], 404);
        }
    
        $validatedData = $request->validate([
            'chemical_name' => 'required|string|max:255',
            'cas_number' => 'required|string|max:255',
            'status_of_chemical' => 'required|integer|in: 0,1',  //0 = Inactive 1= Active
        ]);
    
        $chemical->update($validatedData);
    
        return response()->json($chemical, 200);
    }

    public function destroy($id) //DELETE
    {
        $chemical = Chemical::find($id);
    
        if (!$chemical) {
            return response()->json(['message' => 'Chemical not found'], 404);
        }
    
        $chemical->delete();
        return response()->json(['message' => 'Chemical deleted successfully'], 200);
    }
    
// ------------------------------------------------------------------------------------------------------
// -------------------------------------------FRONTEND METHODS-------------------------------------------
// ------------------------------------------------------------------------------------------------------ 

    // CREATE A NEW CHEMICAL    
    public function addChemical(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'chemical_name' => 'required|string|max:255',
            'cas_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $existingChemical = Chemical::where('chemical_name', $validatedData['chemical_name'])
            ->where('cas_number', $validatedData['cas_number'])
            ->first();

        if ($existingChemical) {
            return response()->json(['message' => 'Chemical Already Exists.'], 409);
        }

        $chemical = Chemical::create(array_merge($validatedData, ['status_of_chemical' => '1']));

        return response()->json($chemical, 201);
    }
  
    
    // CHANGE STATUS TO "INVALID"
    public function deleteChemical(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'chemical_id' => 'required|integer|exists:chemical,id', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $chemical = Chemical::find($validatedData['chemical_id']);
    
        $chemical->update(['status_of_chemical' => 0]);
    
        return response()->json($chemical->fresh(), 200);
    }

    // MODIFY CHEMICAL NAME OR CAS NUMBER
    public function editChemical(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'chemical_id' => 'required|integer|exists:chemical,id', 
            'chemical_name' => 'nullable|string|max:255',
            'cas_number' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        if (empty($validatedData['chemical_name']) && empty($validatedData['cas_number'])) {
            return response()->json(['message' => 'Either chemical_name or cas_number must be provided'], 400);
        }
    
        $chemical = Chemical::find($validatedData['chemical_id']);
    
        $chemical->update(array_filter($validatedData));
    
        return response()->json(['chemical' => $chemical], 200);
    }

    // RETURNS ALL CHEMICALS WITH GIVEN NAME
    public function searchChemicalName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chemical_name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $chemicals = Chemical::where('status_of_chemical', 1)
        ->whereRaw('LOWER(chemical_name) LIKE ?', ['%' . strtolower($validatedData['chemical_name']) . '%'])
        ->get(['id', 'chemical_name', 'cas_number']);
    
        if ($chemicals->isEmpty()) {
            return response()->json(['message' => 'No active chemicals found with the specified name.'], 404);
        }
    
        return response()->json($chemicals, 200);
    }    

    //COUNTS ALL CHEMICALS MADE THIS Month
    public function chemicalsMadeThisMonth() 
    {
        $oneMonthAgo = now()->subMonth(); 
        $today = now(); 
    
        $chemicalCount = Chemical::whereBetween('created_at', [$oneMonthAgo, $today])->count();
    
        return response()->json(['chemicals_created_in_the_last_30_days' => $chemicalCount], 200);
    }

    // RETURNS CAS NUMBER FOR CHEMICAL NAME
    public function getCasNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chemical_name' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        $chemical = Chemical::where('chemical_name', $validatedData['chemical_name'])->first();
    
        if (!$chemical) {
            return response()->json(['message' => 'Chemical not found'], 404);
        }
    
        return response()->json(['cas_number' => $chemical->cas_number], 200);
    }

    public function getAllChemicals()
    {
        $chemicals = Chemical::select('chemical_name', 'cas_number')
                             ->where('status_of_chemical', 'active') // Optional: only get active chemicals
                             ->get();

        return response()->json($chemicals);
    }

    // Get CAS number by chemical name for autofill
    public function getCASNumberByChemicalName($chemical_name)
    {
        $chemical = Chemical::where('chemical_name', $chemical_name)->first();

        if ($chemical) {
            return response()->json($chemical);
        } else {
            return response()->json(['error' => 'Chemical not found'], 404);
        }
    }

    public function index()
{
    $chemicals = Chemical::all(); // Assuming `Chemical` is the model
    return response()->json($chemicals);
}

}
