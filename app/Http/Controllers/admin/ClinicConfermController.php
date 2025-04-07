<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clinic;
use Exception;

class ClinicConfermController extends Controller
{
    public function allClinicsNotConferm()
    {
        try {
            $clinics = Clinic::with('schedules', 'municipality')->where('Statue', 0)->get()->map(function ($clinic) {
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



    public function approveClinic($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);

            if ($clinic->Statue == 1) {
                return response()->json(['message' => 'العيادة معتمدة بالفعل'], 200);
            }

            $clinic->Statue = 1;
            $clinic->save();

            return response()->json(['message' => 'تمت الموافقة على العيادة بنجاح'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'العيادة غير موجودة'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب', 'error' => $e->getMessage()], 500);
        }
    }


    public function searchClinicsNotConferm(Request $request)
    {
        try {
            $query = Clinic::with(['schedules', 'municipality'])
                ->where('Statue', 0);

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
                ],
                'clinics' => $clinics->items()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء البحث',
                'error' => $e->getMessage(),
                'hint' => 'تأكد من أن حقول البحث موجودة في جدول العيادات'
            ], 500);
        }
    }

}
