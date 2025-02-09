<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    /** @use HasFactory<\Database\Factories\PostcodeFactory> */
    use HasFactory;

    protected $fillable = [
        'postcode',
        'coordinates',
        'lat',
        'long'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'long' => 'float',
        ];
    }
}
