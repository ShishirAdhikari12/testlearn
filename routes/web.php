<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome', [
        'students' => App\Models\Student::with(['comments'=>function($query){
            $query->orderBy('id', 'desc');
        }])->get()
    ]);
});

$students = App\Models\Student::with(['comments'=>function($query){
    $query->orderBy('id', 'desc');
}])->get();
