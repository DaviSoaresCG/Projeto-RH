<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index()
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        $departments = Department::all();

        return view('department.departments', compact('departments'));
    }

    public function newDepartment()
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');
        return view('department.add-department');
    }

    public function createDepartment(Request $request)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        //  validate
        $request->validate([
            'name' => 'required|string|max:100|unique:departments'
        ]);

        Department::create([
            'name' => $request->name
        ]);


        return redirect()->route('departments');
    }

    public function editDepartment($id)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        if ($this->isDepartmentBlocked($id)) {
            return redirect()->route('departments');
        }

        $department = Department::findOrFail($id);
        return view('department.edit-department', compact('department'));
    }

    public function updateDepartment(Request $request)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        $id = $request->id;

        // validate
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|min:3|max:100|unique:departments,name,' . $id
        ]);

        // check if ID == 1
        if ($this->isDepartmentBlocked($id)) {
            return redirect()->route('departments');
        }

        $department = Department::findOrFail($id);

        $department->update([
            'name' => $request->name
        ]);

        return redirect()->route('departments');
    }

    public function deleteDepartment($id)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        if ($this->isDepartmentBlocked($id)) {
            return redirect()->route('departments');
        }

        $department = Department::findOrFail($id);

        // page confirmation

        return view('department.delete-department-confirm', compact('department'));
    }

    public function deleteDepartmentConfirm($id)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        if($this->isDepartmentBlocked($id)){
            return redirect()->route('departments');
        }

            $department = Department::findOrFail($id);

            $department->delete();

            // update all colaborators with department deleted
            User::where('department_id', $id)->update(['department_id' => null]);

            return redirect()->route('departments');
        
    }

    private function isDepartmentBlocked($id)
    {
        return in_array(intval($id), [1,2]);
    }
}
