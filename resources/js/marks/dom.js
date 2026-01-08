// --------------------
// DOM elements
// --------------------

export const DOM = {
    // Forms ans global containers
    form: document.getElementById('send-marks-form'),
    tableBody: document.querySelector('table tbody'),
    container: document.getElementById('mainContainer'),
    loadingOverlay: document.getElementById("loading-overlay"),
    errorContainer: document.querySelector('.alert-danger'),
    studentLines: document.querySelectorAll(".studentLine"),
    overlay: document.getElementById('overlay'),

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
    btnSavePDF: document.getElementById('btn-save-pdf'),
    btnDeleteGlobalAttachments: document.querySelectorAll('.btn-delete-global-attachment'),
    btnFullScreen: document.getElementById('btn-full-screen'),

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

    // tabs
    tabStats: document.getElementById('view-stats'),
    tabStudents: document.getElementById('view-students'),
}
