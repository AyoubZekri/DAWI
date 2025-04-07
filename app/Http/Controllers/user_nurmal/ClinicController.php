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
                ->where('Statue', 1)
                ->having('distance', '<=', 2)
                ->orderBy('distance', 'asc')
                ->with('municipality')
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
            $clinics = Clinic::with('schedules', 'municipality')->where('Statue', 1)->get()->map(function ($clinic) {
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

    public function showClinic($id)
    {
        try {
            $clinic = Clinic::with('schedules', 'municipality')->where('Statue', 1)->find($id);

            if (!$clinic) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'العيادة غير موجودة'
                ], 404);
            }

            $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
            $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
            $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
            unset($clinic->specialty);

            return response()->json([
                'status' => 'success',
                'clinic' => $clinic
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getBySpecialty($id)
    {
        try {
            $clinics = Clinic::whereHas('doctors', function ($query) use ($id) {
                $query->where('specialties_id', $id);
            })
                ->with([
                    'doctors' => function ($query) use ($id) {
                        $query->where('specialties_id', $id);
                    }
                ])
                ->get();

            return response()->json([
                'status' => 'success',
                'clinics' => $clinics
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
        try {
            $query = Clinic::with(['schedules', 'municipality'])->where('Statue', 1);

            if ($request->has('name')) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            }

            if ($request->has('address')) {
                $query->where('address', 'LIKE', '%' . $request->address . '%');
            }

            if ($request->has('pharm_name_fr')) {
                $query->where('pharm_name_fr', 'LIKE', '%' . $request->pharm_name_fr . '%');
            }

            if ($request->has('municipality')) {
                $query->whereHas('municipality', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->municipality . '%');
                });
            }

            $clinics = $query->paginate(10);

            $clinics->getCollection()->transform(function ($clinic) {
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
                    'municipality' => $clinic->municipality->name ?? null,
                    'schedules' => $clinic->schedules->map(function ($schedule) {
                        return [
                            'day' => $schedule->day,
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 'success',
                'pagination' => [
                    'total' => $clinics->total(),
                    'per_page' => $clinics->perPage(),
                    'current_page' => $clinics->currentPage(),
                    'last_page' => $clinics->lastPage(),
                    'from' => $clinics->firstItem(),
                    'to' => $clinics->lastItem(),
                ],
                'clinics' => $clinics->items()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء البحث',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
