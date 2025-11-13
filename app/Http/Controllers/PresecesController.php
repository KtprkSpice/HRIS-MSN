<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresecesController extends Controller
{
    public function index() {
        $presences = Presence::all();
        return view('presences.index', compact('presences'));
    }

    public function create() {
        $presences = Presence::all();
        $employees = Employee::all();
        return view('presences.create', compact('preseneces', 'employees'));
    }
}
