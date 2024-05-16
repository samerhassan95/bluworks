<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'age', 'join_date', 'termination_date',
    ];

    protected $dates = ['join_date', 'termination_date'];

    public function clockIns()
    {
        return $this->hasMany(ClockIn::class);
    }
}
