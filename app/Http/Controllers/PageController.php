<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Display the specified page.
     */
    public function show(Page $page)
    {
        return view('frontend.pages.show', compact('page'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('pages/gallery', 'public');
            }
        }

        $validated['gallery_images'] = $galleryImages;
        $validated['status'] = $request->status ?? 'active';

        Page::create($validated);
        return redirect()->route('pages.index')->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $currentGalleryImages = $page->gallery_images ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $currentGalleryImages[] = $image->store('pages/gallery', 'public');
            }
        }

        $validated['gallery_images'] = $currentGalleryImages;
        $validated['status'] = $request->status ?? 'active';

        $page->update($validated);
        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        if ($page->gallery_images) {
            foreach ($page->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully.');
    }

    /**
     * Toggle the page status.
     */
    public function toggleStatus(Page $page)
    {
        $page->status = $page->status === 'active' ? 'inactive' : 'active';
        $page->save();

        return response()->json([
            'success' => true,
            'status' => $page->status,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Delete a single gallery image.
     */
    public function deleteGalleryImage(Request $request, Page $page)
    {
        $imagePath = $request->image_path;
        $images = $page->gallery_images;

        if (($key = array_search($imagePath, $images)) !== false) {
            unset($images[$key]);
            Storage::disk('public')->delete($imagePath);
            $page->gallery_images = array_values($images);
            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found'
        ], 404);
    }
}
