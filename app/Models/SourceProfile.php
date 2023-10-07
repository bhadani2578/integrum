<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SourceProfile extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id',
    'client_id',
    'source_name',
    'type_source_id',
    'type_arrangement_id',
    'type_contract_id',
    'banking_arragement_id',
    'settlement_id',
    'state_id',
    'discoms_id',
    'voltage_id',
    'annual_traffic_type',
    'annual_traffic_value',
    'start_date',
    'end_date',
    'quantum',
    'minimum_off_take',
    'applicable_period_id',
    'locking_period_id',
    'locking_period_month_id',
    'source_profile_path',
    'granularity_id',
    'granularity_level_id',
    'supply_commitment',
    'minimum_supply',
    'loan',
    'annual_maintain',
    'insurance',
    'revenue_unit',
    'depreciation_benefit',
    'transmission_charges',
    'wheeling_charges',
    'electricity_duty',
    'asset_fees',
    'energy_landed_cost',
    'statutory_charge'
];
    public function state(){
        return $this->hasOne('\App\Models\State', 'id', 'state_id');
    }

    public function type_source(){
        return $this->hasOne('\App\Models\TypeSource', 'id', 'type_source_id');
    }

    public function contract(){
        return $this->hasOne('\App\Models\TypeContract', 'id', 'type_contract_id');
    }

    public function arrangement(){
        return $this->hasOne('\App\Models\TypeArrangement', 'id', 'type_arrangement_id');
    }

    public function banking_arrangement(){
        return $this->hasOne('\App\Models\BankingArrangement', 'id', 'banking_arragement_id');
    }

    public function day_shift(){
        return $this->hasOne('\App\Models\ConsumptionDayShift', 'profile_id', 'id');
    }

    public function voltage(){
        return $this->hasOne('\App\Models\Voltage', 'id', 'voltage_id');
    }
}
