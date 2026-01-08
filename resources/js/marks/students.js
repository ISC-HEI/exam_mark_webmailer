import { DOM } from './dom.js';

// --------------------
// Add and Remove student
// --------------------
const sendMarksForm = DOM.form;
const formActionInput = DOM.inputFormAction;

DOM.btnAddStudent.addEventListener('click', () => {
    formActionInput.value = 'add_student';
    
    sendMarksForm.action = '/marks/add-student';
    
    sessionStorage.setItem('scrollToBottom', '1');

    sendMarksForm.submit();
});


DOM.btnRemoveStudents.forEach(btn => {
    btn.addEventListener('click', function() {
        const index = this.getAttribute('data-index');
        
        formActionInput.value = 'remove_student';
        
        DOM.inputRemoveIndex.value = index;

        sendMarksForm.action = '/marks/remove-student';

        sendMarksForm.submit();
    });
});

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
