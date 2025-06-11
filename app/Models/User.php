<?php

namespace App\Models;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    
    // relacionamento de 1 para 1 com a tabela User_detail
    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    // belong significa que o usuario pertence a muitos departmentos, 1:n
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
