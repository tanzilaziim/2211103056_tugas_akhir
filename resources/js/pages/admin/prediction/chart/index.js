import { Chart, registerables } from 'chart.js';
import { formatIdDate, stats } from '../shared';

Chart.register(...registerables);

const HEALTH_COLOR = {
    baik: '#16A34A',
    sedang: '#2563EB',
    tidakSehat: '#FACC15',
    sangatTidakSehat: '#DC2626',
    berbahaya: '#111827',
};

const resolveHealthColor = (value) => {
    if (value <= 15.5) return HEALTH_COLOR.baik;
    if (value <= 55.4) return HEALTH_COLOR.sedang;
    if (value <= 150.4) return HEALTH_COLOR.tidakSehat;
    if (value <= 250.4) return HEALTH_COLOR.sangatTidakSehat;
    return HEALTH_COLOR.berbahaya;
};

const resolveIndicator = (value) => {
    if (value <= 15.5) return { key: 'baik', label: 'Baik' };
    if (value <= 55.4) return { key: 'sedang', label: 'Sedang' };
    if (value <= 150.4) return { key: 'tidakSehat', label: 'Tidak Sehat' };
    if (value <= 250.4) return { key: 'sangatTidakSehat', label: 'Sangat Tidak Sehat' };
    return { key: 'berbahaya', label: 'Berbahaya' };
};

const hexToRgba = (hex, alpha) => {
    const normalized = String(hex || '').replace('#', '');
    const value = parseInt(
        normalized.length === 3 ? normalized.split('').map((c) => c + c).join('') : normalized,
        16,
    );
    const r = (value >> 16) & 255;
    const g = (value >> 8) & 255;
    const b = value & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const gradientFromColor = (hex) =>
    `linear-gradient(180deg, ${hexToRgba(hex, 0.25)} 0%, rgba(255, 255, 255, 0.25) 100%)`;

const ICON_BAIK = `
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="h-6 w-6">
  <path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path>
  <circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle>
  <circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle>
  <path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path>
  <path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path>
</svg>
`;

const ICON_SEDANG = `
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="h-6 w-6">
  <path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path>
  <circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle>
  <circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle>
  <path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path>
</svg>
`;

const getDateMode = (range) => {
    if (range === '7 Hari') return 'range';
    if (range === '30 Hari') return 'month';
    return 'single';
};

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const api = async (url) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
    });
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(payload?.message || 'Gagal memuat data prediksi.');
    return payload;
};

