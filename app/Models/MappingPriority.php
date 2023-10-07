<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingPriority extends Model
{
    use HasFactory;
    protected $table = 'mapping_priority';
    protected $fillable = [
        'digit',
        'latters',
    ];
}
