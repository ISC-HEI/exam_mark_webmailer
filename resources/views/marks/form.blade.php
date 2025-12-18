@extends('layouts.app')

@section('content')
<div class="container-fluid p-4 bg-light min-vh-100">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Marks Mail Sender</h2>
            <p class="text-muted small mb-0">Manage exams and notify students efficiently.</p>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-0 py-2" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="send-marks-form" method="POST" action="{{ route('marks.send') }}">
        @csrf
        <input type="hidden" name="action" id="form-action-input" value="send">
        <input type="hidden" name="remove_index" id="remove-index-input" value="">
    </form>

    <div class="row g-4">
        
        <div class="col-lg-4 col-xl-3">
            <div class="card bg-dark text-white border-0 shadow h-100">
                <div class="card-header bg-transparent border-secondary border-opacity-50 pt-4 pb-0">
                    <h5 class="card-title fw-bold"><i class="bi bi-sliders me-2"></i>Configuration</h5>
                </div>
                
                <div class="card-body d-flex flex-column gap-3">
                    
                    <div>
                        <label class="form-label text-secondary small fw-bold text-uppercase">Informations</label>
                        <div class="mb-2">
                            <input type="text" form="send-marks-form" name="course_name" class="form-control bg-secondary text-white border-0" value="{{ old('course_name') }}" required placeholder="Course name">
                        </div>
                        <div>
                            <input type="text" form="send-marks-form" name="exam_name" class="form-control bg-secondary text-white border-0" value="{{ old('exam_name') }}" required placeholder="Exam name">
                        </div>
                    </div>

                    <hr class="border-secondary opacity-50 my-1">

                    <div class="flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-secondary small fw-bold text-uppercase mb-0">Message Email</label>
                            
                            <form method="POST" action="{{ route('marks.reset_message') }}">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none p-0" title="Réinitialiser">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </form>
                        </div>
                        
                        <textarea form="send-marks-form" name="message" class="form-control bg-secondary text-white border-0 flex-grow-1" style="min-height: 250px; font-family: monospace; font-size: 0.85rem;">{{ old('message', session('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : [STUDENT_MARK]\n\nEn cas de question merci de contacter <your.email@domain.ch>")) }}</textarea>
                        
                        <div class="mt-2 text-white-50 small fst-italic">
                            <i class="bi bi-info-circle me-1"></i> Variables: [STUDENT_NAME], [COURSE_NAME], [EXAM_NAME], [STUDENT_MARK]
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 p-3 pt-0">
                    <button type="submit" form="send-marks-form" class="btn btn-primary w-100 py-3 fw-bold shadow">
                        <i class="bi bi-send-fill me-2"></i> Send marks
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2"></i>Students :</h5>
                    
                    <div class="d-flex gap-2">
                        <button type="button" id="add-student-btn" class="btn btn-light border">
                            <i class="bi bi-plus-lg text-success"></i> Add student
                        </button>
                        <form method="POST" action="{{ route('marks.load_csv') }}" enctype="multipart/form-data" class="d-flex align-items-center bg-light border rounded px-2">
                            @csrf
                            <input type="file" name="csv_file" accept=".csv" class="form-control form-control-sm border-0 bg-transparent">
                            <button type="submit" class="btn btn-sm btn-link text-decoration-none fw-bold text-dark">
                                <i class="bi bi-upload"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase small text-muted">
                                <th class="ps-4 py-3">Name</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Mark</th>
                                <th class="text-end pe-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="ps-4">
                                    <input type="text" form="send-marks-form" name="students[{{ $index }}][name]" class="form-control border-0 bg-transparent fw-bold" value="{{ $student['name'] }}" placeholder="Nom">
                                </td>
                                <td>
                                    <input type="email" form="send-marks-form" name="students[{{ $index }}][email]" class="form-control border-0 bg-transparent text-muted" value="{{ $student['email'] }}" placeholder="Email">
                                </td>
                                <td style="width: 120px;">
                                    <input type="number" id="inputMark" form="send-marks-form" step="0.1" min="1" max="6" name="students[{{ $index }}][mark]" 
                                           class="form-control text-center fw-bold mark-input border-0" 
                                            value="{{ $student['mark'] }}">
                                </td>
                                <td class="text-end pe-4">
                                <button type="button" data-index="{{ $index }}" class="btn-remove-student btn btn-outline-danger btn-sm border-0">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if(count($students) === 0)
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    No students in the list. Add them manually or import a CSV.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section("script")
<script>
    // --------------------
    // Update Marks input
    // --------------------
    const inputMarks = document.querySelectorAll("#inputMark")

    const updateMarkInputColor = (input) => {
        const mark = parseFloat(input.value);
        
        const successClasses = ['text-success', 'bg-success-subtle'];
        const failureClasses = ['text-danger', 'bg-danger-subtle'];
        input.classList.remove(...successClasses, ...failureClasses);
        
        if (!isNaN(mark) && mark >= 4.0) {
            input.classList.add(...successClasses);
        } else if (!isNaN(mark) && mark < 4.0) {
            input.classList.add(...failureClasses);
        }
    }

    inputMarks.forEach(inp => {
        updateMarkInputColor(inp)

        inp.addEventListener("input", () => {updateMarkInputColor(inp)})
    });
    
    // --------------------
    // Add and Remove student
    // --------------------
    const addStudentBtn = document.getElementById('add-student-btn');
    const removeIndexInput = document.getElementById('remove-index-input');
    const removeButtons = document.querySelectorAll('.btn-remove-student');

    const sendMarksForm = document.getElementById('send-marks-form');
    const formActionInput = document.getElementById('form-action-input');
    
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', () => {
            formActionInput.value = 'add_student';
            
            sendMarksForm.action = '{{ route('marks.add_student') }}';
            
            sendMarksForm.submit();
        });
    }


    console.log(removeButtons)
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            
            formActionInput.value = 'remove_student';
            
            removeIndexInput.value = index;

            sendMarksForm.action = '{{ route('marks.remove_student') }}';

            sendMarksForm.submit();
        });
    });

    const sendMarksBtn = document.querySelector('button[form="send-marks-form"]');

    if (sendMarksBtn) {
        sendMarksBtn.addEventListener('click', () => {
            formActionInput.value = 'send';
            sendMarksForm.action = '{{ route('marks.send') }}';
        });
    }
</script>
@endsection