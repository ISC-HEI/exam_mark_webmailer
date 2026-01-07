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
                            <input type="email" form="send-marks-form" name="teacher_email" class="form-control bg-secondary text-white border-0" value="{{ old('teacher_email') }}" required placeholder="Your email">
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
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart me-2"></i>Exam Analysis</h5>
                        
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
@section("script")
<script>
    // --------------------
    // Constants
    // --------------------
    const SUCCESS_MARK = 4.0;
    const SUCCESS_RATE = 60; // in percent

    const TIMER_BEFORE_SEND = 5; // seconds

    const INCOGNITO_BLUR = 9; // px

    const DEFAULT_MESSAGE = `Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : **[STUDENT_MARK]**\n\nEn cas de question merci de contacter: [MY_MAIL]`;

    // Chart constants
    const CHART = {
        ANIMATION_DURATION: 2000,
        BUBBLE_SIZE: 8,
        COLOR: {
            GREY_DEFAULT: '#f0f0f0',
            WEIGHT_100: '#f5f5f5',
            WEIGHT_200: '#eceff1',
            WEIGHT_300: '#cfd8dc',
            WEIGHT_500: '#90a4ae',
            WEIGHT_700: '#546e7a',
            WEIGHT_900: '#263238'
        }
    }

    // Variables constants
    const VARIABLES = {
        DEFAULT_ACTIVE_INDEX: 0,
        PREFIX: "[",
        SUFFIX: "]"
    }

    // DOM elements
    const DOM = {
        // Forms ans global containers
        form: document.getElementById('send-marks-form'),
        tableBody: document.querySelector('table tbody'),
        container: document.getElementById('mainContainer'),
        loadingOverlay: document.getElementById("loading-overlay"),
        errorContainer: document.querySelector('.alert-danger'),
        studentLines: document.querySelectorAll(".studentLine"),

        // Main inputs
        inputCourse: document.querySelector('input[name="course_name"]'),
        inputExam: document.querySelector('input[name="exam_name"]'),
        textareaMessage: document.querySelector('textarea[name="message"]'),
        inputFormAction: document.getElementById('form-action-input'),
        inputRemoveIndex: document.getElementById('remove-index-input'),
        inputMarks: document.querySelectorAll(".mark-input"),
        fileInput: document.getElementById('fileInput'),

        // Action buttons
        btnSend: document.querySelector('button[form="send-marks-form"]'),
        btnSendTestEmail: document.getElementById('send-test-email'),
        btnAddStudent: document.getElementById('add-student-btn'),
        btnResetMessage: document.getElementById('reset-message-btn'),
        btnIncognito: document.getElementById('btn-incognito'),
        btnThemeToggle: document.getElementById('theme-toggle'),
        btnRemoveStudents: document.querySelectorAll('.btn-remove-student'),

        // Confirmation modal
        modalConfirm: document.getElementById('confirmSendModal'),
        btnFinalConfirm: document.getElementById('final-confirm-send'),
        summaryCourse: document.getElementById('summary-course'),
        summaryExam: document.getElementById('summary-exam'),
        summaryCount: document.getElementById('summary-count'),

        // Stats and research
        searchInput: document.getElementById('student-search'),
        studentCounter: document.getElementById('student-counter'),
        totalStudentsLabel: document.getElementById('totalStudents'),
        btnStatsTab: document.getElementById('tab-stats-btn'),
        btnStudentsTab: document.getElementById('tab-students-btn'),
        spaStatsAverage: document.getElementById('stats-average'),
        spaStatsExtreme: document.getElementById('stats-extreme'),
        spaStatsSuccess: document.getElementById('stats-success'),
        spaStatsMedian: document.getElementById('stats-median'),
        filesCount: document.getElementById('file-count'),

        // Variables menu
        variableMenu: document.getElementById('variable-menu'),
        variableItems: document.querySelectorAll('#variable-menu .list-group-item'),

        // Charts
        ctxBar: document.getElementById('marksChartBar'),
        ctxBubble: document.getElementById('marksChartBubble'),
    }

    // --------------------
    // Update Marks input
    // --------------------
    const updateMarkInputColor = (input) => {
        const mark = parseFloat(input.value);
        
        const successClasses = ['text-success', 'bg-success-subtle'];
        const failureClasses = ['text-danger', 'bg-danger-subtle'];
        input.classList.remove(...successClasses, ...failureClasses);
        
        if (!isNaN(mark) && mark >= SUCCESS_MARK) {
            input.classList.add(...successClasses);
        } else if (!isNaN(mark) && mark < SUCCESS_MARK) {
            input.classList.add(...failureClasses);
        }
    }

    DOM.inputMarks.forEach(inp => {
        updateMarkInputColor(inp)

        inp.addEventListener("input", () => {updateMarkInputColor(inp)})
    });
    
    // --------------------
    // Add and Remove student
    // --------------------
    const addStudentBtn = DOM.btnAddStudent;

    const loadingOverlay = DOM.loadingOverlay;

    const sendMarksForm = DOM.form;
    const formActionInput = DOM.inputFormAction;
    
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', () => {
            formActionInput.value = 'add_student';
            
            sendMarksForm.action = '{{ route('marks.add_student') }}';
            
            sendMarksForm.submit();
        });
    }


    DOM.btnRemoveStudents.forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            
            formActionInput.value = 'remove_student';
            
            DOM.inputRemoveIndex.value = index;

            sendMarksForm.action = '{{ route('marks.remove_student') }}';

            sendMarksForm.submit();
        });
    });

    const sendMarksBtn = DOM.btnSend;

    if (sendMarksBtn) {
        sendMarksBtn.addEventListener('click', () => {
            
            formActionInput.value = 'send';
            sendMarksForm.action = '{{ route('marks.checkValidity') }}';
        });
    }

    // --------------------
    // Shortcuts
    // --------------------
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key === 'Enter') {
            if (DOM.btnSend) DOM.btnSend.click();
        }
        if (e.altKey && (e.key === 'a' || e.key === 'A')) {
            e.preventDefault();
            const addBtn = DOM.btnAddStudent;
            if (addBtn) addBtn.click();
        }
        if (e.altKey && (e.key === 'm' || e.key === 'M')) {
            e.preventDefault();
            DOM.textareaMessage.focus();
        }
        if (e.altKey && (e.key === 'r' || e.key === 'R')) {
            e.preventDefault();
            DOM.btnResetMessage.click();
        }
        if (e.altKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            DOM.btnStudentsTab.classList.contains('active') ? DOM.btnStatsTab.click() : DOM.btnStudentsTab.click(); 
        }
    });

    // --------------------
    // Confirm modal and Backend Check
    // --------------------
    const confirmModal = new bootstrap.Modal(DOM.modalConfirm);
    const finalConfirmBtn = DOM.btnFinalConfirm;

    if (sendMarksBtn) {
        sendMarksBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            if (DOM.errorContainer) DOM.errorContainer.remove();

            if (!sendMarksForm.checkValidity()) {
                sendMarksForm.reportValidity();
                return;
            }

            const originalContent = sendMarksBtn.innerHTML;
            sendMarksBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';
            sendMarksBtn.disabled = true;

            try {
                const formData = new FormData(sendMarksForm);
                const response = await fetch('{{ route('marks.checkValidity') }}', {
                    method: 'POST',
                    headers: {
                        accept: "Application/json"
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    DOM.summaryCourse.innerText = DOM.inputCourse.value;
                    DOM.summaryExam.innerText = DOM.inputExam.value;
                    DOM.summaryCount.innerText = DOM.btnRemoveStudents.length;

                    let timeLeft = TIMER_BEFORE_SEND;
                    const originalText = finalConfirmBtn.innerText;
                    
                    finalConfirmBtn.disabled = true;
                    finalConfirmBtn.innerText = `${originalText} (${timeLeft}s)`;

                    confirmModal.show();

                    const timer = setInterval(() => {
                        timeLeft--;
                        if (timeLeft > 0) {
                            finalConfirmBtn.innerText = `${originalText} (${timeLeft}s)`;
                        } else {
                            clearInterval(timer);
                            finalConfirmBtn.disabled = false;
                            finalConfirmBtn.innerText = originalText;
                            finalConfirmBtn.focus();
                        }
                    }, 1000);

                    DOM.modalConfirm.addEventListener('hidden.bs.modal', () => {
                        clearInterval(timer);
                    }, { once: true });
                } else {
                    displayErrors(result.errors);
                }
            } catch (error) {
                console.error('Erreur lors du check:', error);
                alert('Une erreur est survenue lors de la vérification des données.');
            } finally {
                sendMarksBtn.innerHTML = originalContent;
                sendMarksBtn.disabled = false;
            }
        });
    }

    function displayErrors(errors) {
        let errorHtml = '<div class="alert alert-danger shadow-sm border-0 mb-4"><ul class="mb-0">';
        Object.values(errors).forEach(errArray => {
            errArray.forEach(message => {
                errorHtml += `<li>${message}</li>`;
            });
        });
        errorHtml += '</ul></div>';
        
        DOM.container.insertAdjacentHTML('afterbegin', errorHtml);
        window.scrollTo(0, 0);
    }

    finalConfirmBtn.addEventListener('click', () => {
        confirmModal.hide();
        loadingOverlay.classList.remove("d-none");
        loadingOverlay.classList.add("d-flex");

        sendMarksBtn.classList.add('disabled');
        
        formActionInput.value = 'send';
        sendMarksForm.action = '{{ route('marks.send') }}';
        sendMarksForm.submit();
    });

    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirmModal) {
                confirmModal.hide();
            }
        });
    });

    // ---------------
    // Send test email
    // ---------------
    DOM.btnSendTestEmail.addEventListener("click", () => {
        formActionInput.value = 'send';
        loadingOverlay.classList.remove("d-none");
        loadingOverlay.classList.add("d-flex");

        sendMarksForm.action = '{{ route('marks.send_test') }}';
        sendMarksForm.submit();
    })

    // ---------------
    // Variables choice
    // ---------------
    const textarea = DOM.textareaMessage;
    const menu = DOM.variableMenu;
    const items = DOM.variableItems
    let activeIndex = VARIABLES.DEFAULT_ACTIVE_INDEX;

    const insertVariable = (variable) => {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        
        const before = text.substring(0, start - 1);
        const after = text.substring(end);
        
        textarea.value = before + `${VARIABLES.PREFIX}${variable}${VARIABLES.SUFFIX}` + after;
        
        const newCursorPos = start + variable.length + 1;
        textarea.setSelectionRange(newCursorPos, newCursorPos);
        
        hideMenu();
        textarea.focus();
    };

    const hideMenu = () => {
        menu.classList.add('d-none');
        activeIndex = 0;
    };

    textarea.addEventListener('input', function(e) {
        const value = textarea.value;
        const cursorPos = textarea.selectionStart;
        const lastChar = value.substring(cursorPos - 1, cursorPos);

        if (lastChar === VARIABLES.PREFIX) {
            const coordinates = getCaretCoordinates(textarea, cursorPos);
            
            menu.style.top = (textarea.offsetTop + coordinates.top + 20) + 'px';
            menu.style.left = (textarea.offsetLeft + coordinates.left) + 'px';
            menu.classList.remove('d-none');
        } else {
            hideMenu();
        }
    });

    textarea.addEventListener('keydown', function(e) {
        if (!menu.classList.contains('d-none')) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveItem();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveItem();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                insertVariable(items[activeIndex].getAttribute('data-var'));
                resetActiveButton(items)
            } else if (e.key === 'Escape') {
                hideMenu();
            }
        }
    });

    function updateActiveItem() {
        items.forEach((item, index) => {
            item.classList.toggle('list-group-item-dark', index === activeIndex);
        });
    }

    items.forEach((item) => {
        item.addEventListener('click', () => {
            insertVariable(item.getAttribute('data-var'));
        });
    });

    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && e.target !== textarea) hideMenu();
    });

    function getCaretCoordinates(element, position) {
        const div = document.createElement('div');
        div.id = 'textarea-mirror';
        const style = window.getComputedStyle(element);
        
        const props = ['fontFamily', 'fontSize', 'fontWeight', 'lineHeight', 'padding', 'border', 'width', 'boxSizing'];
        props.forEach(prop => div.style[prop] = style[prop]);
        
        div.textContent = element.value.substring(0, position);
        const span = document.createElement('span');
        span.textContent = element.value.substring(position) || '.';
        div.appendChild(span);
        
        document.body.appendChild(div);
        const { offsetTop: top, offsetLeft: left } = span;
        document.body.removeChild(div);
        
        return { top, left };
    }

    function resetActiveButton(items) {
        items.forEach(item => {
            if (item.classList.contains("list-group-item-dark")) {
                item.classList.remove("list-group-item-dark")
            }
        })
        items[0].classList.add("list-group-item-dark")
    }

    // ---------------
    // File input - global attachment
    // ---------------
    DOM.fileInput.addEventListener('change', function(e) {
        const count = e.target.files.length;
        const display = DOM.fileCount;
        display.innerText = count > 0 ? ( count === 1 ? `${count} file selected` : `${count} files selected`) : "";
    });

    // ---------------
    // Reset message button
    // ---------------
    DOM.btnResetMessage.addEventListener('click', () => {
        textarea.value = DEFAULT_MESSAGE;
    });

    // ---------------
    // Theme toggle
    // ---------------
    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
    };

    const storedTheme = localStorage.getItem('theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    setTheme(storedTheme);

    const themeBtn = DOM.btnThemeToggle;
    const themeIconSun = themeBtn.querySelector('.bi-sun-fill');
    const themeIconMoon = themeBtn.querySelector('.bi-moon-stars-fill');

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            if (newTheme === 'dark') {
                themeIconMoon.classList.remove('d-block-dark');
                themeIconMoon.classList.add('d-none');
                themeIconSun.classList.remove('d-none');
                themeIconSun.classList.add('d-block-light');
            } else {
                themeIconSun.classList.remove('d-block-light');
                themeIconSun.classList.add('d-none');
                themeIconMoon.classList.remove('d-none');
                themeIconMoon.classList.add('d-block-dark');
            }
            setTheme(newTheme);
        });
    }

    // ---------------
    // Search students
    // ---------------
    const searchInput = DOM.searchInput;
    const tableBody = DOM.tableBody;
    const totalStudents = DOM.totalStudentsLabel;
    const originalTotalStudentsText = totalStudents.innerText;

    if (searchInput) {

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            if (searchTerm !== '') {
                totalStudents.innerText = `Filtered Students`;
            } else {
                totalStudents.innerText = originalTotalStudentsText;
            }

            const rows = tableBody.querySelectorAll('tr:not(.no-result)');
            let hasVisibleRow = false;
            
            rows.forEach(row => {
                const name = row.querySelector('input[name*="[name]"]').value.toLowerCase();
                const email = row.querySelector('input[name*="[email]"]').value.toLowerCase();
                const mark = row.querySelector('input[name*="[mark]"]').value.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm) || mark.includes(searchTerm)) {
                    row.style.display = '';
                    hasVisibleRow = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            let noResultRow = tableBody.querySelector('.no-result');
            if (!hasVisibleRow) {
                if (!noResultRow) {
                    noResultRow = document.createElement('tr');
                    noResultRow.className = 'no-result';
                    noResultRow.innerHTML = `<td colspan="5" class="text-center py-4 text-muted fst-italic">Not students find for: "${searchTerm}"</td>`;
                    tableBody.appendChild(noResultRow);
                }
            } else if (noResultRow) {
                noResultRow.remove();
            }
            updateStudentCounter();
        });
    }
        
    // --------------------
    // Total students counter
    // --------------------
    const studentCounter = DOM.studentCounter;

    const updateStudentCounter = () => {
        const allStudents = tableBody.querySelectorAll('tr:not([style*="display: none"]):not(.no-result):not(.empty)');

        studentCounter.innerText = allStudents.length;

        if (allStudents.length === 0) {
            studentCounter.classList.replace('bg-primary', 'bg-secondary');
        } else {
            studentCounter.classList.replace('bg-secondary', 'bg-primary');
        }
    };
    updateStudentCounter();

    // --------------------
    // Stats Calculation
    // --------------------
    const updateStatistics = () => {
        let marks = [];
        
        DOM.inputMarks.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) marks.push(val);
        });

        if (marks.length === 0) return;

        const avg = marks.reduce((a, b) => a + b, 0) / marks.length;
        const best = Math.max(...marks);
        const worst = Math.min(...marks);
        const successCount = marks.filter(m => m >= SUCCESS_MARK).length;
        const successRate = (successCount / marks.length) * 100;
        const median = (() => {
            const sorted = [...marks].sort((a, b) => a - b);
            const mid = Math.floor(sorted.length / 2);
            return sorted.length % 2 !== 0 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2;
        })();

        DOM.spaStatsAverage.innerText = avg.toFixed(2);
        DOM.spaStatsExtreme.innerText = worst.toFixed(1) + " - " + best.toFixed(1);
        DOM.spaStatsSuccess.innerText = successRate.toFixed(0) + '%';
        DOM.spaStatsMedian.innerText = median.toFixed(2);

        if (successRate >= SUCCESS_RATE) {
            DOM.spaStatsSuccess.classList.remove('text-danger');
            DOM.spaStatsSuccess.classList.add('text-success');
        } else {
            DOM.spaStatsSuccess.classList.remove('text-success');
            DOM.spaStatsSuccess.classList.add('text-danger');
        }
    };

    DOM.btnStatsTab.addEventListener('click', () => {
        updateStatistics();
    });

    // --------------------
    // Marks Chart
    // --------------------
    const labelsBar = ["1.0-1.9", "2.0-2.9", "3.0-3.9", "4.0-4.9", "5.0-5.9", "6.0"];
    function colorize() {
        return (content) => {
                    if (!content.parsed) return CHART.COLOR.GREY_DEFAULT;
                    const value = content.parsed.y; 

                    if (value < 2.0) return CHART.COLOR.WEIGHT_100;
                    if (value < 3.0) return CHART.COLOR.WEIGHT_200;
                    if (value < 4.0) return CHART.COLOR.WEIGHT_300; 
                    if (value < 5.0) return CHART.COLOR.WEIGHT_500;  
                    if (value < 6.0) return CHART.COLOR.WEIGHT_700;
                    return CHART.COLOR.WEIGHT_900;
                }
    }

    let chartBar = null;
    let chartBubble = null;

    DOM.btnStatsTab.addEventListener('click', () => {
        if (chartBar) {
            chartBar.destroy();
        }
        if (chartBubble) {
            chartBubble.destroy();
        }

        // Bar chart
        const numbersOfStudentsEachRange = [];
        labelsBar.forEach((label) => {
            const rangeStudent = Array.from(DOM.inputMarks).filter(input => {
                const mark = parseFloat(input.value);
                if (label === "6.0") {
                    return mark === 6.0;
                } else {
                    const [min, max] = label.split('-').map(parseFloat);
                    return mark >= min && mark <= max;
                }
            }).length;
            numbersOfStudentsEachRange.push(rangeStudent);
        })
        const dataBar = {
            labels: labelsBar,
            datasets: [{
                label: 'Number of Students',
                data: numbersOfStudentsEachRange,
                backgroundColor: colorize(),
                borderColor: 'transparent',
            }]
        }
        const chartConfigBar = {
            type: 'bar',
            data: dataBar,
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Marks Distribution bar' },
                },
                animation: {
                    x: {
                        from: 0,
                        duration: CHART.ANIMATION_DURATION
                    },
                },
            }
        };

        // Bubble chart
        const dataBubble = {
            datasets: [{
                label: 'Students Marks',
                data: Array.from(DOM.inputMarks).map((input, index) => {
                    const mark = parseFloat(input.value);
                    return {
                        x: index + 1,
                        y: mark,
                        r: CHART.BUBBLE_SIZE,
                    };
                }),
                backgroundColor: CHART.COLOR.WEIGHT_500,
                borderColor: CHART.COLOR.WEIGHT_700,
            }]
        }
        const chartConfigBubble = {
            type: 'bubble',
            data: dataBubble,
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Marks Distribution bubble' },
                    tooltip: {
                        enabled: false
                    }
                },
                animation: {
                    x: {
                        from: 0,
                        duration: CHART.ANIMATION_DURATION
                    },
                },
            }
        };

        chartBubble = new Chart(DOM.ctxBubble, chartConfigBubble);
        chartBar = new Chart(DOM.ctxBar, chartConfigBar);
    });
    // --------------------
    // Incognito mode
    // --------------------
    DOM.btnIncognito.addEventListener("click", toogleIncognitoMode);

    function toogleIncognitoMode() {
        DOM.studentLines.forEach(line => {
            const nameInput = line.querySelector("input[name*='[name']");
            const emailInput = line.querySelector("input[name*='[email']");
            const attachmentInput = line.querySelector("input[type='file']");

            nameInput.style.filter === "" ? nameInput.style.filter = `blur(${INCOGNITO_BLUR}px)` : nameInput.style.filter = "";
            emailInput.style.filter === "" ? emailInput.style.filter = `blur(${INCOGNITO_BLUR}px)` : emailInput.style.filter = "";
            attachmentInput.style.filter === "" ? attachmentInput.style.filter = `blur(${INCOGNITO_BLUR}px)` : attachmentInput.style.filter = "";
        })
        DOM.btnIncognito.classList.toggle("btn-secondary")
    }
</script>
@endsection