import { DOM } from './dom.js';
import { TIMER_BEFORE_SEND, DEFAULT_MESSAGE } from './constants.js'

// --------------------
// Confirm modal and Backend Check
// --------------------
const confirmModal = new bootstrap.Modal(DOM.modalConfirm);
const finalConfirmBtn = DOM.btnFinalConfirm;
const formActionInput = DOM.inputFormAction;
const sendMarksForm = DOM.form;
const loadingOverlay = DOM.loadingOverlay;
const sendMarksBtn = DOM.btnSend;

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
        const response = await fetch('/marks/check-validity', {
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
        console.error('An error occurred:', error);
        alert('An error occurred while checking the data.');
    } finally {
        sendMarksBtn.innerHTML = originalContent;
        sendMarksBtn.disabled = false;
    }
});

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
    sendMarksForm.action = '/marks/send';
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

    sendMarksForm.action = '/marks/send-test';
    sendMarksForm.submit();
});


// ---------------
// File input - global attachment
// ---------------
DOM.fileInput.addEventListener('change', function(e) {
    const count = e.target.files.length;
    const display = DOM.filesCount;
    display.innerText = count > 0 ? ( count === 1 ? `${count} file selected` : `${count} files selected`) : "";
});

// ---------------
// Reset message button
// ---------------
DOM.btnResetMessage.addEventListener('click', () => {
    DOM.textareaMessage.value = DEFAULT_MESSAGE;
});
