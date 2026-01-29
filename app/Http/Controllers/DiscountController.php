<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Discount::query();

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, exclude deleted
            $query->notDeleted();
        }

        $discounts = $query->orderBy('created_at', 'asc')->paginate(10)->withQueryString();
        $i = ($discounts->currentPage() - 1) * $discounts->perPage();

        return view('discounts.index', compact('discounts', 'i'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('discounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,amount',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);

        Discount::create($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        return view('discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code,' . $discount->id,
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,amount',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);

        $discount->update($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        // Soft delete by updating status to 'deleted'
        $discount->update(['status' => 'deleted']);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }

    /**
     * Toggle discount status
     */
    public function toggleStatus(Discount $discount)
    {
        $newStatus = $discount->status === 'active' ? 'inactive' : 'active';
        $discount->update(['status' => $newStatus]);

        return redirect()->route('discounts.index')->with([
            'success' => true,
            'status' => $discount->status,
            'message' => 'Discount status updated successfully.'
        ]);
    }
}
