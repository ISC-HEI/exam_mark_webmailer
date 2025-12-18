<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksController;

Route::get("/", [MarksController::class, 'showForm'])->name('marks.form');

Route::get('/marks', [MarksController::class, 'showForm'])->name('marks.form');
Route::post('/marks/send', [MarksController::class, 'sendMarks'])->name('marks.send');

Route::post('/marks/add-student', [MarksController::class, 'addStudent'])->name('marks.add_student');
Route::post('/marks/remove-student', [MarksController::class, 'removeStudent'])->name('marks.remove_student');

Route::post('/marks/load-csv', [MarksController::class, 'loadCsv'])->name('marks.load_csv');
Route::post('/marks/reset-message', [MarksController::class, 'resetMessage'])->name('marks.reset_message');