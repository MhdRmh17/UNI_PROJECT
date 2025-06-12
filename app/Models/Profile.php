<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للملء (إنشاء وتحديث)
     */
    protected $fillable = [
        'user_id',
        'birthdate',
        'address',
        // أضف أي حقول شخصية إضافية هنا
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
