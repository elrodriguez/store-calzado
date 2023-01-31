<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
        'name',
        'description',
        'ruc',
        'telephone',
        'email',
        'image',
        'address',
        'contact_telephone',
        'contact_name',
        'contact_email',
        'ubigeo'
    ];

    public function getImageAttribute($value)
    {
        return ($value != 'img/imagen-no-disponible.jpeg' ? asset('storage/' . $value) : asset($value));
    }
}
