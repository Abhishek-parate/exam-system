<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $roles = Role::all();
        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role_name' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = Role::where('name', $validated['role_name'])->firstOrFail();

        // Wrap in a transaction so user + profile are always created together
        DB::transaction(function () use ($validated, $request, $role) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role_id'   => $role->id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Auto-create the linked profile based on role
            if ($role->name === 'student') {
                Student::create([
                    'user_id'           => $user->id,
                    'enrollment_number' => 'STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                    // Other nullable fields (class, dob, address, target_exam)
                    // will be filled later from the student's profile edit page
                ]);
            } elseif ($role->name === 'teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                ]);
            }
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load('role', 'student', 'teacher');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('role');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'  => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_name' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = Role::where('name', $validated['role_name'])->firstOrFail();

        DB::transaction(function () use ($validated, $request, $role, $user) {
            $updateData = [
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'role_id'   => $role->id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // If role changed to student and no student profile exists yet, create one
            if ($role->name === 'student' && ! $user->student) {
                Student::create([
                    'user_id'           => $user->id,
                    'enrollment_number' => 'STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                ]);
            }

            // If role changed to teacher and no teacher profile exists yet, create one
            if ($role->name === 'teacher' && ! $user->teacher) {
                Teacher::create([
                    'user_id' => $user->id,
                ]);
            }
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully!');
    }
}