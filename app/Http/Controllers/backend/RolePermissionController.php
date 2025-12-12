<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $data = Role::withCount('users')->paginate(20);

        $title = 'Delete Role!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view('role-permissions.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($item) {
            $last_word = explode('_', $item->name);
            $last_word = count($last_word) > 2 ? $last_word[1] . '_' . $last_word[2] : $last_word[1];
            return $last_word;
        });

        return view('role-permissions.create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        DB::beginTransaction();

        try {
            $permissions = $request->except(['_token', 'name', 'langs']);
            $role = Role::create(['name' => $request->name]);

            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }

            DB::commit();
            toast('Role created successfully!', 'success');
            return redirect()->route('role-permissions.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to create role: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail($id);

        $permissions = Permission::all()->groupBy(function ($item) {
            $last_word = explode('_', $item->name);
            $last_word = count($last_word) > 2 ? $last_word[1] . '_' . $last_word[2] : $last_word[1];
            return $last_word;
        });

        // Get users with this role along with their karyawan data
        $users = User::role($role->name)
            ->with('karyawan')
            ->get();

        return view('role-permissions.detail', [
            'role' => $role,
            'permissions' => $permissions,
            'users' => $users
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);

        $permissions = Permission::all()->groupBy(function ($item) {
            $last_word = explode('_', $item->name);
            $last_word = count($last_word) > 2 ? $last_word[1] . '_' . $last_word[2] : $last_word[1];
            return $last_word;
        });

        return view('role-permissions.edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            // Sync permissions
            $role->syncPermissions([]);
            $permissions = $request->except(['_token', 'name', '_method', 'langs']);

            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }

            DB::commit();
            toast('Role updated successfully!', 'success');
            return redirect()->route('role-permissions.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to update role: ' . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);

            // Check if role has users
            $userCount = User::role($role->name)->count();

            if ($userCount > 0) {
                toast('Cannot delete role! ' . $userCount . ' user(s) still have this role.', 'warning');
                return redirect()->route('role-permissions.index');
            }

            // Detach all permissions
            $role->syncPermissions([]);

            // Delete role
            $role->delete();

            DB::commit();
            toast('Role deleted successfully!', 'success');
            return redirect()->route('role-permissions.index');
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to delete role: ' . $e->getMessage(), 'error');
            return redirect()->route('role-permissions.index');
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);

            $user->syncRoles([$role->name]);

            DB::commit();
            toast('Role assigned successfully!', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to assign role: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);

            $user->removeRole($role->name);

            DB::commit();
            toast('Role removed successfully!', 'success');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            toast('Failed to remove role: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
