<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'invoice_number',
        'email',
        'name',
        'phone',
        'is_paid',
    ];

    public function details()
    {
        return $this->hasMany(Detail::class);
    }
}
