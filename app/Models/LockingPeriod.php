<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockingPeriod extends Model
{
    use HasFactory;
    protected $fillable = ['id','locking_number','lockin_period_month'];
}
