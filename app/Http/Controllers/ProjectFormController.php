<?php

namespace App\Http\Controllers;

use App\Models\ProjectForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * عرض جميع نماذج المشاريع للمستخدم الجاري
     */
public function index()
{
    // نفترض موديل ProjectForm مربوط بـ User عبر user()
    // هنا نجلب فقط الحقول اللازمة من المشروع ومن المستخدم
    $projects = ProjectForm::with([
        'user:id,name,email,phone'   // جلب هذه الحقول فقط من جدول users
    ])->get(['id','user_id','title','description','pdf_path','status']);

    return response()->json($projects, 200);
}



    /**
     * عرض نموذج مشروع معيّن
     */
    public function show(Request $request, $id)
    {
        $form = ProjectForm::where('user_id', $request->user()->id)
                           ->findOrFail($id);
        return response()->json($form);
    }

    /**
     * إنشاء نموذج مشروع جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'supervisor'   => 'required|string|max:255',
            'submitted_at' => 'required|date',
            'pdf'          => 'required|file|mimes:pdf|max:10240',
            'description'  => 'nullable|string',
            // حقول أخرى إذا وجدت...
        ]);

        // خزن الـ PDF في التخزين العام
        $path = $request->file('pdf')->store('project_pdfs', 'public');

        $form = ProjectForm::create([
            'user_id'      => $request->user()->id,
            'title'        => $data['title'],
            'supervisor'   => $data['supervisor'],
            'submitted_at' => $data['submitted_at'],
            'pdf_path'     => $path,
            'description'  => $data['description'] ?? null,
        ]);

        return response()->json($form, 201);
    }

    /**
     * تحديث نموذج مشروع موجود
     */
    public function update(Request $request, $id)
    {
        $form = ProjectForm::where('user_id', $request->user()->id)
                           ->findOrFail($id);

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'supervisor'   => 'sometimes|string|max:255',
            'submitted_at' => 'sometimes|date',
            'pdf'          => 'sometimes|file|mimes:pdf|max:10240',
            'description'  => 'sometimes|nullable|string',
        ]);

        // إذا جاء PDF جديد، احذفه القديم وخزن الجديد
        if ($request->hasFile('pdf')) {
            Storage::disk('public')->delete($form->pdf_path);
            $data['pdf_path'] = $request->file('pdf')->store('project_pdfs', 'public');
        }

        $form->update($data);

        return response()->json($form);
    }

    /**
     * حذف نموذج مشروع
     */
    public function destroy(Request $request, $id)
    {
        $form = ProjectForm::where('user_id', $request->user()->id)
                           ->findOrFail($id);

        // احذف ملف الـ PDF أولاً
        Storage::disk('public')->delete($form->pdf_path);

        $form->delete();

        return response()->json(null, 204);
    }
    public function myProjects(Request $request)
{
    $user = $request->user();

    $projects = \App\Models\ProjectForm::where('user_id', $user->id)
                ->orderBy('submitted_at', 'desc')
                ->get();

    return response()->json([
        'user' => $user->name,
        'projects_count' => $projects->count(),
        'projects' => $projects,
    ]);
}

public function updateStatus(Request $request, $id)
{
    if (auth()->user()->type !== 'admin') {
        return response()->json([
            'message' => 'Unauthorized: Only admins can update the status.'
        ], 403);
    }

    $request->validate([
        'status' => 'required|in:pending,accepted,rejected',
    ]);

    $project = ProjectForm::findOrFail($id);
    $project->status = $request->status;
    $project->save();

    return response()->json(['message' => 'Status updated successfully.']);
}

}
