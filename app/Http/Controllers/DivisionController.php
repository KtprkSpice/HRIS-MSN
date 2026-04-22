<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $roles = auth()->user()->role->name;
        if ($roles === 'owner') {
            $divisions = Division::all();

            return view('Divisions.index', compact('divisions'));

        } else {
            abort(403);
        }
    }

    public function create()
    {
        $roles = auth()->user()->role->name;
        if ($roles === 'owner') {

            return view('Divisions.create');
        } else {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'owner') {
            $request->validate([
                'name' => 'string|required|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            Division::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('division.index')->with('success', "Berhasil Membuat Divisi $request->name");
        } else {
            abort(403);
        }
    }

    public function edit(Division $division)
    {
        $roles = auth()->user()->role->name;
        if ($roles === 'owner') {
            return view('Divisions.edit', compact('division'));

        } else {
            abort(403);
        }
    }

    public function update(Request $request, Division $division)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'owner') {

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            $division->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('division.index')->with('success', "Divisi dengan nama $request->name Telah Diupdate");
    }

    public function destroy(Division $division)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'owner') {
            $division->delete();

            return redirect()->route('division.index')->with('success', "Divisi dengan nama $division->name telah dihapus");
        } else {
            abort(403);
        }

    }
}
