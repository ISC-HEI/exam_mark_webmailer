<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentMarkMail;
use App\Http\Requests\MarksRequest;
use Illuminate\Support\Facades\Storage;

class MarksController extends Controller
{
    protected $CSV_DELIMITER = ';';

    public function showForm(Request $request)
    {
        $students = old('students', $request->session()->get('students', []));

        return view('marks.form', compact('students'));
    }

    public function addStudent(Request $request)
    {
        $updatedStudents = $this->processStudents($request, function ($students) {
            $newStudent = ['name' => '', 'email' => '', 'mark' => ''];
            return array_merge($students, [$newStudent]);
        });

        return redirect()->route('marks.form')->with('students', $updatedStudents);
    }

    public function removeStudent(Request $request)
    {
        $indexToRemove = $request->input('remove_index');

        $updatedStudents = $this->processStudents($request, function ($students) use ($indexToRemove) {
            if (isset($students[$indexToRemove])) {
                unset($students[$indexToRemove]);
            }
            return array_values($students);
        });

        return redirect()->route('marks.form');
    }

    public function loadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $students = [];
        if (($handle = fopen($request->file('csv_file')->getRealPath(), 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, $this->CSV_DELIMITER)) !== false) {
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

    public function checkValidity(MarksRequest $request) {
        return response()->json(['valid' => true]);
    }

    public function sendMarks(MarksRequest $request)
    {
        $this->prepareAndSendEmail($request->students, $request);
        return redirect()->route('marks.form')->with('success', 'Emails have been sent successfully!');
    }

    public function sendTestEmail(MarksRequest $request)
    {
        $studentsData = $request->input('students', []);
        $uploadedFiles = $request->file('students', []);
        
        foreach ($studentsData as $index => &$student) {
            if (isset($uploadedFiles[$index]['individual_file'])) {
                $file = $uploadedFiles[$index]['individual_file'];
                $path = $file->store('temp', 'public');
                
                $student['temp_file_path'] = $path;
                $student['temp_file_name'] = $file->getClientOriginalName();
            }
        }
        unset($student);

        $request->session()->put('students', $studentsData);

        if (isset($studentsData[0])) {
            $this->prepareAndSendEmail([$studentsData[0]], $request, true);
        }
        
        return redirect()->route('marks.form')
            ->with('success', 'The test email has been sent')
            ->withInput($request->except('students'));
    }

    // --------- Helper Functions ---------

    private function getClassAverage($students) {
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

    private function replaceVariables($message, $student, $courseName, $examName, $average, $teacherEmail) {
        return str_replace(
            ['[STUDENT_NAME]', '[COURSE_NAME]', '[EXAM_NAME]', '[STUDENT_MARK]', '[CLASS_AVERAGE]', '[MY_MAIL]'],
            [$student['name'], $courseName, $examName, $student['mark'], $average, $teacherEmail],
            $message
        );
    }

    private function updateGlobalAttachments(Request $request) {
        $globalFiles = session('global_temp_files', []);
    
        if ($request->hasFile('global_attachment')) {
            foreach ($request->file('global_attachment') as $file) {
                $path = $file->store('temp', 'public');
                $globalFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }
        session(['global_temp_files' => $globalFiles]);
    }

    private function processStudents(Request $request, callable $callback)
    {
        $students = $request->input('students', []);
        $files = $request->file('students', []);

        foreach ($students as $index => &$student) {
            if (isset($files[$index]['individual_file'])) {
                $file = $files[$index]['individual_file'];
                $path = $file->store('temp', 'public');

                $student['temp_file_path'] = $path;
                $student['temp_file_name'] = $file->getClientOriginalName();
            }
        }

        $this->updateGlobalAttachments($request);

        $students = $callback($students);

        $request->session()->put('students', $students);
        $request->flashOnly(['course_name', 'exam_name', 'teacher_email', 'message']);

        return $students;
    }

    private function prepareAndSendEmail(array $students, MarksRequest $request, bool $toTeacher = false)
    {
        $globalAttachments = session('global_temp_files', []);

        if ($request->hasFile('global_attachment')) {
            foreach ($request->file('global_attachment') as $file) {
                $path = $file->store('temp', 'public');
                $globalAttachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }

        $files = $request->file('students');
        $average = $this->getClassAverage($request->students);

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

        foreach ($students as $index => $student) {
            if (empty($student['name']) || empty($student['email'])) {
                continue;
            }

            $individualFilePath = null;
            $individualFileName = null;

            if (isset($files[$index]['individual_file'])) {
                $file = $files[$index]['individual_file'];
                $individualFileName = $file->getClientOriginalName();
                $individualFilePath = $file->store('temp', 'public');
            } elseif (isset($student['temp_file_path'])) {
                $individualFileName = $student['temp_file_name'] ?? null;
                $individualFilePath = $student['temp_file_path'];
            }

            $message = $this->replaceVariables(
                $request->message,
                $student,
                $request->course_name,
                $request->exam_name,
                $average,
                $request->teacher_email
            );

            $recipient = $toTeacher ? $request->teacher_email : $student['email'];

            Mail::to($recipient)->send(
                new StudentMarkMail(
                    $request->course_name,
                    $message,
                    $individualFilePath,
                    $individualFileName,
                    $globalAttachments
                )
            );

            if ($toTeacher) {
                break;
            } else {
                if ($individualFilePath && Storage::disk('public')->exists($individualFilePath)) {
                    Storage::disk('public')->delete($individualFilePath);
                }
            }
        }

        
        if (!$toTeacher) {
            foreach ($globalAttachments as $att) {
                Storage::disk('public')->delete($att['path']);
            }

            $request->session()->forget('global_temp_files');
            $request->session()->forget('students');
        }
    }
}
