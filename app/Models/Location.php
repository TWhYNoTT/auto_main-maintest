<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    protected $fillable = ['state_id', 'name'];
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Scope a query to find locations within a given radius of a zip code
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $zipCode
     * @param int $radius Distance in miles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearbyLocations($query, $zipCode, $radius = 50)
    {
        // First, get the coordinates for the given zip code
        $zipCoordinates = $this->getZipCodeCoordinates($zipCode);

        if (!$zipCoordinates) {
            return $query->whereRaw('1=0'); // Return no results if zip not found
        }

        // Haversine formula to calculate distance
        return $query->select('*')
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * 
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians(?) ) + 
                sin( radians(?) ) * 
                sin( radians( latitude ) ) ) AS distance', 
                [$zipCoordinates['latitude'], $zipCoordinates['longitude'], $zipCoordinates['latitude']])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    /**
     * Retrieve coordinates for a given zip code
     * 
     * @param string $zipCode
     * @return array|null
     */
    private function getZipCodeCoordinates($zipCode)
    {
        // This is a placeholder. In a real app, you'd:
        // 1. Use a zip code database
        // 2. Potentially use a geocoding API
        // 3. Cache results
        $zipData = DB::table('zip_codes')->where('zip', $zipCode)->first();

        return $zipData ? [
            'latitude' => $zipData->latitude,
            'longitude' => $zipData->longitude
        ] : null;
    }
}