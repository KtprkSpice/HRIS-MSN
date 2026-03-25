<?php

namespace App\Http\Controllers;

use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
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

        return view('Dashboard.index', compact('employees'));
    }
}
