<?php

namespace App\Services;

class HaversineService
{
    /**
     * Radius rata-rata bumi dalam meter.
     */
    protected const EARTH_RADIUS_METERS = 6371000;

    /**
     * Menghitung jarak antara dua koordinat GPS menggunakan formula Haversine.
     *
     * @param float $lat1 Latitude titik 1 (misal: posisi guru)
     * @param float $lon1 Longitude titik 1 (misal: posisi guru)
     * @param float $lat2 Latitude titik 2 (misal: sekolah)
     * @param float $lon2 Longitude titik 2 (misal: sekolah)
     * @return float Jarak dalam meter
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * self::EARTH_RADIUS_METERS, 2);
    }

    /**
     * Memeriksa apakah suatu koordinat berada di dalam radius sekolah.
     *
     * @param float $lat Latitude pengguna
     * @param float $lon Longitude pengguna
     * @return array ['in_radius' => bool, 'distance_meters' => float, 'max_radius' => int]
     */
    public static function checkInSchoolRadius(float $lat, float $lon): array
    {
        $schoolLat = (float) config('school.latitude', -3.604806);
        $schoolLon = (float) config('school.longitude', 114.700306);
        $maxRadius = (int) config('school.radius_meter', 100);

        $distance = self::calculateDistance($lat, $lon, $schoolLat, $schoolLon);

        return [
            'in_radius' => $distance <= $maxRadius,
            'distance_meters' => $distance,
            'max_radius' => $maxRadius,
            'school_lat' => $schoolLat,
            'school_lon' => $schoolLon,
        ];
    }
}
