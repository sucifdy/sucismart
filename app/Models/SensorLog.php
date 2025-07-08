<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $fillable = ['suhu', 'kelembaban', 'cahaya', 'flame', 'gas'];
}
