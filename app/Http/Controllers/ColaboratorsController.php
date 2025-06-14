<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ColaboratorsController extends Controller
{
    public function index()
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        $colaborators = User::withTrashed()
            ->with('detail', 'department')
            ->where('role', '<>', 'admin')
            ->get();

        return view('colaborators.admin-all-colaborators', compact('colaborators'));
    }

    public function showDetails($id)
    {
        Gate::any(['admin', 'rh']) ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant show my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colaborator = User::with('detail', 'department')
            ->where('id', $id)
            ->first();

        if(!$colaborator){
            abort(404, 'PÁGINA NAO ENCONTRADA');
        }

        return view('colaborators.show-details', compact('colaborator'));
    }

    public function deleteColaborator($id)
    {
        Gate::any(['admin', 'rh']) ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant delete my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colaborator = User::findOrFail($id);

        return view('colaborators.delete-colaborator-confirm', compact('colaborator'));
    }

    public function deleteColaboratorConfirm($id)
    {
        Gate::any(['admin', 'rh']) ?: abort(403, 'VOCE NAO ESTA AUTORIZADO BITCH');

        // I cant delete my ID
        if (Auth::user()->id == $id) {
            return redirect()->route('home');
        }

        $colaborator = User::findOrFail($id);

        $colaborator->delete();

        return redirect()->route('colaborators.all-colaborators');
    }

    public function restoreColaborator($id)
    {
        Gate::allows('admin') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        $colaborator = User::withTrashed()->findOrFail($id);

        $colaborator->restore();

        return redirect()->route('colaborators.all-colaborators');
    }

    public function home(){
        Gate::allows('colaborator') ?: abort(403, 'VOCE NAO TEM AUTORIZAÇÃO');

        // get colaborator data
        $colaborator = User::with('detail', 'department')
            ->where('id', Auth::user()->id)
            ->first();

        return view('colaborators.show-details', compact('colaborator'));
    }
}
