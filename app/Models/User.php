<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    
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
