<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HourlySourceData extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'hourly_source_data';

    protected $fillable = [
        'profile_id',
        'client_id',
        'hours',
        'consumed_unit'
    ];
}
