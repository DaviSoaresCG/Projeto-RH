<?php

use App\Http\Controllers\ColaboratorsController;
use App\Http\Controllers\ConfirmAccountController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RhUserController;
use App\Models\Department;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/email', function () {
    Mail::raw('Mensagem de teste', function (Message $message) {
        $message->to('davi@gmail.com')
            ->subject('Bem vindo')
            ->from('rh@gmail.com');
    });

    echo "Email enviado";
});

Route::get('/admin', function () {
    $admin = User::with('detail', 'department')->find(1);
    return view('admin', compact('admin'));
});


Route::middleware('guest')->group(function () {
    // email confirmation and password confirmation
    Route::get('/confirm-account/{token}', [ConfirmAccountController::class, 'confirmAccount'])->name('confirm-account');
    Route::get('/confirm-account', [ConfirmAccountController::class, 'confirmAccountSubmit'])->name('confirm-account-submit');
});


Route::middleware('auth')->group(function () {
    Route::redirect('/', 'home');
    Route::view('/home', 'home')->name('home');

    // user profile
    Route::get('/user/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::post('/user/profile/update-password', [ProfileController::class, 'updatePassword'])->name('user.profile.update-password');
    Route::post('/user/profile/update-user-data', [ProfileController::class, 'updateUserData'])->name('user.profile.update-user-data');

    // departments routes
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');

    // create departments
    Route::get('/departments/new-department', [DepartmentController::class, 'newDepartment'])->name('departments.new-department');
    Route::post('/departments/create-department', [DepartmentController::class, 'createDepartment'])->name('departments.new-department');

    // edit departments
    Route::get('/departments/edit-department/{id}', [DepartmentController::class, 'editDepartment'])->name('departments.edit-department');
    Route::get('/departments/update-department', [DepartmentController::class, 'updateDepartment'])->name('departments.update-department');

    // delete departments
    Route::get('/departments/delete-department/{id}', [DepartmentController::class, 'deleteDepartment'])->name('departments.delete-department');
    Route::get('/departments/delete-department-confirm/{id}', [DepartmentController::class, 'deleteDepartment'])->name('departments.delete-department-confirm');

    // rh colaborators
    Route::get('/rh-users', [RhUserController::class, 'index'])->name('colaborators.rh-users');
    Route::get('/rh-users/new-colaborator', [RhUserController::class, 'newColaborator'])->name('colaborators.rh.new-colaborator');
    // new colaborator
    Route::post('/rh-users/create-colaborator', [RhUserController::class, 'createColaborator'])->name('colaborators.rh.create-colaborator');

    // edit colaborators
    Route::get('/rh-users/edit-colaborator/{id}',  [RhUserController::class, 'editColaborator'])->name('colaborators.rh.edit-colaborator');
    // update colaborator
    Route::get('/rh-users/update-colaborator',  [RhUserController::class, 'updateColaborator'])->name('colaborators.rh.update-colaborator');

    // delete colaborator
    Route::get('/rh-users/delete/{id}', [RhUserController::class, 'deleteColaborator'])->name('colaborators.rh.delete-colaborator');
    Route::get('/rh-users/delete-confirm/{id}', [RhUserController::class, 'deleteColaboratorConfirm'])->name('colaborators.rh.delete-confirm');
    // restore colaborators
    Route::get('/rh-users/restore/{id}', [RhUserController::class, 'restoreColaborator'])->name('colaborators.rh.restore');


    // admin colaborators list
    Route::get('/colaborators', [ColaboratorsController::class, 'index'])->name('colaborators.all-colaborators');
    Route::get('/colaborators/details/{id}', [ColaboratorsController::class, 'showDetails'])->name('colaborators.details');
    // delete colaborator
    Route::get('/colaborators/delete/{id}', [ColaboratorsController::class, 'deleteColaborator'])->name('colaborators.delete');
    Route::get('/colaborators/delete-confirm/{id}', [ColaboratorsController::class, 'deleteColaboratorConfirm'])->name('colaborators.delete-confirm');
    // admin restore
    Route::get('/rh-users/restore/{id}', [ColaboratorsController::class, 'restoreColaborator'])->name('colaborators.restore');

});
