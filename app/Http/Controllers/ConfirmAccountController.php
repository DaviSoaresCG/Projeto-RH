<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ConfirmAccountController extends Controller
{
    public function confirmAccount($token)
    {
        // check token is valid
        $user = User::where('confirmation_token', $token)->first();

        if(!$user){
            abort(403, 'Invalid Confirmation Token');
        }

        return view('auth.confirm-account', compact('user'));
    }

    public function confirmAccountSubmit(Request $request)
    {
        // validate form
        $request->validate([
            'token' => 'required|string|size:60',
            // password com caracteres especiais etc...
            'password' => 'required|confirmed|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => ''
        ]);

        $user = User::where('confirmation_token', $request->token)->first();

        $user->password = bcrypt($request->password);
        $user->confirmation_token = null;
        $user->email_verified_at = now();
        $user->save();

        return view('auth.welcome', compact('user'));
    }
}
