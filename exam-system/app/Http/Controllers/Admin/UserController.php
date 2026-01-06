<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentModel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.users.create', compact('roles', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        DB::beginTransaction();
        try {
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->has('is_active');

            $user = User::create($validated);

            // Create role-specific record
            $role = Role::find($validated['role_id']);
            $teacher = $this->createRoleSpecificRecord($user, $role->name);

            // Assign subjects to teacher
            if ($role->name === 'teacher' && $teacher && !empty($request->subject_ids)) {
                $teacher->subjects()->sync($request->subject_ids);
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                           ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load(['role', 'student', 'teacher.subjects', 'parent']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $subjects = Subject::where('is_active', true)->get();
        
        // FIXED: Get assigned subject IDs directly from database
        $assignedSubjectIds = [];
        
        // Get teacher record
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if ($teacher) {
            // Query directly from pivot table
            $assignedSubjectIds = DB::table('teacher_subject')
                ->where('teacher_id', $teacher->id)
                ->pluck('subject_id')
                ->toArray();
            
            // Convert to integers to ensure matching
            $assignedSubjectIds = array_map('intval', $assignedSubjectIds);
            
            Log::info('EDIT PAGE - Assigned Subjects:', [
                'user_id' => $user->id,
                'teacher_id' => $teacher->id,
                'assigned_subject_ids' => $assignedSubjectIds
            ]);
        }
        
        // Load user with role
        $user->load('role');
        
        return view('admin.users.edit', compact('user', 'roles', 'subjects', 'assignedSubjectIds'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        DB::beginTransaction();
        try {
            // Handle password
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->has('is_active');
            
            $oldRoleId = $user->role_id;
            $newRoleId = $validated['role_id'];
            
            // Update user
            $user->update($validated);

            // Get or create teacher record
            $teacher = null;
            if ($oldRoleId != $newRoleId) {
                // Role changed
                if ($user->student) $user->student->delete();
                if ($user->teacher) {
                    $user->teacher->subjects()->detach();
                    $user->teacher->delete();
                }
                if ($user->parent) $user->parent->delete();
                
                $newRole = Role::find($newRoleId);
                $teacher = $this->createRoleSpecificRecord($user, $newRole->name);
            } else {
                // Same role
                $teacher = Teacher::where('user_id', $user->id)->first();
            }

            // Sync subjects
            $currentRole = Role::find($newRoleId);
            if ($currentRole->name === 'teacher' && $teacher) {
                $subjectIds = $request->input('subject_ids', []);
                
                // Clear and re-add
                DB::table('teacher_subject')->where('teacher_id', $teacher->id)->delete();
                
                if (!empty($subjectIds)) {
                    foreach ($subjectIds as $subjectId) {
                        DB::table('teacher_subject')->insert([
                            'teacher_id' => $teacher->id,
                            'subject_id' => $subjectId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                Log::info('UPDATE - Subjects Saved:', [
                    'teacher_id' => $teacher->id,
                    'subject_ids' => $subjectIds
                ]);
            }

            DB::commit();
            return redirect()->route('admin.users.index')
                           ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        DB::beginTransaction();
        try {
            if ($user->student) $user->student->delete();
            if ($user->teacher) {
                DB::table('teacher_subject')->where('teacher_id', $user->teacher->id)->delete();
                $user->teacher->delete();
            }
            if ($user->parent) $user->parent->delete();
            
            $user->delete();
            
            DB::commit();
            return redirect()->route('admin.users.index')
                           ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    private function createRoleSpecificRecord(User $user, string $roleName)
    {
        switch (strtolower($roleName)) {
            case 'student':
                if (!$user->student) {
                    Student::create([
                        'user_id' => $user->id,
                        'enrollment_number' => 'STU' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    ]);
                }
                return null;
                
            case 'teacher':
                $existingTeacher = Teacher::where('user_id', $user->id)->first();
                if ($existingTeacher) {
                    return $existingTeacher;
                }
                
                $employeeCode = 'TCH' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                return Teacher::create([
                    'user_id' => $user->id,
                    'employee_id' => $employeeCode,
                    'employee_code' => $employeeCode,
                ]);
                
            case 'parent':
                if (!$user->parent) {
                    ParentModel::create([
                        'user_id' => $user->id,
                    ]);
                }
                return null;
        }
        
        return null;
    }
}
