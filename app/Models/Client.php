<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use  HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_name',
        'parent_group',
        'person_name',
        'designation',
        'email',
        'country_code',
        'phone',
        'lead_type',
        'consultant_name',
        'comission_fee',
        'type_of_industry',
        'consumption_point_no',
        'source_point_no',
        'is_metadata',
        'status',
    ];

 
}