const initAdminPredictionChart = () => {
    const root = document.querySelector('[data-page="admin-prediction-chart"]');
    if (!root) return;

    const state = {
        range: '24 Jam',
        chartDate: '',
        rangeStart: '',
        rangeEnd: '',
        month: '',
        showDateMenu: false,
        availableDates: [],
        hasActiveRun: false,
        pm10Series: [],
        pm25Series: [],
        labels: [],
    };

    const rangeButtons = root.querySelectorAll('[data-chart-range]');
    const chartDateToggle = root.querySelector('[data-chart-date-toggle]');
    const chartDateMenu = root.querySelector('[data-chart-date-menu]');
    const chartDateLabel = root.querySelector('[data-chart-date-label]');
    const chartDateCurrent = root.querySelector('[data-chart-date-current]');
    const chartDatePrev = root.querySelector('[data-chart-date-prev]');
    const chartDateNext = root.querySelector('[data-chart-date-next]');
    const chartDateList = root.querySelector('[data-chart-date-list]');
    const dateModeSingle = root.querySelector('[data-chart-date-mode="single"]');
    const dateModeRange = root.querySelector('[data-chart-date-mode="range"]');
    const dateModeMonth = root.querySelector('[data-chart-date-mode="month"]');
    const rangeStartInput = root.querySelector('[data-chart-range-start]');
    const rangePreview = root.querySelector('[data-chart-range-preview]');
    const rangeApplyBtn = root.querySelector('[data-chart-range-apply]');
    const monthInput = root.querySelector('[data-chart-month]');
    const monthApplyBtn = root.querySelector('[data-chart-month-apply]');

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
    const chartPm10Status = root.querySelector('[data-chart-pm10-status]');
    const chartPm25Status = root.querySelector('[data-chart-pm25-status]');
    const chartPm10Icon = root.querySelector('[data-chart-pm10-icon]');
    const chartPm25Icon = root.querySelector('[data-chart-pm25-icon]');

    const pm10Canvas = document.getElementById('admin-prediction-pm10-chart');
    const pm25Canvas = document.getElementById('admin-prediction-pm25-chart');

    if (
        !rangeButtons.length ||
        !chartDateToggle || !chartDateMenu || !chartDateLabel || !chartDateCurrent || !chartDatePrev || !chartDateNext || !chartDateList ||
        !dateModeSingle || !dateModeRange || !dateModeMonth || !rangeStartInput || !rangePreview || !rangeApplyBtn || !monthInput || !monthApplyBtn ||
        !chartPm10Card || !chartPm25Card || !chartPm10Wrap || !chartPm25Wrap ||
        !chartPm10Avg || !chartPm10Max || !chartPm10Min || !chartPm25Avg || !chartPm25Max || !chartPm25Min ||
        !chartPm10Status || !chartPm25Status || !chartPm10Icon || !chartPm25Icon ||
        !(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)
    ) return;

    const buildChart = (ctx, color, label) => new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label,
                data: [],
                borderColor: color,
                backgroundColor: color,
                pointRadius: 2.5,
                pointHoverRadius: 4,
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
                        title: (items) => {
                            const labelText = items[0]?.label ?? '-';
                            return state.range === '24 Jam' ? `Jam ${labelText}` : labelText;
                        },
                        label: (item) => `${item.parsed.y} ug/m3`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: state.range === '30 Hari' ? 15 : 24 },
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

    const buildQuery = () => {
        const params = new URLSearchParams();
        const mapRange = state.range === '7 Hari' ? '7hari' : state.range === '30 Hari' ? '30hari' : '24jam';
        params.set('range', mapRange);
        const mode = getDateMode(state.range);
        if (mode === 'single') params.set('date_single', state.chartDate);
        if (mode === 'range') params.set('date_start', state.rangeStart);
        if (mode === 'month') params.set('date_month', state.month);
        return params.toString();
    };

    const renderRanges = () => {
        rangeButtons.forEach((button) => {
            const isActive = button.getAttribute('data-chart-range') === state.range;
            button.classList.toggle('bg-primary-300', isActive);
            button.classList.toggle('text-surface-50', isActive);
            button.classList.toggle('text-surface-300', !isActive);
            button.classList.toggle('hover:text-primary-300', !isActive);
        });
    };

    const renderDateMenu = () => {
        const mode = getDateMode(state.range);
        dateModeSingle.classList.toggle('hidden', mode !== 'single');
        dateModeRange.classList.toggle('hidden', mode !== 'range');
        dateModeMonth.classList.toggle('hidden', mode !== 'month');

        if (mode === 'single') {
            chartDateLabel.textContent = state.chartDate ? formatIdDate(state.chartDate) : '-';
            chartDateCurrent.textContent = state.chartDate ? formatIdDate(state.chartDate) : '-';
            const currentIdx = state.availableDates.indexOf(state.chartDate);
            chartDatePrev.disabled = currentIdx <= 0;
            chartDateNext.disabled = currentIdx < 0 || currentIdx >= state.availableDates.length - 1;

            chartDateList.innerHTML = state.availableDates.map((dateStr) => {
                const active = dateStr === state.chartDate;
                return `<button type="button" data-chart-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatIdDate(dateStr)}</button>`;
            }).join('');
            chartDateList.querySelectorAll('[data-chart-date-value]').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const value = btn.getAttribute('data-chart-date-value');
                    if (!value) return;
                    state.chartDate = value;
                    state.showDateMenu = false;
                    chartDateMenu.classList.add('hidden');
                    await loadChartData();
                });
            });
            return;
        }

        if (mode === 'range') {
            chartDateLabel.textContent = state.rangeStart && state.rangeEnd ? `${formatIdDate(state.rangeStart)} - ${formatIdDate(state.rangeEnd)}` : '-';
            rangeStartInput.value = state.rangeStart || '';
            rangePreview.textContent = state.rangeStart && state.rangeEnd
                ? `${formatIdDate(state.rangeStart)} s.d ${formatIdDate(state.rangeEnd)}`
                : '-';
            chartDatePrev.disabled = true;
            chartDateNext.disabled = true;
            chartDateList.innerHTML = '';
            return;
        }

        chartDateLabel.textContent = state.month ? formatIdDate(`${state.month}-01`).replace(/^\d+\s/, '') : '-';
        monthInput.value = state.month || '';
        chartDatePrev.disabled = true;
        chartDateNext.disabled = true;
        chartDateList.innerHTML = '';
    };

    const renderChartSection = () => {
        const pm10Stat = stats(state.pm10Series);
        const pm25Stat = stats(state.pm25Series);
        const pm10Color = resolveHealthColor(Number(pm10Stat.avg || 0));
        const pm25Color = resolveHealthColor(Number(pm25Stat.avg || 0));
        const pm10Indicator = resolveIndicator(Number(pm10Stat.avg || 0));
        const pm25Indicator = resolveIndicator(Number(pm25Stat.avg || 0));

        chartPm10Avg.textContent = pm10Stat.avg;
        chartPm10Max.textContent = String(pm10Stat.max);
        chartPm10Min.textContent = String(pm10Stat.min);
        chartPm25Avg.textContent = pm25Stat.avg;
        chartPm25Max.textContent = String(pm25Stat.max);
        chartPm25Min.textContent = String(pm25Stat.min);
        chartPm10Status.textContent = pm10Indicator.label;
        chartPm25Status.textContent = pm25Indicator.label;
        chartPm10Icon.innerHTML = pm10Indicator.key === 'baik' ? ICON_BAIK : ICON_SEDANG;
        chartPm25Icon.innerHTML = pm25Indicator.key === 'baik' ? ICON_BAIK : ICON_SEDANG;

        chartPm10Card.style.background = gradientFromColor(pm10Color);
        chartPm25Card.style.background = gradientFromColor(pm25Color);
        chartPm10Avg.style.color = pm10Color;
        chartPm10Max.style.color = pm10Color;
        chartPm10Min.style.color = pm10Color;
        chartPm25Avg.style.color = pm25Color;
        chartPm25Max.style.color = pm25Color;
        chartPm25Min.style.color = pm25Color;

        chartPm10Card.classList.remove('hidden');
        chartPm25Card.classList.remove('hidden');
        chartPm10Wrap.classList.remove('hidden');
        chartPm25Wrap.classList.remove('hidden');

        pm10Chart.options.scales.x.ticks.maxTicksLimit = state.range === '30 Hari' ? 15 : 24;
        pm25Chart.options.scales.x.ticks.maxTicksLimit = state.range === '30 Hari' ? 15 : 24;

        pm10Chart.data.labels = state.labels;
        pm25Chart.data.labels = state.labels;
        pm10Chart.data.datasets[0].data = state.pm10Series;
        pm25Chart.data.datasets[0].data = state.pm25Series;
        pm10Chart.data.datasets[0].borderColor = pm10Color;
        pm10Chart.data.datasets[0].backgroundColor = pm10Color;
        pm25Chart.data.datasets[0].borderColor = pm25Color;
        pm25Chart.data.datasets[0].backgroundColor = pm25Color;
        pm10Chart.update();
        pm25Chart.update();
        renderDateMenu();
    };

    const loadMeta = async () => {
        const payload = await api('/admin/api/prediction/meta');
        const data = payload?.data || {};
        state.hasActiveRun = !!data.active_run;
        state.availableDates = Array.isArray(data.dates) ? data.dates : [];
        state.chartDate = data?.defaults?.single || state.availableDates[0] || '';
        state.rangeStart = data?.defaults?.start || state.chartDate;
        state.rangeEnd = data?.defaults?.end || state.chartDate;
        state.month = data?.defaults?.month || (state.chartDate ? state.chartDate.slice(0, 7) : '');
    };

    const loadChartData = async () => {
        if (!state.hasActiveRun) {
            state.labels = [];
            state.pm10Series = [];
            state.pm25Series = [];
            renderChartSection();
            return;
        }
        const payload = await api(`/admin/api/prediction/chart?${buildQuery()}`);
        const data = payload?.data || {};
        state.labels = Array.isArray(data?.pm10?.labels) ? data.pm10.labels : [];
        state.pm10Series = Array.isArray(data?.pm10?.series) ? data.pm10.series : [];
        state.pm25Series = Array.isArray(data?.pm25?.series) ? data.pm25.series : [];
        state.availableDates = Array.isArray(data?.dates) ? data.dates : state.availableDates;
        const applied = data?.applied || {};
        if (applied.date_single) state.chartDate = applied.date_single;
        if (applied.date_start) state.rangeStart = applied.date_start;
        if (applied.date_end) state.rangeEnd = applied.date_end;
        if (applied.date_month) state.month = applied.date_month;
        renderChartSection();
    };

    rangeButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            state.range = button.getAttribute('data-chart-range') || '24 Jam';
            renderRanges();
            renderDateMenu();
            await loadChartData();
        });
    });

    chartDateToggle.addEventListener('click', () => {
        state.showDateMenu = !state.showDateMenu;
        chartDateMenu.classList.toggle('hidden', !state.showDateMenu);
    });

    chartDatePrev.addEventListener('click', async () => {
        if (getDateMode(state.range) !== 'single') return;
        const idx = state.availableDates.indexOf(state.chartDate);
        if (idx > 0) {
            state.chartDate = state.availableDates[idx - 1];
            await loadChartData();
        }
    });

    chartDateNext.addEventListener('click', async () => {
        if (getDateMode(state.range) !== 'single') return;
        const idx = state.availableDates.indexOf(state.chartDate);
        if (idx >= 0 && idx < state.availableDates.length - 1) {
            state.chartDate = state.availableDates[idx + 1];
            await loadChartData();
        }
    });

    rangeApplyBtn.addEventListener('click', async () => {
        if (!rangeStartInput.value) return;
        state.rangeStart = rangeStartInput.value;
        const end = new Date(`${state.rangeStart}T00:00:00`);
        end.setDate(end.getDate() + 6);
        state.rangeEnd = end.toISOString().slice(0, 10);
        state.showDateMenu = false;
        chartDateMenu.classList.add('hidden');
        renderDateMenu();
        await loadChartData();
    });

    monthApplyBtn.addEventListener('click', async () => {
        if (!monthInput.value) return;
        state.month = monthInput.value;
        state.showDateMenu = false;
        chartDateMenu.classList.add('hidden');
        renderDateMenu();
        await loadChartData();
    });

    document.addEventListener('click', (event) => {
        if (!chartDateMenu.contains(event.target) && !chartDateToggle.contains(event.target)) {
            state.showDateMenu = false;
            chartDateMenu.classList.add('hidden');
        }
    });

    (async () => {
        try {
            await loadMeta();
            renderRanges();
            renderDateMenu();
            if (!state.hasActiveRun) {
                chartDateLabel.textContent = 'Belum ada run aktif';
            }
            await loadChartData();
        } catch (error) {
            console.error(error);
            chartDateLabel.textContent = 'Gagal memuat data';
        }
    })();
};

document.addEventListener('DOMContentLoaded', initAdminPredictionChart);
