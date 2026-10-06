<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['role', 'department'])
        ->when($request->search, function ($query) use ($request) {

            $query->where(function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');

            });

        })

        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('administrator.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('administrator.user.create', compact('roles', 'departments'));
    }

    public function show(User $user)
    {
        $user->load(['role', 'department']);
        return view('administrator.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('administrator.user.edit', compact('user', 'roles', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => ['required', 'confirmed', Password::min(6)],
            'role_id'       => 'required|exists:roles,id_role',
            'department_id' => 'nullable|exists:departments,id_department',
        ]);

        User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            // Model sudah casts password => hashed, jangan Hash::make lagi
            'password'      => $validated['password'],
            'role_id'       => $validated['role_id'],
            'department_id' => $validated['department_id'] ?: null,
        ]);

        return redirect()
            ->route('administrator.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'password'      => ['nullable', 'confirmed', Password::min(6)],
            'role_id'       => 'required|exists:roles,id_role',
            'department_id' => 'nullable|exists:departments,id_department',
        ]);

        $data = [
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'role_id'       => $validated['role_id'],
            'department_id' => $validated['department_id'] ?: null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password']; // hashed cast
        }

        $user->update($data);

        return redirect()
            ->route('administrator.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Jangan hapus diri sendiri
        if ($user->is(Auth::user())) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}