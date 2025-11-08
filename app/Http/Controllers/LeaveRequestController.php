<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index() {
        $leaveRequests = LeaveRequest::all();
        return view('leave.index', compact('leaveRequests'));
    }
}
