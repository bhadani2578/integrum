<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualSourceData extends Model
{
    use HasFactory;
     protected $table = 'annual_source_profiles';

    protected $fillable = [
        'profile_id',
        'client_id',
        'year_unit',
        'lower_consumption_unit'
    ];

}
