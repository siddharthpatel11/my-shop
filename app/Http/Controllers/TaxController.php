<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::where('status', '!=', 'deleted')->orderBy('created_at', 'asc')->paginate(10);
        return view('taxes.index', compact('taxes'));
    }
    public function create()
    {
        return view('taxes.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,deleted',
        ]);

        Tax::create($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax created successfully.');
    }
    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $tax->update($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax updated successfully.');
    }
    public function destroy(Tax $tax)
    {
        $tax->update(['status' => 'deleted']);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
    public function toggleStatus(Tax $tax)
    {
        $newStatus = $tax->status === 'active' ? 'inactive' : 'active';
        $tax->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Tax status updated successfully.'
        ]);
    }
}
