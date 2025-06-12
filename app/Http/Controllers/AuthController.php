<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد.
     */
    public function register(Request $request)
    {
        // 1. التحقق من صحة البيانات
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'username'   => 'required|string|max:50|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:6|confirmed',
            // 'password_confirmation' يأتي أوتوماتيكيًا مع 'confirmed'
        ]);

        // 2. إنشاء المستخدم
        $user = User::create([
            'name'          => $data['name'],
            'phone'         => $data['phone'] ?? null,
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
                'type' => $request->type, // ← هاي مهمة
            'registered_at' => now(),
            // حقل 'type' يبقى افتراضيًا كما في الميغريشن ('student')
        ]);

        // 3. إنشِئ توكن API جديد
        $token = $user->createToken('api_token')->plainTextToken;

        // 4. رجّع استجابة JSON
        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /**
     * تسجيل دخول مستخدم موجود.
     */
   public function login(Request $request)
    {
        // Validate using username instead of email
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'حقل اسم المستخدم مطلوب.',
            'username.exists'   => 'اسم المستخدم غير موجود.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.min'      => 'كلمة المرور يجب أن تتكون من 6 أحرف على الأقل.',
        ]);

        // Attempt to authenticate with username
        $credentials = $request->only('username', 'password');
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة.'
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }
  public function logout(Request $request)
    {
        $request->user()
                ->currentAccessToken()
                ->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.'
        ], 200);
    }
}
