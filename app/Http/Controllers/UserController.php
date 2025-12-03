<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        
        // Get users with pagination, ordered by role and name
        $users = User::where('id', '!=', auth()->id())
                    ->orderByRaw("FIELD(role, 'admin', 'coadmin', 'user')")
                    ->orderBy('name')
                    ->paginate($perPage);
        
        // Count users by role
        $roleCounts = [
            'admin' => User::where('role', 'admin')->count(),
            'coadmin' => User::where('role', 'coadmin')->count(),
            'user' => User::where('role', 'user')->count(),
        ];
        
        // Separate users by role for the current page
        $admins = $users->where('role', 'admin');
        $coadmins = $users->where('role', 'coadmin');
        $regularUsers = $users->where('role', 'user');

        return view('users.index', compact(
            'users', 
            'roleCounts', 
            'admins', 
            'coadmins', 
            'regularUsers'
        ));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,coadmin,user',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500'
        ]);

        // Gunakan create method dengan benar
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio']
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function updateRole(Request $request, $id)
    {
        // Cari user by ID
        $user = User::findOrFail($id);
        
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $request->validate([
            'role' => 'required|in:admin,coadmin,user'
        ]);

        // Update role
        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function destroy($id)
    {
        // Cari user by ID
        $user = User::findOrFail($id);
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}