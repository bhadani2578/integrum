<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProjectDetailComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'no_turbines',
        'solar_mwp',
        'dc_ac_ratio',
        'solar_unit_mwp',
        'solar_deration',
        'wind_capacity_mws',
        'wind_gen_unit_turbine',
        'total_gen',
        'solar_capex',
        'wind_capex',
        'total_capex'
    ];
}
