<?php

namespace App\Http\Controllers;

use App\Models\division;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = division::all();

        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.create');
    }
}
