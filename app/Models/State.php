<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'abbreviation'];
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}