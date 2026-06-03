<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'member_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'amount_paid',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
