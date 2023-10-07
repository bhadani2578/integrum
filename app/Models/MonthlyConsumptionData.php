<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyConsumptionData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'client_id',
        'name',
        'consumed_unit'
    ];
}
