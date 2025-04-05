<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{

    public function index($id)
    {
        if (!Clinic::where('id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'العيادة غير موجودة'
            ], 404);
        }

        $doctors = Doctor::with('clinic')
            ->where('clinic_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'doctors' => $doctors
        ], 200);
    }

    public function showdoctor($id)
    {
        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الطبيب غير موجودة'
                ], 404);
            }

            // $clinic->cover_image = $clinic->cover_image ? asset('storage/' . $clinic->cover_image) : null;
            // $clinic->profile_image = $clinic->profile_image ? asset('storage/' . $clinic->profile_image) : null;
            // $clinic->specialty_name = $clinic->specialty ? $clinic->specialty->name : null;
            // unset($clinic->specialty);

            return response()->json([
                'status' => 'success',
                'clinic' => $doctor
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'clinic_id' => 'required|exists:clinics,id',
            'specialties_id' => 'required|exists:specialties,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified' => rand(10000, 99999),
            'user_role' => 4,
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'clinic_id' => $request->clinic_id,
            'specialties_id' => $request->specialties_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'تم إنشاء الطبيب والمستخدم بنجاح',
            'doctor' => $doctor,
            'user' => $user
        ], 201);
    }




    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'الطبيب غير موجود'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6',
            'specialties_id' => 'required|exists:specialties,id',
            'clinic_id' => 'required|exists:clinics,id',

        ]);

        $doctor->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $doctor->user->password,
        ]);

        $doctor->update([
            'clinic_id' => $request->clinic_id,
            'specialties_id' => $request->specialties_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'تم تحديث بيانات الطبيب بنجاح',
            'doctor' => $doctor
        ], 200);
    }

    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'الطبيب غير موجود'], 404);
        }

        $doctor->user->delete();

        $doctor->delete();

        return response()->json(['message' => 'تم حذف الطبيب والمستخدم بنجاح'], 200);
    }
}
