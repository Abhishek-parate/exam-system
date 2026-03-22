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
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // ══════════════════════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $roles = Role::all();
        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // ══════════════════════════════════════════════════════════
    //  CREATE
    // ══════════════════════════════════════════════════════════
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // ══════════════════════════════════════════════════════════
    //  STORE (single user)
    // ══════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile'            => ['nullable', 'string', 'max:20'],
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
            'role_name'         => ['required', 'exists:roles,name'],
            'is_active'         => ['nullable', 'boolean'],

            // Student extra fields
            'enrollment_number' => ['nullable', 'string', 'max:100', 'unique:students,enrollment_number'],
            'class'             => ['nullable', 'string', 'max:100'],
            'date_of_birth'     => ['nullable', 'date'],
            'target_exam'       => ['nullable', 'string', 'max:255'],
            'address'           => ['nullable', 'string', 'max:500'],
        ]);

        $role = Role::where('name', $validated['role_name'])->firstOrFail();

        DB::transaction(function () use ($validated, $request, $role) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'mobile'    => $validated['mobile'] ?? null,
                'password'  => Hash::make($validated['password']),
                'role_id'   => $role->id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            if ($role->name === 'student') {
                Student::create([
                    'user_id'           => $user->id,
                    'enrollment_number' => $validated['enrollment_number']
                                            ?? ('STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
                    'class'             => $validated['class'] ?? null,
                    'date_of_birth'     => $validated['date_of_birth'] ?? null,
                    'target_exam'       => $validated['target_exam'] ?? null,
                    'address'           => $validated['address'] ?? null,
                ]);
            } elseif ($role->name === 'teacher') {
                Teacher::create(['user_id' => $user->id]);
            }
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully!');
    }

    // ══════════════════════════════════════════════════════════
    //  SHOW
    // ══════════════════════════════════════════════════════════
    public function show(User $user)
    {
        $user->load('role', 'student', 'teacher');
        return view('admin.users.show', compact('user'));
    }

    // ══════════════════════════════════════════════════════════
    //  EDIT
    // ══════════════════════════════════════════════════════════
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('role', 'student', 'teacher');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // ══════════════════════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════════════════════
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile'    => ['nullable', 'string', 'max:20'],
            'password'  => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_name' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = Role::where('name', $validated['role_name'])->firstOrFail();

        DB::transaction(function () use ($validated, $request, $role, $user) {
            $data = [
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'mobile'    => $validated['mobile'] ?? null,
                'role_id'   => $role->id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            if ($role->name === 'student' && !$user->student) {
                Student::create([
                    'user_id'           => $user->id,
                    'enrollment_number' => 'STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                ]);
            }

            if ($role->name === 'teacher' && !$user->teacher) {
                Teacher::create(['user_id' => $user->id]);
            }
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully!');
    }

    // ══════════════════════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════════════════════
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully!');
    }

    // ══════════════════════════════════════════════════════════
    //  BULK TEMPLATE DOWNLOAD
    // ══════════════════════════════════════════════════════════
    public function bulkTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_import_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'name', 'email', 'mobile', 'role', 'password',
            'enrollment_number', 'class', 'date_of_birth', 'target_exam', 'address',
        ];

        $sampleRows = [
            ['John Doe',   'john@example.com',  '9876543210', 'student', 'Password@123', 'ENR-001', 'Class 10', '2005-01-15', 'JEE',   'Pune, Maharashtra'],
            ['Jane Smith', 'jane@example.com',  '9123456780', 'student', 'Password@123', 'ENR-002', 'Class 12', '2003-06-20', 'NEET',  'Mumbai, Maharashtra'],
            ['Mr. Kumar',  'kumar@example.com', '9000000001', 'teacher', 'Password@123', '',        '',         '',           '',      ''],
        ];

        $callback = function () use ($columns, $sampleRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ══════════════════════════════════════════════════════════
    //  BULK STORE  (CSV + manual rows)
    // ══════════════════════════════════════════════════════════
    public function bulkStore(Request $request)
    {
        $request->validate([
            'csv_file'     => ['nullable', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
            'default_role' => ['nullable', 'exists:roles,name'],
        ]);

        $created     = 0;
        $skipped     = 0;
        $bulkErrors  = [];

        DB::beginTransaction();

        try {
            // ── A: CSV Upload ──────────────────────────────────
            if ($request->hasFile('csv_file')) {
                $defaultRole = $request->input('default_role', 'student');
                $handle      = fopen($request->file('csv_file')->getRealPath(), 'r');
                $header      = array_map('trim', fgetcsv($handle)); // first row = headers

                $lineNo = 1;
                while (($row = fgetcsv($handle)) !== false) {
                    $lineNo++;
                    if (count($row) !== count($header)) continue; // skip malformed rows

                    $data = array_combine($header, array_map('trim', $row));

                    // Skip completely empty rows
                    if (empty($data['name']) && empty($data['email'])) { $skipped++; continue; }

                    // Skip duplicate emails silently
                    if (User::where('email', $data['email'])->exists()) {
                        $bulkErrors[] = "Line {$lineNo}: Email '{$data['email']}' already exists — skipped.";
                        $skipped++;
                        continue;
                    }

                    try {
                        $this->createUserFromArray($data, $defaultRole);
                        $created++;
                    } catch (\Throwable $e) {
                        $bulkErrors[] = "Line {$lineNo} ({$data['email']}): " . $e->getMessage();
                        $skipped++;
                    }
                }
                fclose($handle);
            }

            // ── B: Manual Rows ─────────────────────────────────
            if ($request->has('users')) {
                foreach ($request->input('users', []) as $i => $data) {
                    // Skip blank rows
                    if (empty(trim($data['name'] ?? '')) && empty(trim($data['email'] ?? ''))) {
                        $skipped++;
                        continue;
                    }

                    // Validate required fields per row
                    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                        $bulkErrors[] = "Row " . ($i + 1) . ": Name, Email and Password are required.";
                        $skipped++;
                        continue;
                    }

                    if (User::where('email', $data['email'])->exists()) {
                        $bulkErrors[] = "Row " . ($i + 1) . ": Email '{$data['email']}' already exists — skipped.";
                        $skipped++;
                        continue;
                    }

                    try {
                        $this->createUserFromArray($data, $data['role'] ?? 'student');
                        $created++;
                    } catch (\Throwable $e) {
                        $bulkErrors[] = "Row " . ($i + 1) . " ({$data['email']}): " . $e->getMessage();
                        $skipped++;
                    }
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Bulk import failed: ' . $e->getMessage())
                ->withInput();
        }

        $message = "{$created} user(s) imported successfully.";
        if ($skipped > 0) $message .= " {$skipped} row(s) skipped.";

        return redirect()->route('admin.users.index')
            ->with('success', $message)
            ->with('bulk_errors', $bulkErrors);
    }

    // ══════════════════════════════════════════════════════════
    //  PRIVATE HELPER — create one user from array
    // ══════════════════════════════════════════════════════════
    private function createUserFromArray(array $data, string $defaultRole = 'student'): User
    {
        $roleName = trim($data['role'] ?? $defaultRole) ?: $defaultRole;
        $role     = Role::where('name', $roleName)->firstOrFail();

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'mobile'    => $data['mobile'] ?? null,
            'password'  => Hash::make($data['password'] ?? Str::random(12)),
            'role_id'   => $role->id,
            'is_active' => 1,
        ]);

        if ($role->name === 'student') {
            Student::create([
                'user_id'           => $user->id,
                'enrollment_number' => !empty($data['enrollment_number'])
                                        ? $data['enrollment_number']
                                        : ('STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
                'class'             => $data['class'] ?? null,
                'date_of_birth'     => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'target_exam'       => $data['target_exam'] ?? null,
                'address'           => $data['address'] ?? null,
            ]);
        } elseif ($role->name === 'teacher') {
            Teacher::create(['user_id' => $user->id]);
        }

        return $user;
    }
}
