<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_type_id', 'make_id', 'state_id', 'location_id',
        'vin', 'lot_number', 'model', 'year', 'damage', 'odometer',
        'auction_date', 'description', 'images'
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class, 'make_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function getImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function scopeNewlyAdded($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeSearchByKeywords($query, $keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $q->where('description', 'LIKE', "%$keywords%")
              ->orWhere('vin', 'LIKE', "%$keywords%")
              ->orWhere('lot_number', 'LIKE', "%$keywords%");
        });
    }
}