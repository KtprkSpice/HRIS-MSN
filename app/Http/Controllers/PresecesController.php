<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresecesController extends Controller
{
    public function index()
    {
        $presences = Presence::all();
        return view('presences.index', compact('presences'));
    }

    public function create()
    {
        $presences = Presence::all();
        $employees = Employee::all();
        return view('presences.create', compact('presences', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        Presence::create($request->all());

        return redirect()->route('presence.index')->with('success', "Data Presensi Telah Dibuat");
    }

    public function edit(Presence $presence) {
        $employees = Employee::all();
        return view('presences.edit', compact('employees', 'presence'));
    }

    public function update(Presence $presence, Request $request){
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        $presence->update($request->all());

        return redirect()->route('presence.index')->with('success', "Data Berhasil Diubah");
    }

    public function destroy(Presence $presence) {
        $presence->delete();

        return redirect()->route('presence.index')->with('success', "Data Telah dihapus");
    }
}
