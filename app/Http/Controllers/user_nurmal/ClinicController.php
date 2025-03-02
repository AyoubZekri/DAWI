<?php

namespace App\Http\Controllers\user_nurmal;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Exception;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function nearbyClinics(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = 2;

            $clinics = Clinic::selectRaw("
            clinics.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?)) * sin(radians(latitude))
            )) AS distance
              ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', 2)
                ->orderBy('distance', 'asc')
                ->with('schedules')
                ->get()
                ->map(function ($clinic) {
                    $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
                    $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
                    return $clinic;
                });

            return response()->json([
                'status' => 'success',
                'count' => $clinics->count(),
                'clinics' => $clinics->toArray()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function allClinics()
    {
        try {
            $clinics = Clinic::with('schedules')->get()->map(function ($clinic) {
                $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
                $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
                return $clinic;
            });

            return response()->json([
                'status' => 'success',
                'count' => $clinics->count(),
                'clinics' => $clinics->toArray()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
