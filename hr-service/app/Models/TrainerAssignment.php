<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerAssignment extends Model
{
    protected $fillable = [
        'trainer_id',
        'member_id',
        'schedule',
        'notes',
        'status',
    ];

    public function trainer()
    {
        return $this->belongsTo(Employee::class, 'trainer_id');
    }
}
