<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Image::query();

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        // Apply search filter if search parameter exists
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'ILIKE', "%{$searchTerm}%");
        }

        $images = $query->latest()->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('gallery._images', compact('images'))->render()
            ]);
        }

        return view('gallery.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255'
        ]);

        try {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Create safe filename
            $newName = Str::slug($request->name) . '.' . $extension;

            // Store the file
            $path = $file->storeAs('images', $newName, 'public');

            // Create image record
            $image = new Image([
                'store_id' => auth()->user()->hasGlobalAccess()
                    ? $request->store_id
                    : auth()->user()->current_store_id,
                'name' => $request->name,
                'path' => $path,
                'original_name' => $originalName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            $image->save();

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => $image
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Image $image)
    {
        try {
            // Check if user has access to this image
            if (!auth()->user()->hasGlobalAccess() &&
                $image->store_id !== auth()->user()->current_store_id) {
                throw new \Exception('Unauthorized access');
            }

            // Delete file from storage
            Storage::disk('public')->delete($image->path);

            // Delete database record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function images(Request $request)
    {
        $query = Image::query();

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        // Apply search filter if search parameter exists
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'ILIKE', "%{$searchTerm}%");
        }

        $images = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $images,
            'html' => view('gallery._modal_images', compact('images'))->render()
        ]);
    }
}
