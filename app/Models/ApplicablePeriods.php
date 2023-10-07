<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicablePeriods extends Model
{
    use HasFactory;
    protected $fillable = ['id','name'];
}
