<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectForm extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للملء (إنشاء وتحديث)
     */
    protected $fillable = [
        'user_id',
        'title',
        'supervisor',
        'submitted_at',
        'pdf_path',
        'description',
        'status',
        // أضف أي حقول مشروع إضافية هنا
    ];

    /**
     * العلاقة مع المستخدم
     */
public function user()
{
    return $this->belongsTo(User::class);
}

}
   
