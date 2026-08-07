<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($roles !== 'owner' || empty($roles)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'string|required|max:255',
            'description' => 'nullable|string|max:255',

            // Position
            'position' => 'required|array|min:1',
            'position.*' => 'required|string|max:255',
            'base_salary' => 'required|array|min:1',
            'base_salary.*' => 'required|string',
            'deduction_per_minute' => 'required|array|min:1',
            'deduction_per_minute.*' => 'required|string',
        ]);

        DB::transaction(function () use ($validated) {

            $division = Division::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            foreach ($validated['position'] as $index => $positionName) {
                $cleanSalary = (int) str_replace('.', '', $validated['base_salary'][$index]);
                $cleanDeduction = (int) str_replace('.', '', $validated['deduction_per_minute'][$index]);

                Position::create([
                    'division_id' => $division->id,
                    'name' => $positionName,
                    'base_salary' => $cleanSalary,
                    'cut_per_minute' => $cleanDeduction,
                ]);
            }
        });

        return redirect()->route('division.index')->with('success', "Berhasil Membuat Divisi $request->name");
    }

    public function edit($id)
    {
        $roles = auth()->user()->role->name;
        if ($roles !== 'owner' || empty($roles)) {
            abort(403);
        }

        $division = Division::with('positions')->findOrFail($id);

        return view('Divisions.edit', compact('division'));
    }

    public function update(Request $request, $id)
    {
        $roles = auth()->user()->role->name;

        if ($roles !== 'owner' || empty($roles)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',

            //    Position
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'nullable|integer',
            'position' => 'required|array|min:1',
            'position.*' => 'required|string|max:255',
            'base_salary' => 'required|array|min:1',
            'base_salary.*' => 'required|string',
            'deduction_per_minute' => 'required|array|min:1',
            'deduction_per_minute.*' => 'required|string',
        ]);

        $division = Division::findOrFail($id);

        try {

            DB::transaction(function () use ($validated, $division) {
                $division->update([
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                ]);

                $processedID = [];

                foreach ($validated['position'] as $index => $positionName) {
                    $posID = $validated['position_ids'][$index] ?? null;
                    $cleanSalary = str_replace('.', '', $validated['base_salary'][$index]);
                    $cleanDeduction = str_replace('.', '', $validated['deduction_per_minute'][$index]);

                    $position = Position::updateOrCreate(
                        [
                            'id' => $posID,
                            'division_id' => $division->id,
                        ],
                        [
                            'name' => $positionName,
                            'base_salary' => $cleanSalary,
                            'cut_per_minute' => $cleanDeduction,
                        ]
                    );

                    $processedID[] = $position->id;
                }
                // setelah selesai semua update/create
                $positionsToDelete = $division->positions()
                    ->whereNotIn('id', $processedID)
                    ->get();

                foreach ($positionsToDelete as $posToDelete) {

                    $isUsed = DB::table('employees')
                        ->where('position_id', $posToDelete->id)
                        ->exists();

                    if ($isUsed) {
                        throw new \Exception(
                            "Posisi '{$posToDelete->name}' tidak dapat dihapus karena masih digunakan oleh karyawan aktif."
                        );
                    }

                    $posToDelete->delete();
                }
            });

            return redirect()->route('division.index')->with('success', "Divisi dengan nama $request->name Telah Diupdate");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error_from_controller', $e->getMessage());
        }
    }

    public function destroy(Division $division)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'owner') {
            $employeeCount = Employee::where('division_id', $division->id)->count();

            if ($employeeCount > 0) {
                return redirect()
                    ->route('division.index')
                    ->with(
                        'error_from_controller',
                        "Divisi ini tidak dapat dihapus karena masih memiliki {$employeeCount} karyawan. Pindahkan atau hapus karyawan tersebut terlebih dahulu."
                    );
            }

            $division->delete();

            return redirect()->route('division.index')->with('success', "Divisi dengan nama $division->name telah dihapus");
        } else {
            abort(403);
        }
    }

    public function active($id)
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles == 'owner') {
            $division = Division::findOrFail($id);
            $divisionName = $division->name;

            $division->update([
                'status' => 'active',
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('division.index')->with('success', "Tugas $divisionName telah diupdate menjadi Active");
    }

    public function inactive($id)
    {
        $roles = auth()->user()->role->name;

        if ($roles == 'owner') {
            $division = Division::findOrFail($id);
            $divisionName = $division->name;

            $employeeCount = Employee::where('division_id', $division->id)->count();

            if ($employeeCount > 0) {
                return redirect()
                    ->route('division.index')
                    ->with(
                        'warning_from_controller',
                        "Divisi ini tidak dapat dinonaktifkan karena masih memiliki {$employeeCount} karyawan. Pindahkan atau hapus karyawan tersebut terlebih dahulu."
                    );
            }

            $division->update([
                'status' => 'inactive',
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('division.index')->with('success', "Tugas $divisionName telah diupdate menjadi Inactive");
    }
}
