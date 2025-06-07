<?php

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