@extends('layouts.admin')
@section('title', 'Add New User')

@section('content')
<style>
  * { -webkit-font-smoothing: antialiased; }
  .tab-btn.active { background: #111827; color: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }
  .bulk-row { animation: fadeUp .2s ease both; }
  @keyframes fadeUp { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }
  input[type="file"]::file-selector-button {
    background: #f3f4f6; border: 1px solid #d1d5db; color: #374151;
    padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: background .15s;
  }
  input[type="file"]::file-selector-button:hover { background: #e5e7eb; }
</style>

<div class="min-h-screen bg-slate-50">

  {{-- ── Sticky Topbar ── --}}
  <div class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-2.5 flex items-center justify-between gap-4">
      <nav class="flex items-center gap-1.5 text-xs text-gray-400">
        <i class="fas fa-home text-gray-300"></i>
        <i class="fas fa-chevron-right text-[8px] text-gray-200"></i>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-indigo-600 transition">Users</a>
        <i class="fas fa-chevron-right text-[8px] text-gray-200"></i>
        <span class="text-indigo-600 font-semibold">Add New</span>
      </nav>
      <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-xs text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-medium transition">
        <i class="fas fa-arrow-left text-[10px]"></i> Back to Users
      </a>
    </div>
  </div>

  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    {{-- ── Page Header ── --}}
    <div class="mb-7">
      <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add Users</h1>
      <p class="text-sm text-gray-500 mt-1">Create a single account or import multiple users at once.</p>
    </div>

    {{-- ── Validation Errors ── --}}
    @if($errors->any())
      <div class="mb-6 bg-rose-50 border border-rose-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
          <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
          <div>
            <p class="text-sm font-semibold text-rose-700 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-xs text-rose-600 space-y-1">
              @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        </div>
      </div>
    @endif

    {{-- ── Mode Tabs ── --}}
    <div class="flex items-center bg-white border border-gray-200 rounded-xl p-1 gap-1 shadow-sm mb-6 w-fit">
      <button type="button" onclick="switchTab('single')" id="tab-single"
        class="tab-btn active inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all">
        <i class="fas fa-user-plus text-[11px]"></i> Single User
      </button>
      <button type="button" onclick="switchTab('bulk')" id="tab-bulk"
        class="tab-btn text-gray-500 hover:bg-gray-50 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition-all">
        <i class="fas fa-users text-[11px]"></i> Bulk Import
      </button>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- TAB: SINGLE USER                         --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="panel-single" class="tab-panel active space-y-5">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        {{-- Account Information --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
              <i class="fas fa-user text-indigo-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Account Information</p>
              <p class="text-xs text-gray-400">Basic details for the new user</p>
            </div>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Name --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Full Name <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="text" name="name" value="{{ old('name') }}" required
                  placeholder="e.g. John Doe"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400 transition placeholder-gray-300 @error('name') border-rose-400 @enderror"/>
              </div>
              @error('name')<p class="text-rose-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i>{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Email Address <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="email" name="email" value="{{ old('email') }}" required
                  placeholder="e.g. john@example.com"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400 transition placeholder-gray-300 @error('email') border-rose-400 @enderror"/>
              </div>
              @error('email')<p class="text-rose-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i>{{ $message }}</p>@enderror
            </div>

            {{-- Mobile --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Mobile</label>
              <div class="relative">
                <i class="fas fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="text" name="mobile" value="{{ old('mobile') }}"
                  placeholder="e.g. 9876543210"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400 transition placeholder-gray-300"/>
              </div>
              @error('mobile')<p class="text-rose-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i>{{ $message }}</p>@enderror
            </div>

            {{-- Role --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Role <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <i class="fas fa-shield-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <select name="role_name" id="role_name" required
                  onchange="toggleStudentFields(this.value)"
                  class="w-full pl-9 pr-10 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400 appearance-none transition @error('role_name') border-rose-400 @enderror">
                  <option value="">Select Role</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role_name') == $role->name ? 'selected' : '' }}>
                      {{ ucfirst($role->display_name ?? $role->name) }}
                    </option>
                  @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
              </div>
              @error('role_name')<p class="text-rose-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i>{{ $message }}</p>@enderror
            </div>

          </div>

          {{-- Active toggle --}}
          <div class="px-6 pb-5">
            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
              <div class="relative">
                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer" id="is_active_toggle">
                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors"></div>
                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
              </div>
              <span class="text-sm font-semibold text-gray-700">Active Account</span>
              <span class="text-xs text-gray-400">User can log in immediately</span>
            </label>
          </div>
        </div>

        {{-- Student Extra Fields (conditional) --}}
        <div id="student-fields" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ old('role_name') === 'student' ? '' : 'hidden' }}">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
              <i class="fas fa-user-graduate text-emerald-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Student Details</p>
              <p class="text-xs text-gray-400">Additional information for student accounts</p>
            </div>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Enrollment Number</label>
              <div class="relative">
                <i class="fas fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="text" name="enrollment_number" value="{{ old('enrollment_number') }}"
                  placeholder="e.g. ENR-2024-001"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:border-emerald-400 transition placeholder-gray-300"/>
              </div>
              @error('enrollment_number')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Class / Batch</label>
              <div class="relative">
                <i class="fas fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="text" name="class" value="{{ old('class') }}"
                  placeholder="e.g. Class 10 / Batch A"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:border-emerald-400 transition placeholder-gray-300"/>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Date of Birth</label>
              <div class="relative">
                <i class="fas fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:border-emerald-400 transition"/>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Target Exam</label>
              <div class="relative">
                <i class="fas fa-bullseye absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="text" name="target_exam" value="{{ old('target_exam') }}"
                  placeholder="e.g. JEE, NEET, UPSC"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:border-emerald-400 transition placeholder-gray-300"/>
              </div>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Address</label>
              <div class="relative">
                <i class="fas fa-map-marker-alt absolute left-3.5 top-3.5 text-gray-300 text-xs pointer-events-none"></i>
                <textarea name="address" rows="2"
                  placeholder="Student's full address"
                  class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:border-emerald-400 transition placeholder-gray-300 resize-none">{{ old('address') }}</textarea>
              </div>
            </div>

          </div>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
              <i class="fas fa-lock text-amber-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Set Password</p>
              <p class="text-xs text-gray-400">Minimum 8 characters</p>
            </div>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Password <span class="text-rose-500">*</span></label>
              <div class="relative">
                <i class="fas fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="password" name="password" id="pwd" required
                  placeholder="Min. 8 characters"
                  class="w-full pl-9 pr-10 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400/60 focus:border-amber-400 transition placeholder-gray-300 @error('password') border-rose-400 @enderror"/>
                <button type="button" onclick="togglePwd('pwd','eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition">
                  <i id="eye1" class="fas fa-eye text-xs"></i>
                </button>
              </div>
              @error('password')<p class="text-rose-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i>{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Confirm Password <span class="text-rose-500">*</span></label>
              <div class="relative">
                <i class="fas fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <input type="password" name="password_confirmation" id="pwd2" required
                  placeholder="Repeat password"
                  class="w-full pl-9 pr-10 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400/60 focus:border-amber-400 transition placeholder-gray-300"/>
                <button type="button" onclick="togglePwd('pwd2','eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition">
                  <i id="eye2" class="fas fa-eye text-xs"></i>
                </button>
              </div>
            </div>

          </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
          <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 font-medium transition">
            <i class="fas fa-times text-xs"></i> Cancel
          </a>
          <button type="submit"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-user-plus text-xs"></i> Create User
          </button>
        </div>

      </form>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- TAB: BULK IMPORT                         --}}
    {{-- ════════════════════════════════════════ --}}
    <div id="panel-bulk" class="tab-panel space-y-5">
      <form method="POST" action="{{ route('admin.users.bulk-store') }}" enctype="multipart/form-data" id="bulk-form">
        @csrf

        {{-- Option A: CSV Upload --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
              <i class="fas fa-file-csv text-teal-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Option A — Upload CSV / Excel</p>
              <p class="text-xs text-gray-400">Upload a file to import multiple users at once</p>
            </div>
          </div>
          <div class="p-6">

            {{-- Download Template --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
              <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-indigo-500 mt-0.5"></i>
                <div>
                  <p class="text-xs font-semibold text-indigo-700">Download the CSV template first</p>
                  <p class="text-[11px] text-indigo-400 mt-0.5">
                    Required columns: <span class="font-mono bg-indigo-100 px-1 py-0.5 rounded text-indigo-700">name, email, mobile, role, password</span>
                    &nbsp;·&nbsp; Optional: <span class="font-mono bg-indigo-100 px-1 py-0.5 rounded text-indigo-700">enrollment_number, class, date_of_birth, target_exam, address</span>
                  </p>
                </div>
              </div>
              <a href="{{ route('admin.users.bulk-template') }}"
                class="inline-flex items-center gap-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-semibold transition shadow-sm shrink-0">
                <i class="fas fa-download text-[10px]"></i> Download Template
              </a>
            </div>

            {{-- File Input --}}
            <div id="csv-dropzone"
              class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-indigo-300 hover:bg-indigo-50/30 transition-all cursor-pointer"
              onclick="document.getElementById('csv_file').click()"
              ondragover="event.preventDefault();this.classList.add('border-indigo-400','bg-indigo-50/50')"
              ondragleave="this.classList.remove('border-indigo-400','bg-indigo-50/50')"
              ondrop="handleDrop(event)">
              <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-3"></i>
              <p class="text-sm font-semibold text-gray-600">Drop your file here, or <span class="text-indigo-600">browse</span></p>
              <p class="text-xs text-gray-400 mt-1">Supports .csv, .xlsx — Max 5MB</p>
              <p id="file-name" class="text-xs text-indigo-600 font-medium mt-2 hidden"></p>
              <input type="file" id="csv_file" name="csv_file" accept=".csv,.xlsx"
                class="hidden" onchange="showFileName(this)"/>
            </div>

            {{-- Role for CSV --}}
            <div class="mt-4 max-w-xs">
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Default Role <span class="text-rose-500">*</span>
                <span class="normal-case font-normal text-gray-400">(used if CSV has no role column)</span>
              </label>
              <div class="relative">
                <i class="fas fa-shield-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                <select name="default_role" required
                  class="w-full pl-9 pr-10 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/60 focus:border-indigo-400 appearance-none transition">
                  <option value="">Select Default Role</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->display_name ?? $role->name) }}</option>
                  @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
              </div>
            </div>

          </div>
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-3">
          <div class="flex-1 border-t border-gray-200"></div>
          <span class="text-xs text-gray-400 font-semibold bg-slate-50 px-3 py-1 rounded-full border border-gray-200">OR</span>
          <div class="flex-1 border-t border-gray-200"></div>
        </div>

        {{-- Option B: Manual Multi-row --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                <i class="fas fa-table text-violet-600 text-sm"></i>
              </div>
              <div>
                <p class="text-sm font-bold text-gray-900">Option B — Enter Manually</p>
                <p class="text-xs text-gray-400">Add up to 50 users in a table form</p>
              </div>
            </div>
            <button type="button" onclick="addBulkRow()"
              class="inline-flex items-center gap-1.5 text-xs bg-violet-600 hover:bg-violet-700 text-white px-3 py-1.5 rounded-lg font-semibold transition shadow-sm">
              <i class="fas fa-plus text-[10px]"></i> Add Row
            </button>
          </div>

          {{-- Table Header --}}
          <div class="overflow-x-auto">
            <table class="w-full text-xs">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide w-8">#</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide min-w-[160px]">Full Name *</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide min-w-[190px]">Email *</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide min-w-[130px]">Mobile</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide min-w-[130px]">Role *</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide min-w-[140px]">Password *</th>
                  <th class="px-3 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wide w-10">Del</th>
                </tr>
              </thead>
              <tbody id="bulk-table-body">
                {{-- JS renders rows here --}}
              </tbody>
            </table>
          </div>

          <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
              <span id="row-count" class="font-semibold text-gray-600">0</span> rows added
            </p>
            <button type="button" onclick="addBulkRow()"
              class="inline-flex items-center gap-1.5 text-xs text-violet-600 hover:text-violet-800 font-semibold transition">
              <i class="fas fa-plus text-[10px]"></i> Add Another Row
            </button>
          </div>
        </div>

        {{-- Bulk Actions --}}
        <div class="flex items-center justify-between bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
          <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 font-medium transition">
            <i class="fas fa-times text-xs"></i> Cancel
          </a>
          <div class="flex items-center gap-3">
            <button type="button" onclick="clearBulkRows()"
              class="inline-flex items-center gap-2 text-xs text-gray-500 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl font-semibold transition">
              <i class="fas fa-trash-alt text-[10px]"></i> Clear All Rows
            </button>
            <button type="submit" id="bulk-submit"
              class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
              <i class="fas fa-users text-xs"></i> Import Users
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</div>

@push('scripts')
<script>
  /* ── Tab Switch ── */
  function switchTab(tab) {
    ['single','bulk'].forEach(t => {
      document.getElementById('tab-'  + t).classList.toggle('active', t === tab);
      document.getElementById('panel-'+ t).classList.toggle('active', t === tab);
      if (t !== tab) document.getElementById('tab-' + t).classList.add('text-gray-500');
      else document.getElementById('tab-' + t).classList.remove('text-gray-500');
    });
  }

  /* ── Show/Hide Student Fields ── */
  function toggleStudentFields(role) {
    const el = document.getElementById('student-fields');
    if (!el) return;
    el.classList.toggle('hidden', role !== 'student');
  }

  /* ── Password Eye Toggle ── */
  function togglePwd(inputId, eyeId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    if (!inp || !eye) return;
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    eye.className = isText ? 'fas fa-eye text-xs' : 'fas fa-eye-slash text-xs';
  }

  /* ── Bulk Table ── */
  let rowIndex = 0;
  const roles  = @json($roles->pluck('name', 'name'));

  function buildRoleOptions(name) {
    let html = `<option value="">Role *</option>`;
    Object.entries(roles).forEach(([val, label]) => {
      html += `<option value="${val}">${label.charAt(0).toUpperCase() + label.slice(1)}</option>`;
    });
    return html;
  }

  function addBulkRow() {
    const tbody = document.getElementById('bulk-table-body');
    const i     = rowIndex++;
    const tr    = document.createElement('tr');
    tr.id       = 'brow-' + i;
    tr.className= 'bulk-row border-b border-gray-50 hover:bg-gray-50/50 transition-colors';
    tr.innerHTML = `
      <td class="px-3 py-2 text-gray-400 text-[11px] font-mono">${rowIndex}</td>
      <td class="px-3 py-2">
        <input type="text" name="users[${i}][name]" placeholder="Full Name" required
          class="w-full px-2.5 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition placeholder-gray-300"/>
      </td>
      <td class="px-3 py-2">
        <input type="email" name="users[${i}][email]" placeholder="Email" required
          class="w-full px-2.5 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition placeholder-gray-300"/>
      </td>
      <td class="px-3 py-2">
        <input type="text" name="users[${i}][mobile]" placeholder="Mobile"
          class="w-full px-2.5 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition placeholder-gray-300"/>
      </td>
      <td class="px-3 py-2">
        <select name="users[${i}][role]" required
          class="w-full px-2.5 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-violet-400 focus:border-violet-400 appearance-none transition">
          ${buildRoleOptions()}
        </select>
      </td>
      <td class="px-3 py-2">
        <input type="text" name="users[${i}][password]" placeholder="Password" required
          class="w-full px-2.5 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition placeholder-gray-300"/>
      </td>
      <td class="px-3 py-2 text-center">
        <button type="button" onclick="removeBulkRow(${i})"
          class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center mx-auto transition">
          <i class="fas fa-times text-[10px]"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    updateRowCount();
  }

  function removeBulkRow(i) {
    const row = document.getElementById('brow-' + i);
    if (row) row.remove();
    updateRowCount();
  }

  function clearBulkRows() {
    document.getElementById('bulk-table-body').innerHTML = '';
    rowIndex = 0;
    updateRowCount();
  }

  function updateRowCount() {
    const cnt = document.getElementById('bulk-table-body').querySelectorAll('tr').length;
    document.getElementById('row-count').textContent = cnt;
  }

  /* ── CSV Drag & Drop ── */
  function handleDrop(e) {
    e.preventDefault();
    const dz   = document.getElementById('csv-dropzone');
    const file = e.dataTransfer.files[0];
    dz.classList.remove('border-indigo-400','bg-indigo-50/50');
    if (file) {
      const dt = new DataTransfer();
      dt.items.add(file);
      document.getElementById('csv_file').files = dt.files;
      showFileName({ files: [file] });
    }
  }

  function showFileName(input) {
    const files = input.files || input;
    const lbl   = document.getElementById('file-name');
    if (files[0]) {
      lbl.textContent = files[0].name + ' (' + (files[0].size / 1024).toFixed(1) + ' KB)';
      lbl.classList.remove('hidden');
    }
  }

  /* ── Init: add 3 default bulk rows ── */
  document.addEventListener('DOMContentLoaded', () => {
    addBulkRow(); addBulkRow(); addBulkRow();
  });
</script>
@endpush
@endsection
