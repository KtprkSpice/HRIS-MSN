<?php

namespace App\Support;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Presence;
use App\Models\Schedule;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class AttendancePolicy
{
    public const APPROVED_LEAVE_STATUSES = ['approved', 'confirmed'];

    public static function isSuddenLeaveType(?LeaveType $leaveType): bool
    {
        if (! $leaveType) {
            return false;
        }

        $name = strtolower($leaveType->name);

        return str_contains($name, 'dadakan')
            || str_contains($name, 'mendadak')
            || str_contains($name, 'emergency');
    }

    public static function approvedLeaveOverlapQuery(
        int $employeeId,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
        ?int $excludeLeaveRequestId = null
    ): Builder {
        $start = self::dateString($startDate);
        $end = self::dateString($endDate);

        return LeaveRequest::where('employee_id', $employeeId)
            ->whereIn('status', self::APPROVED_LEAVE_STATUSES)
            ->when($excludeLeaveRequestId, function ($q) use ($excludeLeaveRequestId) {
                $q->where('id', '!=', $excludeLeaveRequestId);
            })
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start);
    }

    public static function hasApprovedLeaveOnDate(int $employeeId, CarbonInterface|string $date): bool
    {
        return self::approvedLeaveOverlapQuery($employeeId, $date, $date)->exists();
    }

    public static function scheduleOverlapQuery(
        int $employeeId,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
        ?int $excludeScheduleId = null
    ): Builder {
        $start = self::dateString($startDate);
        $end = self::dateString($endDate);

        return Schedule::where('employee_id', $employeeId)
            ->when($excludeScheduleId, function ($q) use ($excludeScheduleId) {
                $q->where('id', '!=', $excludeScheduleId);
            })
            ->whereBetween('date', [$start, $end]);
    }

    public static function payableAbsenceQuery(
        int $employeeId,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate
    ): Builder {
        $start = self::dateString($startDate);
        $end = self::dateString($endDate);

        return Presence::where('employee_id', $employeeId)
            ->where('status', 'absent')
            ->whereBetween('date', [$start, $end])
            ->whereHas('schedule')
            ->whereDoesntHave('employee.leaveRequest', function ($q) {
                $q->whereIn('status', self::APPROVED_LEAVE_STATUSES)
                    ->whereColumn('leave_requests.start_date', '<=', 'presences.date')
                    ->whereColumn('leave_requests.end_date', '>=', 'presences.date');
            });
    }

    private static function dateString(CarbonInterface|string $date): string
    {
        return $date instanceof CarbonInterface ? $date->toDateString() : (string) $date;
    }
}
