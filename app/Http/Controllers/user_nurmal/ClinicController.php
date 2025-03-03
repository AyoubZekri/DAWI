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
                ->with('specialty')
                ->with('schedules')
                ->get()
                ->map(function ($clinic) {
                    $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
                    $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
                    $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
                    unset($clinic->specialty);
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
            $clinics = Clinic::with('schedules', 'specialty')->get()->map(function ($clinic) {
                $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
                $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
                $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
                unset($clinic->specialty);
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



    public function searchClinics(Request $request)
    {
        $query = Clinic::with(['schedules', 'specialty']); // جلب العيادات مع التخصص والجدول الزمني

        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('pharm_name_fr')) {
            $query->where('pharm_name_fr', 'LIKE', '%' . $request->pharm_name_fr . '%');
        }

        if ($request->has('specialty')) {
            $query->whereHas('specialty', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->specialty . '%');
            });
        }

        $clinics = $query->get()->map(function ($clinic) {
            return [
                'id' => $clinic->id,
                'name' => $clinic->name,
                'address' => $clinic->address,
                'phone' => $clinic->phone,
                'email' => $clinic->email,
                'pharm_name_fr' => $clinic->pharm_name_fr,
                'type' => $clinic->type,
                'latitude' => $clinic->latitude,
                'longitude' => $clinic->longitude,
                'cover_image' => $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null,
                'profile_image' => $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null,
                'specialty' => $clinic->specialty->name ?? null,
                'schedules' => $clinic->schedules->map(function ($schedule) {
                    return [
                        'day' => $schedule->day,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                    ];
                }),
            ];
        });

        return response()->json($clinics);
    }

}
