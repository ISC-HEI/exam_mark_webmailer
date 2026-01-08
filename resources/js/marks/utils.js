import { DOM } from './dom.js';
import { INCOGNITO_BLUR, SUCCESS_MARK, SUCCESS_RATE } from './constants.js'

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

themeBtn.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
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

// ---------------
// Load of the page
// ---------------
window.addEventListener('load', () => {
    const scrollToBottom = sessionStorage.getItem('scrollToBottom');
    if (scrollToBottom === '1') {
        if (DOM.studentLines.length > 10) {
            window.scrollTo(0, document.body.scrollHeight);
        }
        sessionStorage.removeItem('scrollToBottom');
    }
});