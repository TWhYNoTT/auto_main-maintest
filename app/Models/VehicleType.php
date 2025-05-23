<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = ['name', 'icon'];
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
