<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'address',
        'phone'
    ];
}
