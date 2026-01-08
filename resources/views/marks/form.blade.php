@extends('layouts.app')

@section('content')
<!-- Email sender check -->
<div class="modal fade" id="confirmSendModal" tabindex="-1" aria-labelledby="confirmSendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmSendModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Send confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">You are going to send marks for :</p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Course :</span> <strong id="summary-course">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Exam :</span> <strong id="summary-exam">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Students number :</span> <span class="badge bg-primary rounded-pill" id="summary-count">0</span>
                    </li>
                </ul>
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-info-circle me-1"></i> This action will send an email to each students.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="final-confirm-send" class="btn btn-primary px-4">Confirm the sending</button>
            </div>
        </div>
    </div>
</div>

<!-- Spinner -->
<div id="loading-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center" style="background: rgba(255,255,255,0.7); z-index: 9999;">
    <div class="text-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <p class="mt-2 fw-bold text-primary">Sending emails in progress...</p>
    </div>
</div>

<div id="mainContainer" class="container-fluid p-4 min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Marks Mail Sender</h2>
            <p class="text-muted small mb-0">Manage exams and notify students efficiently.</p>
        </div>
        <button id="theme-toggle" style="width: 30px; height: 30px" class="btn btn-outline-secondary d-flex justify-content-center align-items-center border-0 rounded-circle p-2">
            <i class="bi bi-sun-fill d-block-light d-none"></i>
            <i class="bi bi-moon-stars-fill d-block-dark"></i>
        </button>
        @if(session('success'))
            <div class="alert alert-success fade show shadow-sm border-0 mb-0 py-2" role="alert">
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

    <form id="send-marks-form" method="POST" enctype="multipart/form-data" action="{{ route('marks.send') }}">
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
                        <div class="mb-2">
                            <input type="text" form="send-marks-form" name="exam_name" class="form-control bg-secondary text-white border-0" value="{{ old('exam_name') }}" required placeholder="Exam name">
                        </div>
                        <div>
                            <input type="email" form="send-marks-form" name="teacher_email" id="teacher_email" class="form-control bg-secondary text-white border-0" value="{{ old('teacher_email') }}" required placeholder="Your email">
                        </div>
                    </div>

                    <hr class="border-secondary opacity-50 my-1">

                    <div class="flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-secondary small fw-bold text-uppercase mb-0">Message Email</label>
                            <button class="btn btn-link btn-sm text-secondary text-decoration-none p-0" title="Reset to default message" type="button" id="reset-message-btn">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                        <div id="variable-menu" class="list-group shadow-lg position-absolute d-none" style="z-index: 1000; min-width: 200px;">
                            <button type="button" class="list-group-item list-group-item-action list-group-item-dark" data-var="STUDENT_NAME">Student's name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="STUDENT_MARK">Mark</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="COURSE_NAME">Course name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="EXAM_NAME">Exam name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="CLASS_AVERAGE">Class average</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="MEDIAN">Median</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="SUCCESS_RATE">Success rate</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="MY_EMAIL">My email</button>
                        </div>
                        <textarea form="send-marks-form" name="message" class="form-control bg-secondary text-white border-0 flex-grow-1" style="min-height: 160px; font-family: monospace; font-size: 0.85rem;">{{ old('message', session('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : **[STUDENT_MARK]**\n\nEn cas de question merci de contacter: [MY_MAIL]")) }}</textarea>
                        
                        <div class="mt-2 text-white-50 small fst-italic">
                            <i class="bi bi-lightbulb"></i> Type <span class="fw-bold m-1">[</span> for variables
                        </div>
                    </div>
                    <div class="custom-file-upload w-100">
                        <input type="file" id="fileInput" form="send-marks-form" name="global_attachment[]" multiple />
                        <label for="fileInput" class="d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="bi bi-cloud-arrow-up mb-2"></i>
                            <span class="main-text">Click here</span>
                            <span class="sub-text">Add more global attachments</span>
                             <div id="file-count" class="mt-2 text-light small"></div>
                        </label>

                        @if(session('global_temp_files'))
                            <div class="mt-2 px-3">
                                <p class="small text-white-50 mb-1">Files already uploaded :</p>
                                @foreach(session('global_temp_files') as $file)
                                    <div class="badge bg-success mb-1 d-flex text-start align-items-center text-truncate">
                                        <i class="bi bi-file-earmark-check me-1"></i> {{ $file['name'] }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-3 pt-0">
                    <button type="button" id="send-test-email" class="btn btn-outline-light w-100 mb-2 py-2 fw-bold shadow-sm">
                        <i class="bi bi-envelope-check me-2"></i> Send a test email
                    </button>
                    <button type="submit" form="send-marks-form" class="btn btn-primary w-100 py-3 fw-bold shadow">
                        <i class="bi bi-send-fill me-2"></i> Send marks
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <ul class="nav nav-pills bg-white-prefer p-2 rounded-top border shadow-sm mb-0" style="width: fit-content" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-students-btn" data-bs-toggle="pill" data-bs-target="#view-students" type="button" role="tab">
                        <i class="bi bi-people me-2"></i>Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-stats-btn" data-bs-toggle="pill" data-bs-target="#view-stats" type="button" role="tab">
                        <i class="bi bi-graph-up me-2"></i>Statistics
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="view-students" role="tabpanel">
                    <div class="card border-0 shadow-sm h-100" style="border-top-left-radius: 0 !important">
                        <div class="card-header bg-white-prefer border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            
                            <div class="d-flex align-items-center bg-white-prefer px-3 py-2 rounded-pill shadow-sm border">
                                <h6 id="totalStudents" class="mb-0 text-muted small fw-bold text-uppercase me-2">
                                    Total Students
                                </h6>
                                <span id="student-counter" class="badge rounded-pill fs-6 fw-bold bg-primary">
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" id="add-student-btn" class="btn border">
                                    <i class="bi bi-plus-lg text-success"></i> Add student
                                </button>
                                <form method="POST" action="{{ route('marks.load_csv') }}" enctype="multipart/form-data" class="d-flex align-items-center border rounded px-2">
                                    @csrf
                                    <input type="file" name="csv_file" accept=".csv" class="form-control form-control-sm border-0">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none fw-bold">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                </form>
                                <button id="btn-incognito" class="btn border">
                                    <i class="bi bi-incognito"></i>
                                </button>
                            </div>
                        </div>
                        @if (count($students) >= 5)
                        <div id="search-container" class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white-prefer border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="student-search" class="form-control border-start-0 ps-0" placeholder="Search by name, email or marks...">
                            </div>
                        </div>
                        @endif
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small text-muted">
                                        <th class="ps-4 py-3">Name</th>
                                        <th class="py-3">Email</th>
                                        <th class="text-center py-3">Mark</th>
                                        <th class="text-center py-3">Attachement</th>
                                        <th class="text-center pe-4 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                    <tr class="studentLine">
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
                                        <td>
                                            @if(isset($student['temp_file_path']))
                                                <div class="small text-success mb-1">
                                                    <i class="bi bi-file-check"></i> {{ $student['temp_file_name'] }}
                                                </div>
                                                <input type="hidden" form="send-marks-form" name="students[{{ $index }}][temp_file_path]" value="{{ $student['temp_file_path'] }}">
                                                <input type="hidden" form="send-marks-form" name="students[{{ $index }}][temp_file_name]" value="{{ $student['temp_file_name'] }}">
                                            @endif
                                            <input type="file" form="send-marks-form" name="students[{{ $index }}][individual_file]" class="form-control form-control-sm">
                                        </td>
                                        <td class="text-center pe-4">
                                        <button type="button" data-index="{{ $index }}" class="btn-remove-student btn btn-outline-danger btn-sm border-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @if(count($students) === 0)
                                    <tr class="empty">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No students in the list. Add them manually or import a CSV.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="view-stats" role="tabpanel">
                    <div id="pdf-header" class="d-none">
                        <div style="border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px;">
                            <h2 style="color: #0d6efd; margin: 0;"><span id="spa-exam-name"></span> - Analysis</h2>
                            <small><span id="spa-course-name"></span> - <span id="spa-teacher-email"></span></small>
                            <p style="margin: 0; color: #666;">Document generate the : <span id="pdf-date"></span></p>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart me-2"></i>Exam Analysis</h5>
                            <button id="btn-save-pdf" class="btn btn-primary fw-bold shadow">
                                <i class="bi bi-save"></i>
                            </button>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light-prefer">
                                    <small class="text-muted d-block">Class Average</small>
                                    <span class="h4 fw-bold text-primary" id="stats-average">0.0</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light-prefer">
                                    <small class="text-muted d-block">Median</small>
                                    <span class="h4 fw-bold text-primary" id="stats-median">0.0</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light-prefer">
                                    <small class="text-muted d-block">Success Rate</small>
                                    <span class="h4 fw-bold" id="stats-success">0%</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded bg-light-prefer">
                                    <small class="text-muted d-block">Extreme marks</small>
                                    <span class="h4 fw-bold text-secondary" id="stats-extreme">0.0 - 0.0</span>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container d-flex flex-column align-items-center w-100">
                            <div class="chart-wrapper">
                                <canvas id="marksChartBar"></canvas>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="marksChartBubble"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection