<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentMarkMail;
use App\Http\Requests\MarksRequest;
use Illuminate\Support\Facades\Storage;

class MarksController extends Controller
{
    public function showForm(Request $request)
    {
        $students = old('students', $request->session()->get('students', []));

        return view('marks.form', compact('students'));
    }

    public function addStudent(Request $request)
    {
        $existingStudents = $request->input('students', []); 
        
        $newStudent = ['name' => '', 'email' => '', 'mark' => ''];

        $updatedStudents = array_merge($existingStudents, [$newStudent]);

        $request->session()->put('students', $updatedStudents);

        $request->flashOnly(['course_name', 'exam_name', 'teacher_email', 'message']);

        return redirect()->route('marks.form');
    }

    public function removeStudent(Request $request)
    {
        $existingStudents = $request->input('students', []); 
        $indexToRemove = $request->input('remove_index');

        if (isset($existingStudents[$indexToRemove])) {
            unset($existingStudents[$indexToRemove]);
            $updatedStudents = array_values($existingStudents); 
        } else {
            $updatedStudents = $existingStudents;
        }

        $request->session()->put('students', $updatedStudents);
        $request->flashOnly(['course_name', 'exam_name', 'teacher_email' ,'message']);

        return redirect()->route('marks.form');
    }

    public function loadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $students = [];
        if (($handle = fopen($request->file('csv_file')->getRealPath(), 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $students[] = [
                    'name' => $data[0] ?? '',
                    'email' => $data[1] ?? '',
                    'mark' => $data[2] ?? '',
                ];
            }
            fclose($handle);
        }

        $request->session()->put('students', $students);

        return redirect()->route('marks.form');
    }

    public function sendMarks(MarksRequest $request)
    {
        foreach ($request->students as $index => $student) {
            if (!empty($student['name']) && !empty($student['email'])) {

                $requiredVariables = ['[STUDENT_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]'];
                $missingVariables = [];

                $average = $this->getClassAverage($request->students);
                $files = $request->file('students');

                foreach ($requiredVariables as $var) {
                    $tempFilePath = null;

                    if (strpos($request->message, $var) === false) {
                        $missingVariables[] = $var;
                    }
                }

                if (!empty($missingVariables)) {
                    return back()->withErrors([
                        'message' => 'Le message doit contenir les variables suivantes : ' . implode(', ', $missingVariables)
                    ])->withInput();
                }
                if (isset($files[$index]['individual_file'])) {
                    $file = $files[$index]['individual_file'];
                    
                    $originalName = $file->getClientOriginalName();
                    $path = $file->store('temp', 'public');
                    $tempFilePath = $path;
                }
                
                $message = str_replace(
                    ['[STUDENT_NAME]', '[COURSE_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]', '[CLASS_AVERAGE]', '[MY_MAIL]'],
                    [$student['name'], $request->course_name, $request->exam_name, $student['mark'], $average, $request->teacher_email],
                    $request->message
                );
                
                Mail::to($student['email'])->send(
                    new StudentMarkMail($request->course_name, $message, $tempFilePath, $originalName ?? null)
                );

                if ($tempFilePath && file_exists(Storage::disk('public')->path($tempFilePath))) {
                    unlink(Storage::disk('public')->path($tempFilePath));
                }
            }
        }

        $request->session()->forget('students');

        return redirect()->route('marks.form')->with('success', 'Emails have been sent');
    }

    public function resetMessage(Request $request)
    {
        $students = $request->session()->get('students', []);

        $request->session()->flash('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : **[STUDENT_MARK]**\n\nEn cas de question merci de contacter: [MY_MAIL]");

        return redirect()->route('marks.form')->withInput();
    }

    public function checkValidity(MarksRequest $request) {
        return response()->json(['valid' => true]);
    }

    public function sendTestEmail(MarksRequest $request) {
        $student = $request->students[0] ?? null;

        if ($student && !empty($student['name']) && !empty($student['email'])) {

            $requiredVariables = ['[STUDENT_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]'];
            $missingVariables = [];
            
            $average = $this->getClassAverage($request->students);

            foreach ($requiredVariables as $var) {
                if (strpos($request->message, $var) === false) {
                    $missingVariables[] = $var;
                }
            }

            if (!empty($missingVariables)) {
                return back()->withErrors([
                    'message' => 'Le message doit contenir les variables suivantes : ' . implode(', ', $missingVariables)
                ])->withInput();
            }

            $tempFilePath = null;
            $files = $request->file('students');

            if (isset($files[0]['individual_file'])) {
                $file = $files[0]['individual_file'];

                $originalName = $file->getClientOriginalName();
                $path = $file->store('temp', 'public');
                $tempFilePath = $path;
            }

            $message = str_replace(
                ['[STUDENT_NAME]', '[COURSE_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]', '[CLASS_AVERAGE]', '[MY_MAIL]'],
                [$student['name'], $request->course_name, $request->exam_name, $student['mark'], $average, $request->teacher_email],
                $request->message
            );

            Mail::to($request->teacher_email)->send(
                new StudentMarkMail($request->course_name, $message, $tempFilePath, $originalName ?? null)
            );

            if ($tempFilePath) {
                $fullPath = Storage::disk('public')->path($tempFilePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        return redirect()->route('marks.form')->with('success', 'The test email has been sent')->withInput();
    }

    public function getClassAverage($students) {
        $marks = array_filter(array_column($students, 'mark'), function($value) {
            return $value !== null && $value !== '';
        });

        if (count($marks) === 0) {
            return 0;
        }

        $total = array_sum($marks);
        $average = $total / count($marks);

        return round($average, 2);
    }
}
