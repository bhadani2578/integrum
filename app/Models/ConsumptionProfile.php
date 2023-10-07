<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsumptionProfile extends Model
{
    use  HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'point_name',
        'client_id',
        'state_id',
        'voltage_id',
        'discom_id',
        'wheeling_charge',
        'discom_category_id',
        'contract_demand',
        'contract_unit',
        'contract_demand_limitation',
        'category_consumption_id',
        'granularity_level_id',
        'granularity_id',
        'consumption_file_path'
    ];

    public function state(){
        return $this->hasOne('\App\Models\State', 'id', 'state_id');
    }

    public function voltage(){
        return $this->hasOne('\App\Models\Voltage', 'id', 'voltage_id');
    }

    public function discom(){
        return $this->hasOne('\App\Models\Discom', 'id', 'discom_id');
    }

    public function ed_detail(){
        return $this->hasOne('\App\Models\ConsumptionEdType', 'profile_id', 'id');
    }

    public function day_shift(){
        return $this->hasOne('\App\Models\ConsumptionDayShift', 'profile_id', 'id');
    }

    public function tod_value(){
        return $this->hasMany('\App\Models\ConsumptionTod', 'profile_id', 'id');
    }
}
