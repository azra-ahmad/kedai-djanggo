<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    /**
     * Display the lock screen with active employees
     */
    public function show()
    {
        $employees = Employee::active()->orderBy('name')->get();
        return view('admin.auth.lock-screen', compact('employees'));
    }

    /**
     * Verify PIN and set active employee session
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pin' => 'required|string',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        // Check if employee is active
        if (!$employee->is_active) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Karyawan ini sudah tidak aktif'], 401);
            }
            return back()->withErrors(['pin' => 'Karyawan ini sudah tidak aktif']);
        }

        // Verify PIN
        if (!Hash::check($request->pin, $employee->pin)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'PIN salah. Silakan coba lagi.'], 401);
            }
            return back()->withErrors(['pin' => 'PIN salah. Silakan coba lagi.']);
        }

        // Store employee data in session
        session([
            'active_employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'avatar_url' => $employee->avatar_url,
            ]
        ]);

        if ($request->wantsJson()) {
            session()->flash('success', "Selamat datang, {$employee->name}!");
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.dashboard')->with('success', "Selamat datang, {$employee->name}!");
    }

    /**
     * Clear active employee session (end shift)
     */
    public function lock()
    {
        $employeeName = session('active_employee.name', 'Kasir');
        session()->forget('active_employee');

        return redirect()->route('admin.lock-screen')->with('success', "Shift {$employeeName} berakhir. Silakan pilih kasir baru.");
    }
}
