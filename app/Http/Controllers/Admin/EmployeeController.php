<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display list of employees
     */
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Store a new employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'required|digits_between:4,6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('employees', 'public');
        }

        Employee::create([
            'name' => $request->name,
            'pin' => Hash::make($request->pin),
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        return back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    /**
     * Update an employee
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'nullable|digits_between:4,6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = ['name' => $request->name];

        // Only update PIN if provided
        if ($request->filled('pin')) {
            $data['pin'] = Hash::make($request->pin);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('employees', 'public');
        }

        $employee->update($data);

        return back()->with('success', 'Data karyawan berhasil diperbarui');
    }

    /**
     * Delete an employee
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        // Delete avatar if exists
        if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return back()->with('success', 'Karyawan berhasil dihapus');
    }

    /**
     * Toggle employee active status
     */
    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['is_active' => !$employee->is_active]);

        $status = $employee->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Karyawan berhasil {$status}");
    }
}
