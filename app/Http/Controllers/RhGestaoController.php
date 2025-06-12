<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ConfirmAccountEmail;

class RhGestaoController extends Controller
{
    public function home()
    {
        Auth::user()->can('rh') ?: abort(403, 'SEM AUTORIZAÇÃO');

        $colaborators = User::with('detail', 'department')
            ->where('role', 'colaborator')
            ->withTrashed()
            ->get();


        return view('colaborators.colaborators', compact('colaborators'));
    }

    public function newColaborator()
    {
        Auth::user()->can('rh') ?: abort(403, 'SEM AUTORIZAÇÃO');

        $departments = Department::where('id', '>', 2);

        // if there are no departments, die
        if ($departments->count() === 0) {
            abort(403, 'There are no departments, Please contact System adminstrator');
        }
        return view('colaborators.add-colaborator', compact('departments'));
    }

    public function createColaborator(Request $request)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        // validate
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'select_department' => 'required|exists:departments,id',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'salary' => 'required|decimal:2',
            'admission_date' => 'required|date_format:Y-m-d',
            'select_department' => 'required|exists:departments,id'
        ]);

        // create new rh user
        if ($request->select_department <= 2) {
            return redirect()->route('home');
        }

        // create user confirmation token
        $token = Str::random(60);

        // new user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->confirmation_token = $token;
        $user->role = 'colaborator';
        $user->department_id = $request->select_department;
        $user->permissions = '["colaborator"]';
        $user->save();

        // user details
        $user->detail()->create([
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'salary' => $request->salary,
            'city' => $request->city,
            'phone' => $request->phone,
            'admission_date' => $request->admission_date
        ]);

        // send email to user
        Mail::to($user->email)->send(new ConfirmAccountEmail(route('confirm-account', $token)));

        return redirect()->route('rh.management.home')->with('success', 'COLABORATOR CREATE BITCH!');
    }

    public function editColaborator($id)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::findOrFail($id);
        $departments = Department::where('id', '>', 2);

        return view('colaborators.edit-colaborator', compact('colaborator', 'departments'));
    }

    public function updateColaborator(Request $request)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');
        
        // validate
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary' => 'required|decimal:2',
            'admission_date' => 'required|date_format:Y-m-d',
            'select_department' => 'required|exists:departments,id'
        ]);

        // check if deparment is valide
        
        if($request->select_department <= 2){
            return redirect()->route('home');
        }

        $user = User::with('detail')->findOrFail($request->user_id);

        $user->detail->salary = $request->salary;
        $user->detail->admission_date = $request->admission_date;
        $user->department_id = $request->select_department;

        $user->save();
        $user->detail->save();

        return redirect()->route('rh.management.home');
    }

    public function showDetails($id)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');
        
        $colaborator = User::with('detail', 'department')->findOrFail($id);

        return view('colaborators.show-details', compact('colaborator'));
    }

    public function deleteColaborator($id)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::findOrFail($id);

        return view('colaborators.delete-colaborator', compact('colaborator'));
    }

    public function deleteColaboratorConfirm($id)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::findOrFail($id);

        $colaborator->delete();

        return redirect()->route('rh.management');
    }

    public function restoreColaborator($id)
    {
        Auth::user()->can('rh') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');
        
        $colaborator = User::withTrashed()->findOrFail($id);

        $colaborator->restore();

        return redirect()->route('home');
    }
}

