<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->role->name;
        $roleId = auth()->user()->role_id;
        $startMonth = now()->startOfMonth();
        $endMonth = now()->endOfMonth();
        $workDaysInCurrentMonth = collect(CarbonPeriod::create($startMonth, $endMonth))
            ->filter(fn ($date) => $date->isWeekday())
            ->count();

        $employees = Employee::where('status', 'active')
            ->with('division')
            ->withCount(['presence as absencesCount' => function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('date', [$startMonth, $endMonth])
                    ->where('status', 'absent');
            }])
            ->get()
            ->map(function ($employee) use ($workDaysInCurrentMonth) {
                $attendancePercentage = $workDaysInCurrentMonth > 0
                ? (($workDaysInCurrentMonth - $employee->absenceCount) / $workDaysInCurrentMonth) * 100
                : 0;
                $employee->attendancePercentage = max(0, round($attendancePercentage, 1));

                return $employee;
            })
            ->filter(function ($employee) {
                return $employee->attendancePercentage < 70;
            })
            ->sortBy([
                ['attendancePercentage', 'asc'],
                ['absenceCount', 'desc'],
            ])
            ->take(10)
            ->values();

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

        $totalDivisions = Division::where('status', 'active')->count();

        return view('Dashboard.index', compact('employees', 'maleEmployee', 'femaleEmployee', 'totalEmployee', 'leaveCounts', 'totalDivisions', 'workDaysInCurrentMonth'));
    }
}
