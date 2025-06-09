<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        $departments = Department::all();

        return view('department.derpatments', compact('departments'));
    }

    public function newDepartment()
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');
        return view('department.add-department');
    }

    public function createDepartment(Request $request)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        //  validate
        $request->validate([
            'name' => 'required|string|max:100|unique:departments'
        ]);

        Department::create([
            'name' => $request->name
        ]);


        return redirect()->route('departments');
    }
}
