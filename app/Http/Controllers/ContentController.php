<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;

class ContentController extends Controller
{
    // Store content for a label (a subtable that map chemicals in each label)
    public function store(Request $request)
    {
        $request->validate([
            'label_id' => 'required|integer|exists:labels,id',
            'chemical_name' => 'required|string',
            'cas_number' => 'required|string',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $content = Content::create([
            'label_id' => $request->label_id,
            'chemical_name' => $request->chemical_name,
            'cas_number' => $request->cas_number,
            'percentage' => $request->percentage,
        ]);

        return response()->json(['success' => true, 'data' => $content], 201);
    }
}
