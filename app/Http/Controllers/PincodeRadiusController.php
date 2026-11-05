<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PincodeRadiusController extends Controller
{
    /**
     * Find all pincodes within a radius (in km) from a lat/lng coordinate
     */
    public function getInRadius(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $radius = (float) ($request->radius ?? 10.0);

        $results = Pincode::findInRadius($lat, $lng, $radius);

        $pincodes = $results->map(function ($p) {
            return [
                'pincode' => $p->pincode,
                'area_name' => $p->area_name,
                'city' => $p->city,
                'distance_km' => round((float) $p->distance, 1),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $pincodes->count(),
            'radius_km' => $radius,
            'pincode_list' => $pincodes->pluck('pincode')->join(', '),
            'items' => $pincodes,
        ]);
    }

    /**
     * Geocode an area or pincode into latitude & longitude
     */
    public function geocode(Request $request)
    {
        $query = trim($request->get('query', ''));
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Query is required'], 422);
        }

        // 1. Check local pincodes table first
        $local = Pincode::where('pincode', $query)
            ->orWhere('area_name', 'LIKE', "%{$query}%")
            ->first();

        if ($local) {
            return response()->json([
                'success' => true,
                'source' => 'local',
                'pincode' => $local->pincode,
                'area_name' => $local->area_name,
                'latitude' => (float) $local->latitude,
                'longitude' => (float) $local->longitude,
            ]);
        }

        // 2. Fallback to OpenStreetMap Nominatim geocoding (100% Free)
        try {
            $resp = Http::withHeaders([
                'User-Agent' => 'FMZGlow-App/1.0',
            ])->timeout(4)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query . ', India',
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($resp->successful() && !empty($resp->json())) {
                $item = $resp->json()[0];
                return response()->json([
                    'success' => true,
                    'source' => 'nominatim',
                    'area_name' => $item['display_name'] ?? $query,
                    'latitude' => (float) $item['lat'],
                    'longitude' => (float) $item['lon'],
                ]);
            }
        } catch (\Throwable $e) {
            // ignore network issues
        }

        return response()->json([
            'success' => false,
            'message' => 'Location not found',
        ], 404);
    }
}

