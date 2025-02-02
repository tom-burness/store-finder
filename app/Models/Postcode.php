<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'postcode',
        'coordinates'
    ];
}
