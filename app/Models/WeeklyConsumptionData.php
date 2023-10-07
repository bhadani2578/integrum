<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyConsumptionData extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile_id',
        'client_id',
        'weeks',
        'consumed_unit',
    ];
}
