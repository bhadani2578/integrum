<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySourceData extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'monthly_source_data';
     protected $fillable = [
        'profile_id',
        'client_id',
        'name',
        'consumed_unit'
    ];
}
