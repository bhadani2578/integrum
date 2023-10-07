<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    use HasFactory;
    protected $table = 'mapping';
    protected $fillable = [
        'client_id',
        'mapping_name',
        'consumption_point_id',
        'source_point_id',
        'c_to_s_priority',
        's_to_c_priority',
        'granularity_level_id',
        'quantum_min',
        'quantum_max',
        'duration',
    ];
    public function consumption_profile(){
        return $this->hasOne('\App\Models\ConsumptionProfile', 'id', 'consumption_point_id');
    }
    public function source_profile(){
        return $this->hasOne('\App\Models\SourceProfile', 'id', 'source_point_id');
    }
    public function mapping_priority(){
        return $this->hasOne('\App\Models\MappingPriority', 'id', 'c_to_s_priority');
    }
}
