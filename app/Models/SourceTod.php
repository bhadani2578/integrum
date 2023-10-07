<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceTod extends Model
{
    use HasFactory;
    protected $table ='tod_source_data';
    protected $fillable = [
        'profile_id',
        'tod_slot_id',
        'tod_start',
        'tod_end',
        'tod_value'
    ];
}
