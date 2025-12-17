@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-center">Marks Mail Sender</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('marks.send') }}">
        @csrf

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label class="form-label">Course Name</label>
                <input type="text" name="course_name" class="form-control" value="{{ old('course_name') }}" required placeholder="Enter course name">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Exam Name</label>
                <input type="text" name="exam_name" class="form-control" value="{{ old('exam_name') }}" required placeholder="Enter exam name">
            </div>
        </div>

        <h4 class="mt-3">Students</h4>
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mark</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td>
                        <input type="text" name="students[{{ $index }}][name]" class="form-control" value="{{ $student['name'] }}" placeholder="Student name">
                    </td>
                    <td>
                        <input type="email" name="students[{{ $index }}][email]" class="form-control" value="{{ $student['email'] }}" placeholder="Student email">
                    </td>
                    <td>
                        <input type="number" step="0.1" min="1" max="6" name="students[{{ $index }}][mark]" class="form-control" value="{{ $student['mark'] }}">
                    </td>
                    <td>
                        <form method="POST" action="{{ route('marks.remove_student', $index) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 {{ $index == 0 ? 'disabled' : '' }}">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-4">
            <form method="POST" action="{{ route('marks.add_student') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">+ Add Student</button>
            </form>
        </div>

        <div class="mb-4">
            <form method="POST" action="{{ route('marks.load_csv') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label">
                    Load students from CSV
                    <span class="d-inline-flex justify-content-center align-items-center 
                            border border-dark rounded-circle 
                            text-center fw-bold"
                    style="width:20px; height:20px; cursor:pointer;"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="right" 
                    title="CSV format must be : student_name;email;mark">
                    ?
                </span>
                </label>
                <div class="input-group">
                    <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                    <button type="submit" class="btn btn-secondary">Load CSV</button>
                </div>
            </form>
        </div>

        <div class="mb-4">
            <label class="form-label">
                Message 
                <span class="d-inline-flex justify-content-center align-items-center 
                            border border-dark rounded-circle 
                            text-center fw-bold"
                    style="width:20px; height:20px; cursor:pointer;"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="right" 
                    title="Use variables: [STUDENT_NAME], [COURSE_NAME], [EXAM_NAME], [STUDENT_MARK]">
                    ?
                </span>
            </label>
            <textarea name="message" class="form-control" rows="5">{{ old('message', session('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : [STUDENT_MARK]\n\nEn cas de question merci de contacter <your.email@domain.ch>")) }}</textarea>
            <form method="POST" action="{{ route('marks.reset_message') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm mt-2">Reset Message</button>
            </form>
        </div>

        <div>
            <button type="submit" class="w-100 btn btn-primary btn-lg btn-block">Send marks</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection
