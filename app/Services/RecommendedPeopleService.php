<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RecommendedPeopleService
{
    /**
     * Get recommended people based on location.
     *
     * @param  float  $latitude
     * @param  float  $longitude
     * @param  int  $page
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function getRecommendedPeople(float $latitude, float $longitude, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        // Calculate distance using Haversine formula
        // Formula: 6371 is Earth's radius in kilometers
        // Using CAST to ensure latitude/longitude are treated as numeric
        $distanceFormula = "(
            6371 * acos(
                cos(radians(?)) *
                cos(radians(CAST(latitude AS DECIMAL(10, 8)))) *
                cos(radians(CAST(longitude AS DECIMAL(11, 8))) - radians(?)) +
                sin(radians(?)) *
                sin(radians(CAST(latitude AS DECIMAL(10, 8))))
            )
        ) AS distance_km";

        $users = User::select('users.*')
            ->selectRaw($distanceFormula, [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw('CAST(latitude AS DECIMAL(10, 8)) IS NOT NULL')
            ->whereRaw('CAST(longitude AS DECIMAL(11, 8)) IS NOT NULL')
            ->whereRaw('CAST(latitude AS DECIMAL(10, 8)) BETWEEN -90 AND 90')
            ->whereRaw('CAST(longitude AS DECIMAL(11, 8)) BETWEEN -180 AND 180')
            ->with(['pictures' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('distance_km', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Add distance_km to each user model
        $users->getCollection()->transform(function ($user) {
            $user->distance_km = round($user->distance_km, 1);
            return $user;
        });

        return $users;
    }
}

