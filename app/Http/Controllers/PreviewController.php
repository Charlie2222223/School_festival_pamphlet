<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\UploadedImage;

class PreviewController extends Controller
{
    public function index()
    {
        if (!session()->has('class_id')) {
            return redirect('/login');
        }

        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('preview', compact('rClasses', 'sClasses', 'jClasses'));
    }

    public function previewPage()
    {
        if (!session()->has('class_id')) {
            return redirect('/login');
        }

        $classId = session('class_id');
        $class_name = session('class_name');
        $class = Classes::find($classId);
        $uploadedImages = UploadedImage::where('class_id', $classId)->get();

        // クラス分類も渡す
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('preview', [
            'class' => $class,
            'uploadedImages' => $uploadedImages,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
            'class_name' => $class_name,
            // 追加: 保存済みコード
            'html_code' => $class?->html_code,
            'css_code'  => $class?->css_code,
            'js_code'   => $class?->js_code,
        ]);
    }

    public function showPreview()
    {
        $classId = session('class_id');
        $class = Classes::find($classId);

        return view('preview', [
            'uploadedImages' => $class->uploadedImages ?? [],
            'html_code' => $class->html_code,
            'css_code'  => $class->css_code,
            'js_code'   => $class->js_code,
            'rClasses'  => Classes::where('division', 'R')->get(),
            'sClasses'  => Classes::where('division', 'S')->get(),
            'jClasses'  => Classes::where('division', 'J')->get(),
        ]);
    }
    
}