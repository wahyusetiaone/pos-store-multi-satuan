<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasGlobalAccess()) {
            $stores = Store::paginate(15);
        } else {
            $stores = Auth::user()->stores()->paginate(15);
        }
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Unauthorized action.');
        }
        return view('stores.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stores,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('stores/logos', 'public');
            $validated['logo'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $store = Store::create($validated);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Toko berhasil ditambahkan.');
    }

    public function show(Store $store)
    {
        if (!Auth::user()->canAccessStore($store->id)) {
            abort(403, 'Unauthorized action.');
        }

        $store->load(['products', 'sales', 'users']);
        return view('stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stores,email,' . $store->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $path = $request->file('logo')->store('stores/logos', 'public');
            $validated['logo'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $store->update($validated);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy(Store $store)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }

        $store->delete();

        return redirect()
            ->route('stores.index')
            ->with('success', 'Toko berhasil dihapus.');
    }

    // Method baru untuk manajemen user toko
    public function users(Store $store)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil user yang belum di-assign ke toko ini dan bukan owner/purchasing
        $availableUsers = User::whereNotIn('role', ['owner', 'purchasing'])
            ->whereDoesntHave('stores', function($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->get();

        return view('stores.users', compact('store', 'availableUsers'));
    }

    public function assignUser(Request $request, Store $store)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Pastikan user bukan owner/purchasing
        if (in_array($user->role, ['owner', 'purchasing'])) {
            return redirect()
                ->route('stores.users', $store->id)
                ->with('error', 'Owner dan Purchasing tidak perlu di-assign ke toko tertentu.');
        }

        // Assign user ke toko
        $store->users()->attach($validated['user_id']);

        // Set current_store_id jika user belum punya toko aktif
        if (!$user->current_store_id) {
            $user->current_store_id = $store->id;
            $user->save();
        }

        return redirect()
            ->route('stores.users', $store->id)
            ->with('success', 'User berhasil ditambahkan ke toko.');
    }

    public function removeUser(Store $store, User $user)
    {
        if (!Auth::user()->hasGlobalAccess()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan user bukan owner/purchasing
        if (in_array($user->role, ['owner', 'purchasing'])) {
            return redirect()
                ->route('stores.users', $store->id)
                ->with('error', 'Owner dan Purchasing tidak bisa dihapus dari toko.');
        }

        // Remove user dari toko
        $store->users()->detach($user->id);

        // Reset current_store_id jika ini adalah toko aktif user
        if ($user->current_store_id === $store->id) {
            $user->current_store_id = $user->stores()->first()?->id;
            $user->save();
        }

        return redirect()
            ->route('stores.users', $store->id)
            ->with('success', 'User berhasil dihapus dari toko.');
    }

    public function select()
    {
        $user = auth()->user();

        // If user has global access, show all active stores
        if ($user->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        } else {
            // Otherwise, show only stores the user has access to
            $stores = $user->stores()->where('is_active', true)->get();
        }

        return view('stores.select', compact('stores'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id'
        ]);

        $user = auth()->user();
        $storeId = $request->store_id;

        // Check if user can access this store
        if (!$user->canAccessStore($storeId)) {
            return back()->with('error', 'Anda tidak memiliki akses ke toko ini.');
        }

        // Update user's current store
        $user->update(['current_store_id' => $storeId]);
        session(['current_store_id' => $storeId]);

        return redirect()->route('index')->with('success', 'Berhasil beralih toko.');
    }
}
