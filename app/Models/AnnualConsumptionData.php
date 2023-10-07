<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnualConsumptionData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'annual_consumption_data';

    protected $fillable = [
        'profile_id',
        'client_id',
        'year_unit',
        'lower_consumption_unit'
    ];
}
