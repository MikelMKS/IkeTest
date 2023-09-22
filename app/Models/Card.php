<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $table = 'cards';
    protected $primaryKey = 'id';

    protected $fillable = [
        'card_id',
        'capture_country',
        'policy_version',
        'language',
    ];
}
