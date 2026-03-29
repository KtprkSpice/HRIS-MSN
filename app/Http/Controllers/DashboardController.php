<?php

namespace App\Http\Controllers;

use App\Models\division;
use App\Models\Employee;
use App\Models\LeaveRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->role->name;
        $roleId = auth()->user()->role_id;
        $startMonth = now()->startofMonth()->format('Y-m-d');
        $endMonth = now()->endOfMonth()->format('Y-m-d');
        // $employees = Employee::where('status', 'active')
        //     ->whereHas('presence', function ($q) use ($startMonth, $endMonth) {
        //         $q->whereBetween('date', [$startMonth, $endMonth])->where('status', 'absent');
        //     })->get();

        $employees = Employee::where('status', 'active')
            ->withCount(['presence as absenceCount' => function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('date', [$startMonth, $endMonth])
                    ->where('status', 'absent');
            },
            ])->get()
            ->filter(function ($employee) {
                return $employee->absenceCount >= 8; // 70% dari 28 hari kerja
            });

        $roles = $user === 'owner' ? ['employee', 'hr'] : ['employee'];

        $genderCounts = Employee::whereHas('user.role', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        })
            ->where('status', 'active')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $maleEmployee = $genderCounts['laki-laki'] ?? 0;
        $femaleEmployee = $genderCounts['perempuan'] ?? 0;

        $totalEmployee = $maleEmployee + $femaleEmployee;

        $leaveCounts = LeaveRequest::where('status', 'pending')->whereHas('approvals', function ($q) use ($roleId) {
            $q->whereColumn('approval_order', 'leave_requests.current_step')
                ->where('role_id', $roleId)
                ->whereNull('approved_at');
        })->count();

        $totalDivisions = division::where('status', 'active')->count();

        return view('Dashboard.index', compact('employees', 'maleEmployee', 'femaleEmployee', 'totalEmployee', 'leaveCounts', 'totalDivisions'));
    }
}
