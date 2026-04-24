<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Exports\UserTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $users = $query->latest()->paginate(10);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()->name ?? '';
        
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        // Sync role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()
                         ->with('success', 'Password berhasil diubah');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                             ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil dihapus');
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                             ->with('error', 'Anda tidak dapat mengubah status akun sendiri');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $statusText = $user->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
                         ->with('success', "User berhasil {$statusText}");
    }

    /**
     * Export users to Excel.
     */
    public function export()
    {
        return Excel::download(new UsersExport(), 'users_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('admin.users.import');
    }

    /**
     * Download template import user.
     */
    public function downloadTemplate()
    {
        return Excel::download(new UserTemplateExport(), 'template_import_user.xlsx');
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new UsersImport();
        Excel::import($import, $request->file('file'));

        $rowCount = $import->getRowCount();
        $successCount = $import->getSuccessCount();
        $failures = $import->getFailures();

        $message = "Import selesai. Total data: {$rowCount}, Berhasil: {$successCount}, Gagal: " . count($failures);

        if (count($failures) > 0) {
            session()->flash('import_errors', $failures);
            return redirect()->back()->with('warning', $message);
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}