<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;

class CodeController extends Controller
{
    public function save(Request $request)
    {
        $html = $request->input('html');
        $css  = $request->input('css');
        $js   = $request->input('js');
    
        $classId = session('class_id');

        // クラスが存在すれば更新、なければ作成
        $class = Classes::find($classId);
    
        if ($class) {
            $class->html_code = $html;
            $class->css_code  = $css;
            $class->js_code   = $js;
            $class->save();

            return response()->json(['success' => true]);
        } else {
            // クラスIDが見つからないときの処理
            return response()->json(['success' => false, 'message' => 'クラスが見つかりません']);
        }
    }
}