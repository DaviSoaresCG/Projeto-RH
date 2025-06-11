<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccountEmail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RhUserController extends Controller
{
    public function index()
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        // $colaborators = User::where('role', 'rh')->get();
        $colaborators = User::withTrashed()
            ->with('details')
            ->where('role', 'rh')
            ->get();

        return view('colaborators.rh-users', compact('colaborators'));
    }

    public function newColaborator()
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        // all departments
        $departments = Department::all();

        return view('colaborators.add-rh-user', compact('departments'));
    }

    public function createColaborator(Request $request)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

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
        if ($request->select_department != 2) {
            return redirect()->rotue('home');
        }

        // create user confirmation token
        $token = Str::random(60);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->confirmation_token = $token;
        $user->role = 'rh';
        $user->department_id = $request->select_department;
        $user->permissions = '["rh"]';
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

        return redirect()->route('colaborators.rh-users')->with('success', 'COLABORATOR CREATE BITCH!');
    }

    public function editColaborator($id)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::with('detail')->where('role', 'rh')->findOrFail($id);

        return view('colaborators.edit-rh-user', compact('colaborator'));
    }

    public function updateColaborator(Request $request)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        // validate
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary' => 'required|decimal:2',
            'admission_date' => 'required|date_format:Y-m-d'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->detail->update([
            'salary' => $request->salary,
            'admission_date' => $request->admission_date
        ]);

        return redirect()->route('colaborators.rh-users')->with('success', 'colaborator UPDATE BITCH!!');
    }

    public function deleteColaborator($id)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::findOrFail($id);
        return view('colaborators.delete-rh-user', compact('colaborator'));
    }

    public function deleteColaboratorConfirm($id)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::findOrFail($id);
        $colaborator->delete();

        return redirect()->route('colaborators.rh-users')->with('success', 'CPF CANCELADO!!!');
    }

    public function restoreColaborator($id)
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::withTrashed()->where('role', 'rh')->findOrFail($id);
        $colaborator->restore();

        return redirect()->route('colaborators.rh-users')->with('success', 'COLABORATODOR FOI RESTAURADO');
    }
}
