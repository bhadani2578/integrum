<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LoanComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'gst',
        'income_tax',
        'cash_equity',
        'debt',
        'total_fund',
        'rate_of_interest',
        'repayment_period',
        'moratorium',
        'tax_rate',
        'depreciation_rate',
        'addl_depreciation_rate'
    ];
}
