<?php

namespace App\Http\Controllers\clinic\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class login extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password) || $user->user_role != 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة أو ليس لديك صلاحية الدخول.',
                ], 401);
            }

            // if (!$user->email_verified) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'يجب تأكيد البريد الإلكتروني أولاً.',
            //     ], 403);
            // }

            $clinic = Clinic::where('user_id', $user->id)->first();

            if (!$clinic) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على بيانات العيادة.',
                ], 404);
            }

            if (!$clinic->Statue == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'انتضر حتى يتم الموافقة على العيادة',
                ], 404);
            }

            if ($clinic->Statue == 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => "لم يتم الموافقة على العيادة ",
                    'content' => "هناك خطا في السجل او العيادة غير معترف بها",
                ], 404);
            }

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح.',
                'user' => $user,
                'clinic' => $clinic,
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء محاولة تسجيل الدخول.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الخروج بنجاح.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الخروج.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
