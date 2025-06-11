<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColaboratorsController extends Controller
{
    public function index()
    {
        Auth::user()->can('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        $colabortors = User::with('detail', 'department')
            ->where('role', '<>', 'admin')
            ->get();

        return view('colaborators.admin-all-colaborators', compact('colaborators'));
    }

    public function showDetails($id)
    {
        Auth::user()->can('admin', 'rh') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant show my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colabortors = User::with('detail', 'department')
            ->where('id', $id)
            ->first();

        return view('colaborators.show-details', compact('colaborators'));
    }

    public function deleteColaborator($id)
    {
        Auth::user()->can('admin', 'rh') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant delete my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colaborator = User::findOrFail($id);
        
        return view('colaborators.delete-colaborator-confirm', compact('colaborator'));
    }

    public function deleteColaboratorConfirm($id)
    {
        Auth::user()->can('admin', 'rh') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant delete my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colaborator = User::findOrFail($id);

        $colaborator->delete();

        return redirect()->route('colaboraotors.all-colaborator');

    }
}
