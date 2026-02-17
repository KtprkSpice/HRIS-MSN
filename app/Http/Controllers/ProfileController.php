<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        return view('profile.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|digits_between:1,20|max:20|unique:employees,phone,'.$id,

        ]);

        $employee->update($request->all());

        return redirect()->route('profile.edit', $employee->id)
            ->with('success', 'Profile telah diupdate');

    }
}
