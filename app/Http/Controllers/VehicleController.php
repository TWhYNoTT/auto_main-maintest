<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    // Render the search page (which uses all HTML elements from main.html)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $vehicles = Vehicle::paginate($perPage);
        return view('vehicles.search', compact('vehicles'));

    }

    public function search(Request $request)
    {
        $vehicles = Vehicle::query();

        if ($request->filled('type_id')) {
            $vehicles->where('vehicle_type_id', $request->input('type_id'));
        }

        if ($request->filled('make_id')) {
            $vehicles->where('make_id', $request->input('make_id'));
        }

        if ($request->filled('state_id')) {
            $vehicles->where('state_id', $request->input('state_id'));
        }

        if ($request->filled('location_id')) {
            $vehicles->where('location_id', $request->input('location_id'));
        }

        // New Zip Code Search Logic
        if ($request->filled('search-by-zip') && $request->filled('zip_code')) {
            $zipCode = $request->input('zip_code');
            $radius = $request->input('search_radius', 50); // Default 50 miles

            // Assuming you have a method to find nearby locations
            $vehicles->whereHas('location', function($query) use ($zipCode, $radius) {
                $query->nearbyLocations($zipCode, $radius);
            });
        }

        if ($request->filled('from-year') && $request->filled('to-year')) {
            $vehicles->whereBetween('year', [$request->input('from-year'), $request->input('to-year')]);
        }

        if ($request->filled('from-mileage') && $request->filled('to-mileage')) {
            $vehicles->whereBetween('odometer', [$request->input('from-mileage'), $request->input('to-mileage')]);
        }

        if ($request->filled('keywords')) {
            $keywords = $request->input('keywords');
            $vehicles->where(function ($q) use ($keywords) {
                $q->where('description', 'LIKE', "%$keywords%")
                    ->orWhere('vin', 'LIKE', "%$keywords%")
                    ->orWhere('lot_number', 'LIKE', "%$keywords%");
            });
        }

        if ($request->filled('newly-added')) {
            $hours = match($request->input('newly-added')) {
                '1' => 24,
                '2' => 48,
                '7' => 7 * 24,
                default => null
            };

            if ($hours) {
                $vehicles->where('created_at', '>=', now()->subHours($hours));
            }
        }

        $vehicles->orderBy('auction_date', 'desc');

        $results = $vehicles->paginate(10);

        if ($request->ajax()) {
            return view('vehicles.search-results', ['vehicles' => $results]);
        }
    
        return view('vehicles.search', ['vehicles' => $results]);
            // return response()->json($results);
    }
}