<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'doc',
        'email',
        'phone',
        'city',
        'state',
        'street',
        'number',
        'complement',
        'district',
        'zipcode',
        'additional',
    ];

    protected $casts = [
        'additional' => 'array',
    ];

    protected $attributes = [
        'additional' => '[]',
    ];
}
