<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;
    protected $table = 'consents';
    protected $primaryKey = 'id';
    const UPDATED_AT = null;

    protected $fillable = [
        'consent_type',
        'account_level'
    ];
}
