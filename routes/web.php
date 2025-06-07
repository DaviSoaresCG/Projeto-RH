<?php

use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/email', function(){
    Mail::raw('Mensagem de teste', function (Message $message){
        $message->to('davi@gmail.com')
        ->subject('Bem vindo')
        ->from('rh@gmail.com');
    });

    echo "Email enviado";
});

Route::get('/admin', function(){
    $admin = User::with('detail', 'department')->find(1);
    return view('admin', compact('admin'));
});