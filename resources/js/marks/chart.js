import { CHART } from './constants.js';
import { DOM } from './dom.js';

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
