<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id', 'timestamp', 'latitude', 'longitude',
    ];

    protected $dates = ['timestamp'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
