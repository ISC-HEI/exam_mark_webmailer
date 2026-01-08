<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarksController;

Route::get("/", [MarksController::class, 'showForm'])->name('marks.form');

Route::get('/marks', [MarksController::class, 'showForm'])->name('marks.form');
Route::post('/marks/check-validity', [MarksController::class, 'checkValidity'])->name('marks.checkValidity');
Route::post('/marks/send', [MarksController::class, 'sendMarks'])->name('marks.send');
Route::post('/marks/send-test', [MarksController::class, 'sendTestEmail'])->name('marks.send_test');

Route::post('/marks/add-student', [MarksController::class, 'addStudent'])->name('marks.add_student');
Route::post('/marks/remove-student', [MarksController::class, 'removeStudent'])->name('marks.remove_student');

Route::post('/marks/delete-global-attachment', [MarksController::class, 'deleteGlobalAttachments'])->name('marks.delete_global_attachment');

Route::post('/marks/load-csv', [MarksController::class, 'loadCsv'])->name('marks.load_csv');

Route::post('/marks/email-preview', [MarksController::class, 'emailPreview'])->name("email.preview");