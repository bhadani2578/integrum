<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodStateConsumptionData extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'client_id',
        'slot',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'consumed_unit',
    ];
}
