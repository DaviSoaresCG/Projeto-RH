<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function home()
    {
        Auth::user()->can('admin') ?: abort(403, 'SEM AUTORIZAÇÃO');

        $data = [];

        // Total colaborators
        $data['total_colaborators'] = User::whereNull('deleted_at')->count();
        $data['total_colaborators_deleted'] = User::onlyTrashed()->count();

        // Total salary
        $totalSalary = User::withoutTrashed()->with('detail')->get()->sum(function ($colaborator) {
            return $colaborator->detail->salary ?? 0;
        });
        $data['total_salary'] = number_format($totalSalary, 2, ',', '.') . 'R$';

        // Colaborators by department - formatado como array associativo simples
        $data['total_colaborators_per_department'] = User::withoutTrashed()
            ->with('department')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                return [
                    'department' => optional($department->first()->department)->name ?? '-',
                    'total' => $department->count()
                ];
            })
            ->values() // Remove as chaves originais
            ->all(); // Converte para array

        // Salary by department - formatado como array associativo simples
        $data['total_salary_by_department'] = User::withoutTrashed()
            ->with('department', 'detail')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                $total = $department->sum(function ($colaborator) {
                    return $colaborator->detail->salary ?? 0;
                });

                return [
                    'department' => optional($department->first()->department)->name ?? '-',
                    'total' => number_format($total, 2, ',', '.') . 'R$'
                ];
            })
            ->values() // Remove as chaves originais
            ->all(); // Converte para array

        return view('home', compact('data'));
    }
}
