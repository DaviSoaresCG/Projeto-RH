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

        $colaborators = User::with('detal', 'department')
            ->where('role', 'colaborator')
            ->withTrashade()
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
            return redirect()->rotue('home');
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
}
