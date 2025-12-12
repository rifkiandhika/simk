<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['karyawan', 'roles'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->role, function ($query, $role) {
                $query->role($role);
            })
            ->latest()
            ->paginate(20);

        $totalAktif = User::where('status', 'Aktif')->count();
        $totalNonaktif = User::where('status', 'Nonaktif')->count();
        $roles = Role::all();

        $title = 'Delete User!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view('users.index', compact('users', 'totalAktif', 'totalNonaktif', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::where('status_aktif', 'Aktif')
            ->whereDoesntHave('user')
            ->get();
        $roles = Role::all();

        return view('users.create', compact('karyawans', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id_karyawan|unique:users,id_karyawan',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        DB::beginTransaction();

        try {
            $karyawan = Karyawan::find($request->id_karyawan);

            $user = User::create([
                'id_karyawan' => $request->id_karyawan,
                'name' => $karyawan->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            // Assign role to user
            $user->assignRole($request->role);

            DB::commit();
            toast('User created successfully!', 'success');
            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to create user: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['karyawan', 'roles.permissions'])->findOrFail($id);

        return view('users.detail', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['karyawan', 'roles'])->findOrFail($id);
        $karyawans = Karyawan::where('status_aktif', 'Aktif')
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('user')
                    ->orWhere('id_karyawan', $user->id_karyawan);
            })
            ->get();
        $roles = Role::all();

        return view('users.edit', compact('user', 'karyawans', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id_karyawan|unique:users,id_karyawan,' . $id,
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        DB::beginTransaction();

        try {
            $karyawan = Karyawan::find($request->id_karyawan);

            $userData = [
                'id_karyawan' => $request->id_karyawan,
                'name' => $karyawan->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'status' => $request->status,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Sync role
            $user->syncRoles([$request->role]);

            DB::commit();
            toast('User updated successfully!', 'success');
            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to update user: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            // Remove all roles
            $user->syncRoles([]);

            // Delete user
            $user->delete();

            DB::commit();
            toast('User deleted successfully!', 'success');
            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to delete user: ' . $e->getMessage(), 'error');
            return redirect()->route('users.index');
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(string $id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 'Aktif' ? 'Nonaktif' : 'Aktif';
            $user->save();

            DB::commit();
            toast('User status updated successfully!', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to update status: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, string $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();
            toast('Password reset successfully!', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to reset password: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
