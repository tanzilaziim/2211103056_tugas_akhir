import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const parseJsonScript = (id, fallback) => {
    const node = document.getElementById(id);
    if (!node) return fallback;
    try {
        return JSON.parse(node.textContent || 'null') ?? fallback;
    } catch {
        return fallback;
    }
};

const createChart = ({ canvas, labels, series, color }) => {
    if (!(canvas instanceof HTMLCanvasElement)) return null;

    return new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    data: series,
                    borderColor: color,
                    backgroundColor: color,
                    borderWidth: 2,
                    pointRadius: 2.5,
                    pointHoverRadius: 4,
                    tension: 0.25,
                    spanGaps: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => items?.[0]?.label ?? '-',
                        label: (item) => `${item.parsed.y} ug/m3`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: false },
                    grid: { color: '#e2e8f0', drawOnChartArea: false },
                    border: { color: '#cbd5e1' },
                },
                y: {
                    ticks: { color: '#94a3b8' },
                    grid: { color: '#e2e8f0' },
                    border: { display: false },
                },
            },
        },
    });
};

const initPublicHome = () => {
    const root = document.querySelector('[data-page="public-home"]');
    if (!root) return;

    const labels = parseJsonScript('home-prediction-labels', []);
    const pm10Series = parseJsonScript('home-pm10-series', []);
    const pm25Series = parseJsonScript('home-pm25-series', []);
    const pm10Color = parseJsonScript('home-pm10-color', '#2563EB');
    const pm25Color = parseJsonScript('home-pm25-color', '#16A34A');

    createChart({
        canvas: document.getElementById('public-home-pm10-chart'),
        labels,
        series: pm10Series,
        color: pm10Color,
    });

    createChart({
        canvas: document.getElementById('public-home-pm25-chart'),
        labels,
        series: pm25Series,
        color: pm25Color,
    });
};

document.addEventListener('DOMContentLoaded', initPublicHome);

