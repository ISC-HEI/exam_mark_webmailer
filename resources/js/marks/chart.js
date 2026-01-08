import { CHART, PDF } from './constants.js';
import { DOM } from './dom.js';

// --------------------
// Statistics load
// --------------------
DOM.btnStatsTab.addEventListener('click', () => {
    if (DOM.btnSavePDF) {
        DOM.btnSavePDF.disabled = true;
        DOM.btnSavePDF.classList.add('is-loading');

        setTimeout(() => {
            DOM.btnSavePDF.disabled = false;
            DOM.btnSavePDF.classList.remove('is-loading');
        }, PDF.BUTTON_DISABLE_DURATION);
    }
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
// Statistics PDF Export
// --------------------
if (DOM.btnSavePDF) {
    DOM.btnSavePDF.addEventListener('click', () => {
        exportStatsToPDF();
    });
}

async function exportStatsToPDF() {
    const element = DOM.tabStats;
    const dateStr = new Date().toLocaleDateString('en-EN', { 
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' 
    });

    const canvasBar = DOM.ctxBar;
    const canvasBubble = DOM.ctxBubble;
    if (!canvasBar || !canvasBubble) return;

    const imgBarSrc = canvasBar.toDataURL("image/png");
    const imgBubbleSrc = canvasBubble.toDataURL("image/png");

    const clone = element.cloneNode(true);
    
    clone.style.setProperty('margin', '0', 'important');
    clone.style.setProperty('padding', '10mm', 'important');
    clone.style.width = "190mm";
    clone.style.background = "white";

    const pdfHeader = clone.querySelector('#pdf-header');
    if (pdfHeader) pdfHeader.classList.remove('d-none');
    const pdfDate = clone.querySelector('#pdf-date');
    if (pdfDate) pdfDate.innerText = dateStr;
    
    const setInnerText = (id, val) => {
        const el = clone.querySelector(id);
        if (el) el.innerText = val || "N/A";
    };
    setInnerText('#spa-exam-name', DOM.inputExam?.value);
    setInnerText('#spa-course-name', DOM.inputCourse?.value);
    setInnerText('#spa-teacher-email', document.getElementById('teacher_email')?.value);

    const btn = clone.querySelector('#btn-save-pdf');
    if (btn) btn.remove();

    const cloneCanvases = clone.querySelectorAll('canvas');
    const imagesSrc = [imgBarSrc, imgBubbleSrc];
    
    const imagePromises = Array.from(cloneCanvases).map((canvas, index) => {
        return new Promise((resolve) => {
            if (imagesSrc[index]) {
                const wrapper = document.createElement('div');
                wrapper.style.marginBottom = "20px";

                const img = document.createElement('img');
                img.onload = () => resolve();
                img.onerror = () => resolve(); 
                img.src = imagesSrc[index];
                img.style.width = "100%";
                img.style.display = "block";
                
                wrapper.appendChild(img);
                canvas.replaceWith(wrapper);
            } else {
                resolve();
            }
        });
    });

    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.top = '0';
    tempContainer.style.left = '-9999px';
    tempContainer.style.width = '210mm';
    tempContainer.appendChild(clone);
    document.body.appendChild(tempContainer);

    await Promise.all(imagePromises);
    await new Promise(r => setTimeout(r, 150));

    const options = {
        margin: 0,
        filename: `Stats_${new Date().getTime()}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            scrollY: 0,
            scrollX: 0,
            windowHeight: element.scrollHeight,
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['css', 'legacy'] } 
    };

    try {
        await html2pdf().set(options).from(clone).save();
    } catch (err) {
        console.error("PDF error:", err);
    } finally {
        if (tempContainer.parentNode) {
            document.body.removeChild(tempContainer);
        }
    }
}