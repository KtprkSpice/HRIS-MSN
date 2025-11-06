<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
   public function edit($id) {
    $employee = Employee::findOrFail($id);

    return view('profile.edit', compact('employee'));
   }

}
