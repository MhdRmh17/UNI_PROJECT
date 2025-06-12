<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectFormController;

// تسجيل ودخول
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum');


// راوتات محمية بتوكن sanctum
Route::middleware('auth:sanctum')->group(function () {

    // المشاريع الخاصة بالمستخدم الحالي
    Route::get('/my-projects', [ProjectFormController::class, 'myProjects']);

    // فقط الأدمن يعدل الحالة
  Route::patch('/projects/{id}/status', [ProjectFormController::class, 'updateStatus'])
     ->middleware('auth:sanctum');


    // عرض كل المشاريع (يتم التحقق من نوع المستخدم داخل الدالة نفسها)
    Route::get('/projects', [ProjectFormController::class, 'index']);

    // موارد المشاريع والبروفايلات
    Route::apiResource('projects', ProjectFormController::class);
    Route::apiResource('profiles', ProfileController::class);
});
