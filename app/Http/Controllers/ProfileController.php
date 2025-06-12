<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $colaborator =  User::with('detail')->findOrFail(Auth::user()->id);
        return view('user.profile', compact('colaborator'));
    }

    public function updatePassword(Request $request)
    {
        // validate
        $request->validate(
            [
                'current_password' => 'required|min:8|max:16',
                'new_password' => 'required|min:8|max:16|different:current_password',
                'new_password_confirmation' => 'required|same:new_password'
            ]
        );

        $user = auth()->user();
        // check if current password is correct
        if (!password_verify($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current Password is INCORRECT!!! BITCH');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated!');
    }

    public function updateUserData(Request $request)
    {
        // validate
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id()
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profile update!');
    }

    public function updateUserAddress(Request $request)
    {
        // form validate
        $request->validate([
            'address' => 'required|min:3|max:255',
            'zip_code' => 'required|min:8|max:8',
            'city' => 'required|min:3|max:50',
            'phone' => 'required|min:6|max:20',
        ]);

        // get user detail
        $user = User::with('detail')->findOrFail(Auth::user()->id);
        $user->detail->address = $request->address;
        $user->detail->zip_code = $request->zip_code;
        $user->detail->city = $request->city;
        $user->detail->phone = $request->phone;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated!');
    }
}
