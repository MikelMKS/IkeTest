<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $table = 'history';
    protected $primaryKey = 'id';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'id_card',
        'id_consent1',
        'id_consent2',
        'id_consent3',
    ];
}
