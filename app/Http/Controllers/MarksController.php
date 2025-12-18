<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentMarkMail;
use App\Http\Requests\MarksRequest;

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

        $request->flashOnly(['course_name', 'exam_name', 'message']);

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
        $request->flashOnly(['course_name', 'exam_name', 'message']);

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
        foreach ($request->students as $student) {
            if (!empty($student['name']) && !empty($student['email'])) {

                $requiredVariables = ['[STUDENT_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]'];
                $missingVariables = [];

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

                $message = str_replace(
                    ['[STUDENT_NAME]', '[COURSE_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]'],
                    [$student['name'], $request->course_name, $request->exam_name, $student['mark']],
                    $request->message
                );

                Mail::to($student['email'])->send(
                    new StudentMarkMail($request->course_name, $message)
                );
            }
        }

        $request->session()->forget('students');

        return redirect()->route('marks.form')->with('success', 'Emails have been sent');
    }

    public function resetMessage(Request $request)
    {
        $students = $request->session()->get('students', []);

        $request->session()->flash('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : [STUDENT_MARK]\n\nEn cas de question merci de contacter <your.email@domain.ch>");

        return redirect()->route('marks.form')->withInput();
    }
}
