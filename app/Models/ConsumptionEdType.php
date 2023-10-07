<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsumptionEdType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',    
        'ed_type',
        'waiver_time',
        'available_upto',
        'waiver_month',
        'waiver_year',
        'rebate_type',
        'rebate_value',
    ];

}
