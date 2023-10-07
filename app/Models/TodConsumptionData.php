<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodConsumptionData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'client_id',
        'name',
        'tb_1',
        'tb_2',
        'tb_3',
        'tb_4',
        'tb_5',
    ];
}
