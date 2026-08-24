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
        $userId = auth()->user();
        $user = auth()->user()->role->name;
        $roleId = auth()->user()->role_id;
        $startMonth = now()->startOfMonth();
        $endMonth = now()->endOfMonth();
        $workDaysInCurrentMonth = collect(CarbonPeriod::create($startMonth, $endMonth))
            ->filter(fn($date) => $date->isWeekday())
            ->count();

        $employees = Employee::where('status', 'active')
            ->whereHas('user.role', function ($q) {
                $q->where('name', 'employee');
            })
            ->with('division')
            ->withCount(['presence as absence_count' => function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('date', [$startMonth, $endMonth])
                    ->where('status', 'absent');
            }])
            ->withCount(['schedules as schedule_count' => function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('date', [$startMonth, $endMonth]);
            }])
            ->get()
            ->map(function ($employee) {
                $attendancePercentage = $employee->schedule_count > 0
                    ? (($employee->schedule_count - $employee->absence_count) / $employee->schedule_count) * 100
                    : 100;
                $employee->attendancePercentage = max(0, round($attendancePercentage, 1));

                return $employee;
            })
            ->filter(function ($employee) {
                return $employee->attendancePercentage < 70;
            })
            ->sortBy([
                ['attendancePercentage', 'asc'],
                ['absence_count', 'desc'],
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

        $leaveCounts = LeaveRequest::where('status', 'pending')
            ->when($user === 'employee', function ($q) use ($userId) {
                $q->whereHas('employee', function ($employeeQuery) use ($userId) {
                    $employeeQuery->where('id', $userId->employee->id);
                });
            })
            ->count();

        $employeeAttendancePercentage = 100;
        if ($user === 'employee' && $userId->employee) {
            $employeeAbsences = $userId->employee->presence()
                ->whereBetween('date', [$startMonth, $endMonth])
                ->where('status', 'absent')
                ->count();

            $employeeScheduleCount = $userId->employee->schedules()
                ->whereBetween('date', [$startMonth, $endMonth])
                ->count();

            $employeeAttendancePercentage = $employeeScheduleCount > 0
                ? max(0, round((($employeeScheduleCount - $employeeAbsences) / $employeeScheduleCount) * 100, 1))
                : 100;
        }

        $totalDivisions = Division::where('status', 'active')->count();

        return view('Dashboard.index', compact(
            'employees',
            'maleEmployee',
            'femaleEmployee',
            'totalEmployee',
            'leaveCounts',
            'totalDivisions',
            'workDaysInCurrentMonth',
            'employeeAttendancePercentage'
        ));
    }
}
