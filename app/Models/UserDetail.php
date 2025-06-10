<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = ['address', 'phone', 'city', 'salary', 'zip_code', 'admission_date'];

    public function user()
    {
        // 1:1
        return $this->belongsTo(User::class);
    }
}
