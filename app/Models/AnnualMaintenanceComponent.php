<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AnnualMaintenanceComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'solar_maintenance',
        'soalr_free',
        'solar_escalation',
        'wind_maintenance',
        'wind_free',
        'wind_escalation',
        'bop_maintenance',
        'bop_free',
        'bop_escalation'
    ];
}
