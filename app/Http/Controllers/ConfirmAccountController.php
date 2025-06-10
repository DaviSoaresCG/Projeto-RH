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
            'password' => 'required|confirmed|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => ''
        ]);
    }
}
