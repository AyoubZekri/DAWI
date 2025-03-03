<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class GoogleAuth extends Controller
{
    public function GoogleLogin(Request $request)
    {
        try {
            $googleToken = $request->input('token');

            $googleUser = Socialite::driver('google')->userFromToken($googleToken);

            $roleId = 2;
            $user = User::where('email', $googleUser->email)->first();
            if ($user) {

                if ($user->isUser()) {
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'access_token' => $token,
                        'role_id' => $roleId,
                        'user' => $user,
                    ]);
                }else{
                    return response()->json([
                        'error' => ' ليس لديك صلاحيات '
                    ], 403);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make("password@1234"),
                    'google_id' => $googleUser->id,
                    'user_role' => $roleId
                ]);


                DB::table('roles_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
                Mail::to($user['email'])->send(new WelcomeMail($user));
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'role_id' => $roleId,
                    'user' => $user,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء تسجيل الدخول'], 500);
        }
    }

}
