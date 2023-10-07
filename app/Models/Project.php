<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $fillable = [
        'client_id',
        'site_name',
        'project_location',
        'mapping_id',
        'clipping',
        'clipping_value',
        'total_capacity',
        'percentage_satisfied_value',
        'grid_power',
        'green_energy_power',
        'evacuation_capacity',
        'lapsed_unit',
        'connected_voltage',
        'connected_voltage_value',
        'wind_capex',
        'solar_capex',
        'total_capex',

    ];
    public function mapping(){
        return $this->hasOne('\App\Models\Mapping', 'id', 'mapping_id');
    }
}
