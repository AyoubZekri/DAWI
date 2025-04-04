<?php

namespace App\Http\Controllers\clinic\auth;

use App\Http\Controllers\Controller;
use App\Mail\confermemail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class Register extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'register' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'municipalitie_id' => 'required|exists:municipalities,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'clinic_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $registerPath = $request->file('register')->store('clinic_registers', 'public');

            $clinicImagePath = $request->file('clinic_image')->store('clinic_images', 'public');
            $cover_image = $request->file('cover_image')->store('clinic_images', 'public');

            $email_verified_at = rand(10000, 99999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified' => $email_verified_at,
                'user_role' => 3,
            ]);

            $clinic = Clinic::create([
                'user_id' => $user->id,
                'municipalities_id' => $request->municipalitie_id,
                'name' => $request->name,
                'pharm_name_fr' => $request->name_fr,
                'address' => $request->address,
                'register' => $registerPath,
                'email' => $user->email,
                'profile_image' => $clinicImagePath,
                "cover_image"=> $cover_image,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'type' => 'عيادة'
            ]);


            Mail::to($user->email)->send(new confermemail($user));

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الحساب والعيادة بنجاح',
                'user' => $user,
                'clinic' => $clinic,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء إنشاء الحساب أو العيادة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json(['status' => 'error', 'message' => 'العيادة غير موجودة'], 404);
        }

        $clinic->update($request->only(['name', 'pharm_name_fr', 'address', 'latitude', 'longitude']));

        if ($request->hasFile('clinic_image')) {
            Storage::delete('public/' . $clinic->profile_image);
            $clinic->profile_image = $request->file('clinic_image')->store('clinic_images', 'public');
        }

        if ($request->hasFile('cover_image')) {
            Storage::delete('public/' . $clinic->cover_image);
            $clinic->cover_image = $request->file('cover_image')->store('clinic_images', 'public');
        }

        $clinic->save();

        return response()->json(['status' => 'success', 'message' => 'تم تحديث بيانات العيادة بنجاح', 'clinic' => $clinic]);
    }

    public function destroy($id)
    {
        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json(['status' => 'error', 'message' => 'العيادة غير موجودة'], 404);
        }

        Storage::delete(['public/' . $clinic->profile_image, 'public/' . $clinic->cover_image, 'public/' . $clinic->register]);
        $clinic->delete();

        return response()->json(['status' => 'success', 'message' => 'تم حذف العيادة بنجاح']);
    }
}
