<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function home()
    {
        Auth::user()->can('rh') ?: abort(403, 'SEM AUTORIZAÇÃO');

        // all information about the organization
        $data = [];

        // get total numbers of colaborators(deleted_at is null)
        $data['total_colaborators'] =  User::whereNull('deleted_at')->count();

        // total colaborators is deleted
        $data['total_colaborators_deleted'] =  User::onlyTrashed()->count();

        // total salary for all colaborators
        $data['total_salary'] = User::withoutTrashed()->with('detail')->get()->sum(function ($colaborator) {
            return $colaborator->detail->salary;
        });
        $data['total_salary'] = number_format($data['total_salary'], 2, ',', '.').'R$';

        // total colaborators by departments
        $data['total_colaborators_per_department'] = User::withoutTrashed()
            ->with('department')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                return [
                    'department' => $department->first()->department->name ?? '-',
                    'total' => $department->count()
                ];
            });

        // total salary by department
        $data['total_salary_by_department'] = User::withoutTrashed()
            ->with('department', 'detail')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                return [
                    'department' => $department->first()->department->name ?? '-',
                    'total' => $department->sum(function ($colaborator) {
                        return $colaborator->detail->salary;
                    })
                ];
            });
        // format salary
        $data['total_salary_by_department'] = $data['total_salary_by_department']->map(function($department){
            return [
                'department' => $department['department'],
                'total' => number_format($department['total'], 2, ',', '.').'R$'
            ];
        });

        return view('home', compact('data'));
    }
}
