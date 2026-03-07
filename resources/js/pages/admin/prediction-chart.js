import { Chart, registerables } from 'chart.js';
import { PM10_SERIES, PM25_SERIES, formatIdDate, stats } from './prediction-shared';

Chart.register(...registerables);

const addDays = (isoDate, days) => {
    const date = new Date(`${isoDate}T00:00:00`);
    date.setDate(date.getDate() + days);
    return date.toISOString().slice(0, 10);
};

const formatShortDate = (isoDate) => {
    try {
        const date = new Date(`${isoDate}T00:00:00`);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return isoDate;
    }
};

const buildDateOptions = (anchor, count = 7) =>
    Array.from({ length: count }, (_, idx) => addDays(anchor, -idx));

const initAdminPredictionChart = () => {
    const root = document.querySelector('[data-page="admin-prediction-chart"]');
    if (!root) return;

    const state = {
        chartFilter: 'Semua',
        chartDate: '2025-02-13',
        showDateMenu: false,
        showIndicatorMenu: false,
    };

    const chartDateToggle = root.querySelector('[data-chart-date-toggle]');
    const chartDateMenu = root.querySelector('[data-chart-date-menu]');
    const chartDateLabel = root.querySelector('[data-chart-date-label]');
    const chartDateCurrent = root.querySelector('[data-chart-date-current]');
    const chartDatePrev = root.querySelector('[data-chart-date-prev]');
    const chartDateNext = root.querySelector('[data-chart-date-next]');
    const chartDateList = root.querySelector('[data-chart-date-list]');

    const chartIndicatorToggle = root.querySelector('[data-chart-indicator-toggle]');
    const chartIndicatorLabel = root.querySelector('[data-chart-indicator-label]');
    const chartIndicatorMenu = root.querySelector('[data-chart-indicator-menu]');
    const chartIndicatorOptions = root.querySelectorAll('[data-chart-indicator-option]');

    const chartPm10Card = root.querySelector('[data-chart-card-pm10]');
    const chartPm25Card = root.querySelector('[data-chart-card-pm25]');
    const chartPm10Wrap = root.querySelector('[data-chart-wrap-pm10]');
    const chartPm25Wrap = root.querySelector('[data-chart-wrap-pm25]');

    const chartPm10Avg = root.querySelector('[data-chart-pm10-avg]');
    const chartPm10Max = root.querySelector('[data-chart-pm10-max]');
    const chartPm10Min = root.querySelector('[data-chart-pm10-min]');
    const chartPm25Avg = root.querySelector('[data-chart-pm25-avg]');
    const chartPm25Max = root.querySelector('[data-chart-pm25-max]');
    const chartPm25Min = root.querySelector('[data-chart-pm25-min]');

    const pm10Canvas = document.getElementById('admin-prediction-pm10-chart');
    const pm25Canvas = document.getElementById('admin-prediction-pm25-chart');

    if (
        !chartDateToggle || !chartDateMenu || !chartDateLabel || !chartDateCurrent || !chartDatePrev || !chartDateNext || !chartDateList ||
        !chartIndicatorToggle || !chartIndicatorLabel || !chartIndicatorMenu || !chartIndicatorOptions.length ||
        !chartPm10Card || !chartPm25Card || !chartPm10Wrap || !chartPm25Wrap ||
        !chartPm10Avg || !chartPm10Max || !chartPm10Min || !chartPm25Avg || !chartPm25Max || !chartPm25Min ||
        !(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)
    ) return;

    const buildChart = (ctx, color, label) => new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({ length: 24 }, (_, hour) => String(hour)),
            datasets: [{
                label,
                data: [],
                borderColor: color,
                backgroundColor: color,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 2,
                tension: 0.25,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => `Jam ${items[0]?.label}:00`,
                        label: (item) => `${item.parsed.y} ug/m3`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: 24 },
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

    const pm10Chart = buildChart(pm10Canvas, '#2563eb', 'PM10');
    const pm25Chart = buildChart(pm25Canvas, '#16a34a', 'PM2.5');

    const renderDateMenu = () => {
        chartDateLabel.textContent = formatIdDate(state.chartDate);
        chartDateCurrent.textContent = formatIdDate(state.chartDate);

        const options = buildDateOptions(state.chartDate);
        const currentIdx = options.indexOf(state.chartDate);
        chartDatePrev.disabled = currentIdx <= 0;
        chartDateNext.disabled = currentIdx < 0 || currentIdx >= options.length - 1;

        chartDateList.innerHTML = options.map((dateStr) => {
            const active = dateStr === state.chartDate;
            return `<button type="button" data-chart-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatShortDate(dateStr)}</button>`;
        }).join('');

        chartDateList.querySelectorAll('[data-chart-date-value]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const value = btn.getAttribute('data-chart-date-value');
                if (!value) return;
                state.chartDate = value;
                state.showDateMenu = false;
                chartDateMenu.classList.add('hidden');
                renderChartSection();
            });
        });
    };

    const renderChartSection = () => {
        chartDateLabel.textContent = formatIdDate(state.chartDate);
        chartIndicatorLabel.textContent = state.chartFilter;

        const pm10Stat = stats(PM10_SERIES);
        const pm25Stat = stats(PM25_SERIES);

        chartPm10Avg.textContent = pm10Stat.avg;
        chartPm10Max.textContent = String(pm10Stat.max);
        chartPm10Min.textContent = String(pm10Stat.min);

        chartPm25Avg.textContent = pm25Stat.avg;
        chartPm25Max.textContent = String(pm25Stat.max);
        chartPm25Min.textContent = String(pm25Stat.min);

        const showPm10 = state.chartFilter === 'Semua' || state.chartFilter === 'PM10';
        const showPm25 = state.chartFilter === 'Semua' || state.chartFilter === 'PM2.5';

        chartPm10Card.classList.toggle('hidden', !showPm10);
        chartPm25Card.classList.toggle('hidden', !showPm25);
        chartPm10Wrap.classList.toggle('hidden', !showPm10);
        chartPm25Wrap.classList.toggle('hidden', !showPm25);

        pm10Chart.data.datasets[0].data = PM10_SERIES;
        pm25Chart.data.datasets[0].data = PM25_SERIES;
        pm10Chart.update();
        pm25Chart.update();

        renderDateMenu();
    };

    chartDateToggle.addEventListener('click', () => {
        state.showDateMenu = !state.showDateMenu;
        chartDateMenu.classList.toggle('hidden', !state.showDateMenu);
    });

    chartDatePrev.addEventListener('click', () => {
        const options = buildDateOptions(state.chartDate);
        const idx = options.indexOf(state.chartDate);
        if (idx > 0) {
            state.chartDate = options[idx - 1];
            renderChartSection();
        }
    });

    chartDateNext.addEventListener('click', () => {
        const options = buildDateOptions(state.chartDate);
        const idx = options.indexOf(state.chartDate);
        if (idx >= 0 && idx < options.length - 1) {
            state.chartDate = options[idx + 1];
            renderChartSection();
        }
    });

    chartIndicatorToggle.addEventListener('click', () => {
        state.showIndicatorMenu = !state.showIndicatorMenu;
        chartIndicatorMenu.classList.toggle('hidden', !state.showIndicatorMenu);
    });

    chartIndicatorOptions.forEach((option) => {
        option.addEventListener('click', () => {
            const value = option.getAttribute('data-chart-indicator-option');
            if (!value) return;
            state.chartFilter = value;
            state.showIndicatorMenu = false;
            chartIndicatorMenu.classList.add('hidden');
            renderChartSection();
        });
    });

    document.addEventListener('click', (event) => {
        if (!chartDateMenu.contains(event.target) && !chartDateToggle.contains(event.target)) {
            state.showDateMenu = false;
            chartDateMenu.classList.add('hidden');
        }
        if (!chartIndicatorMenu.contains(event.target) && !chartIndicatorToggle.contains(event.target)) {
            state.showIndicatorMenu = false;
            chartIndicatorMenu.classList.add('hidden');
        }
    });

    renderChartSection();
};

document.addEventListener('DOMContentLoaded', initAdminPredictionChart);
