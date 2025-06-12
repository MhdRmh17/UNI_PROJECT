<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        // تأكد أنّ كل الدوال محمية بتوكن Sanctum
        $this->middleware('auth:sanctum');
    }

    /**
     * عرض جميع الـ Profiles
     */
    public function index()
    {
        $profiles = Profile::with('user')->get();
        return response()->json($profiles);
    }

    /**
     * عرض Profile معيّن
     */
    public function show($id)
    {
        $profile = Profile::with('user')->findOrFail($id);
        return response()->json($profile);
    }

    /**
     * إنشاء Profile جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'birthdate' => 'nullable|date',
            'address'   => 'nullable|string|max:255',
            // اضف هنا أي حقول شخصية أخرى
        ]);

        $profile = Profile::create($data);

        return response()->json($profile, 201);
    }

    /**
     * تحديث Profile موجود
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $data = $request->validate([
            'birthdate' => 'sometimes|nullable|date',
            'address'   => 'sometimes|nullable|string|max:255',
            // حقول أخرى بـ sometimes إذا كانت اختيارية
        ]);

        $profile->update($data);

        return response()->json($profile);
    }

    /**
     * حذف Profile
     */
    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $profile->delete();

        return response()->json(null, 204);
    }
}
