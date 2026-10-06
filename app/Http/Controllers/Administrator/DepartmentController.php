<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::withCount('users')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('administrator.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('administrator.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'type' => 'required|in:prodi,kajur,wadir,upa',
        ]);

        Department::create($validated);

        return redirect()
            ->route('administrator.departments.index')
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function show(Department $department)
    {
        $department->load(['users.role']);
        return view('administrator.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('administrator.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id_department . ',id_department',
            'type' => 'required|in:prodi,kajur,wadir,upa',
        ]);

        $department->update($validated);

        return redirect()
            ->route('administrator.departments.index')
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus unit yang masih memiliki pengguna.');
        }

        $department->delete();

        return redirect()
            ->route('administrator.departments.index')
            ->with('success', 'Unit berhasil dihapus.');
    }
}