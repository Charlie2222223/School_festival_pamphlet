<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;

class PosterListController extends Controller
{
    public function index()
{
    $allClasses = Classes::all();
    $rClasses = Classes::where('class_name', 'like', 'R%')->get();
    $sClasses = Classes::where('class_name', 'like', 'S%')->get();
    $jClasses = Classes::where('class_name', 'like', 'J%')->get();

    return view('poster_list', [
        'allClasses' => $allClasses,
        'rClasses' => $rClasses,
        'sClasses' => $sClasses,
        'jClasses' => $jClasses,
    ]);
}
}