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
            <div class="modal-footer bg-light">
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

<div class="container-fluid p-4 bg-light min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Marks Mail Sender</h2>
            <p class="text-muted small mb-0">Manage exams and notify students efficiently.</p>
        </div>
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
                            
                            <form method="POST" action="{{ route('marks.reset_message') }}">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none p-0" title="Réinitialiser">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </form>
                        </div>
                        <div id="variable-menu" class="list-group shadow-lg position-absolute d-none" style="z-index: 1000; min-width: 200px;">
                            <button type="button" class="list-group-item list-group-item-action list-group-item-dark" data-var="[STUDENT_NAME]">Student's name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="[STUDENT_MARK]">Mark</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="[COURSE_NAME]">Course name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="[EXAM_NAME]">Exam name</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="[CLASS_AVERAGE]">Class average</button>
                            <button type="button" class="list-group-item list-group-item-action" data-var="[MY_EMAIL]">My email</button>
                        </div>
                        <textarea form="send-marks-form" name="message" class="form-control bg-secondary text-white border-0 flex-grow-1" style="min-height: 250px; font-family: monospace; font-size: 0.85rem;">{{ old('message', session('message', "Cher [STUDENT_NAME],\n\nVoici votre note pour l'examen [EXAM_NAME] : **[STUDENT_MARK]**\n\nEn cas de question merci de contacter: [MY_MAIL]")) }}</textarea>
                        
                        <div class="mt-2 text-white-50 small fst-italic">
                            <i class="bi bi-lightbulb"></i> Type <span class="fw-bold m-1">[</span> for variables
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 p-3 pt-0">
                    <button type="button" id="send-test-email" class="btn btn-outline-light w-100 mb-2 py-2 fw-bold shadow-sm">
                        <i class="bi bi-envelope-check me-2"></i> Send a test email
                    </button>
                    <button type="submit" form="send-marks-form" class="btn btn-primary w-100 py-3 fw-bold shadow">
                        <i class="bi bi-send-fill me-2"></i> Send marks
                        <div class="ms-2 d-none d-md-block" style="opacity: 0.8">
                            <kbd class="bg-light text-dark border-0 px-2 py-1 small shadow-sm">Ctrl</kbd>
                            <span class="small">+</span>
                            <kbd class="bg-light text-dark border-0 px-2 py-1 small shadow-sm">Enter</kbd>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2"></i>Students :</h5>
                    
                    <div class="d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm border">
                        <h6 class="mb-0 text-muted small fw-bold text-uppercase me-2">
                            Total Students
                        </h6>
                        <span id="student-counter" class="badge rounded-pill fs-6 fw-bold {{ count($students) == 0 ? 'bg-secondary' : 'bg-primary' }}">
                            {{ count($students) }}
                        </span>
                    </div>

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
                                <th class="text-center py-3">Mark</th>
                                <th class="text-center pe-4 py-3">Action</th>
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
                                <td class="text-center pe-4">
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

    const loadingOverlay = document.getElementById("loading-overlay")

    const sendMarksForm = document.getElementById('send-marks-form');
    const formActionInput = document.getElementById('form-action-input');
    
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', () => {
            formActionInput.value = 'add_student';
            
            sendMarksForm.action = '{{ route('marks.add_student') }}';
            
            sendMarksForm.submit();
        });
    }


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
            sendMarksForm.action = '{{ route('marks.checkValidity') }}';
        });
    }

    // --------------------
    // Send email shortcut
    // --------------------
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const sendBtn = document.querySelector('button[form="send-marks-form"]');
            if (sendBtn) sendBtn.click();
        }
    });

    // --------------------
    // Confirm modal and Backend Check
    // --------------------
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmSendModal'));
    const finalConfirmBtn = document.getElementById('final-confirm-send');

    if (sendMarksBtn) {
        sendMarksBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const errorContainer = document.querySelector('.alert-danger');
            if (errorContainer) errorContainer.remove();

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
                    document.getElementById('summary-course').innerText = document.querySelector('input[name="course_name"]').value;
                    document.getElementById('summary-exam').innerText = document.querySelector('input[name="exam_name"]').value;
                    document.getElementById('summary-count').innerText = document.querySelectorAll('.btn-remove-student').length;

                    let timeLeft = 5;
                    const originalText = "Confirm the sending";
                    
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

                    document.getElementById('confirmSendModal').addEventListener('hidden.bs.modal', () => {
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
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', errorHtml);
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
    const sendTestEmail = document.getElementById("send-test-email")

    sendTestEmail.addEventListener("click", () => {
        formActionInput.value = 'send';
        loadingOverlay.classList.remove("d-none");
        loadingOverlay.classList.add("d-flex");

        sendMarksForm.action = '{{ route('marks.send_test') }}';
        sendMarksForm.submit();
    })

    // ---------------
    // Variables choice
    // ---------------
    const textarea = document.querySelector('textarea[name="message"]');
    const menu = document.getElementById('variable-menu');
    const items = menu.querySelectorAll('.list-group-item');
    let activeIndex = 0;

    const insertVariable = (variable) => {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        
        const before = text.substring(0, start - 1);
        const after = text.substring(end);
        
        textarea.value = before + variable + after;
        
        const newCursorPos = start - 1 + variable.length;
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

        if (lastChar === '[') {
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
</script>
@endsection