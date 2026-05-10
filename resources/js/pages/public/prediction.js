import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const ISPU = {
    baik: { key: 'baik', label: 'Baik', hex: '#16A34A' },
    sedang: { key: 'sedang', label: 'Sedang', hex: '#2563EB' },
    tidakSehat: { key: 'tidakSehat', label: 'Tidak Sehat', hex: '#FACC15' },
    sangatTidakSehat: { key: 'sangatTidakSehat', label: 'Sangat Tidak Sehat', hex: '#DC2626' },
    berbahaya: { key: 'berbahaya', label: 'Berbahaya', hex: '#111827' },
};

const ICON_BAIK = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle><circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>';
const ICON_SEDANG = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>';

const resolveIndicator = (value) => {
    const v = Number(value || 0);
    if (v <= 15.5) return ISPU.baik;
    if (v <= 55.4) return ISPU.sedang;
    if (v <= 150.4) return ISPU.tidakSehat;
    if (v <= 250.4) return ISPU.sangatTidakSehat;
    return ISPU.berbahaya;
};

const stats = (values) => {
    const nums = (Array.isArray(values) ? values : []).filter((v) => v !== null && Number.isFinite(Number(v))).map(Number);
    if (!nums.length) return { avg: '0.0', max: 0, min: 0 };
    return {
        avg: (nums.reduce((a, b) => a + b, 0) / nums.length).toFixed(1),
        max: Math.max(...nums),
        min: Math.min(...nums),
    };
};

const formatIdDate = (isoDate) => {
    try {
        const d = new Date(`${isoDate}T00:00:00`);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    } catch {
        return isoDate;
    }
};

const formatMonthId = (monthStr) => {
    try {
        const [year, month] = String(monthStr).split('-').map(Number);
        const date = new Date(year, month - 1, 1);
        return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    } catch {
        return monthStr;
    }
};

const formatShortDate = (isoDate) => {
    try {
        return new Date(`${isoDate}T00:00:00`).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return isoDate;
    }
};

const formatMonthShortId = (monthStr) => {
    try {
        const [year, month] = String(monthStr).split('-').map(Number);
        const date = new Date(year, month - 1, 1);
        return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
    } catch {
        return monthStr;
    }
};

const hexToRgba = (hex, alpha) => {
    const normalized = String(hex || '').replace('#', '');
    const value = parseInt(normalized.length === 3 ? normalized.split('').map((c) => c + c).join('') : normalized, 16);
    const r = (value >> 16) & 255;
    const g = (value >> 8) & 255;
    const b = value & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const gradientFromColor = (hex) => `linear-gradient(180deg, ${hexToRgba(hex, 0.25)} 0%, rgba(255,255,255,0.25) 100%)`;
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const getDateMode = (range) => (range === '7 Hari' ? 'range' : range === '30 Hari' ? 'month' : 'single');

const api = async (url) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
    });
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(payload?.message || 'Gagal memuat prediksi.');
    return payload;
};

const initPredictionPage = () => {
    const root = document.querySelector('[data-page="prediction"]');
    if (!root) return;

    const state = {
        range: '24 Jam',
        date: '',
        rangeStart: '',
        rangeEnd: '',
        month: '',
        availableDates: [],
        availableMonths: [],
        activeTab: 'pm10',
        labels: [],
        pm10Series: [],
        pm25Series: [],
        hasActiveRun: false,
    };

    const periodButtons = root.querySelectorAll('[data-period-btn]');
    const dateToggle = root.querySelector('[data-date-toggle]');
    const dateMenu = root.querySelector('[data-date-menu]');
    const dateDisplay = root.querySelector('[data-date-display]');
    const dateSingleInput = root.querySelector('[data-date-single]');
    const dateSingleApply = root.querySelector('[data-date-single-apply]');
    const dateModeSingle = root.querySelector('[data-date-mode="single"]');
    const dateModeRange = root.querySelector('[data-date-mode="range"]');
    const dateModeMonth = root.querySelector('[data-date-mode="month"]');
    const rangeStartInput = root.querySelector('[data-date-range-start]');
    const rangePreview = root.querySelector('[data-date-range-preview]');
    const rangeApplyBtn = root.querySelector('[data-date-range-apply]');
    const monthInput = root.querySelector('[data-date-month]');
    const monthApplyBtn = root.querySelector('[data-date-month-apply]');
    const accessRange = root.querySelector('[data-access-range]');
    const noActiveRunMessage = root.querySelector('[data-no-active-run-message]');

    const pm10Card = root.querySelector('[data-pm10-card]');
    const pm25Card = root.querySelector('[data-pm25-card]');
    const pm10Icon = root.querySelector('[data-pm10-icon]');
    const pm25Icon = root.querySelector('[data-pm25-icon]');
    const pm10Status = root.querySelector('[data-pm10-status]');
    const pm25Status = root.querySelector('[data-pm25-status]');
    const pm10Avg = root.querySelector('[data-pm10-average]');
    const pm10High = root.querySelector('[data-pm10-highest]');
    const pm10Low = root.querySelector('[data-pm10-lowest]');
    const pm25Avg = root.querySelector('[data-pm25-average]');
    const pm25High = root.querySelector('[data-pm25-highest]');
    const pm25Low = root.querySelector('[data-pm25-lowest]');

    const pm10Title = root.querySelector('[data-chart-title="pm10"]');
    const pm25Title = root.querySelector('[data-chart-title="pm25"]');
    const tabPm10 = root.querySelector('[data-tab="pm10"]');
    const tabPm25 = root.querySelector('[data-tab="pm25"]');
    const tableBody = root.querySelector('[data-detail-table-body]');
    const tableTimeHead = root.querySelector('[data-detail-time-head]');

    const pm10Canvas = document.getElementById('pm10-chart');
    const pm25Canvas = document.getElementById('pm25-chart');

    if (
        !periodButtons.length || !dateToggle || !dateMenu || !dateDisplay || !dateSingleInput || !dateSingleApply ||
        !dateModeSingle || !dateModeRange || !dateModeMonth || !rangeStartInput || !rangePreview || !rangeApplyBtn || !monthInput || !monthApplyBtn || !noActiveRunMessage ||
        !pm10Card || !pm25Card || !pm10Icon || !pm25Icon || !pm10Status || !pm25Status || !pm10Avg || !pm10High || !pm10Low || !pm25Avg || !pm25High || !pm25Low ||
        !pm10Title || !pm25Title || !tabPm10 || !tabPm25 || !tableBody || !tableTimeHead ||
        !(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)
    ) return;

    const buildChart = (ctx) => new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [{ data: [], borderColor: '#2563EB', backgroundColor: '#2563EB', pointRadius: 4, pointHoverRadius: 5, borderWidth: 2, tension: 0 }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => {
                            const label = items[0]?.label ?? '-';
                            return state.range === '24 Jam' ? `Jam ${label}` : label;
                        },
                        label: (item) => `${item.parsed.y} ug/m3`,
                    },
                },
            },
            scales: {
                x: { ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: state.range === '30 Hari' ? 15 : 24 }, grid: { color: '#e2e8f0', drawOnChartArea: false }, border: { color: '#cbd5e1' } },
                y: { ticks: { color: '#94a3b8' }, grid: { color: '#e2e8f0' }, border: { display: false } },
            },
        },
    });

    const pm10Chart = buildChart(pm10Canvas);
    const pm25Chart = buildChart(pm25Canvas);

    const buildQuery = () => {
        const params = new URLSearchParams();
        const mapRange = state.range === '7 Hari' ? '7hari' : state.range === '30 Hari' ? '30hari' : '24jam';
        params.set('range', mapRange);
        const mode = getDateMode(state.range);
        if (mode === 'single') params.set('date_single', state.date);
        if (mode === 'range') params.set('date_start', state.rangeStart);
        if (mode === 'month') params.set('date_month', state.month);
        return params.toString();
    };

    const renderAccessibleRange = () => {
        if (!(accessRange instanceof HTMLElement)) return;
        if (!state.availableDates.length) {
            accessRange.textContent = 'Rentang prediksi tersedia: -';
            return;
        }

        const sortedDates = [...state.availableDates].sort();
        const startDate = sortedDates[0];
        const endDate = sortedDates[sortedDates.length - 1];
        const mode = getDateMode(state.range);

        if (mode === 'month') {
            const startMonth = startDate.slice(0, 7);
            const endMonth = endDate.slice(0, 7);
            accessRange.textContent = `Rentang prediksi tersedia: ${formatMonthShortId(startMonth)} - ${formatMonthShortId(endMonth)}`;
            return;
        }

        accessRange.textContent = `Rentang prediksi tersedia: ${formatShortDate(startDate)} - ${formatShortDate(endDate)}`;
    };

    const renderSystemMessage = () => {
        noActiveRunMessage.classList.toggle('hidden', state.hasActiveRun);
    };

    const normalizeToAvailableDate = (dateValue) => {
        if (!state.availableDates.length) return dateValue;
        if (state.availableDates.includes(dateValue)) return dateValue;
        const candidate = state.availableDates.filter((d) => d <= dateValue).at(-1);
        return candidate || state.availableDates[0];
    };

    const renderPeriodButtons = () => {
        periodButtons.forEach((button) => {
            const active = button.getAttribute('data-period-btn') === state.range;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-slate-600', !active);
        });
    };

    const renderDateMenu = () => {
        const mode = getDateMode(state.range);
        dateModeSingle.classList.toggle('hidden', mode !== 'single');
        dateModeRange.classList.toggle('hidden', mode !== 'range');
        dateModeMonth.classList.toggle('hidden', mode !== 'month');
        renderAccessibleRange();

        if (mode === 'single') {
            dateDisplay.textContent = state.date ? formatIdDate(state.date) : '-';
            dateSingleInput.value = state.date || '';
            if (state.availableDates.length) {
                dateSingleInput.min = state.availableDates[0];
                dateSingleInput.max = state.availableDates[state.availableDates.length - 1];
            }
            return;
        }

        if (mode === 'range') {
            dateDisplay.textContent = state.rangeStart && state.rangeEnd ? `${formatIdDate(state.rangeStart)} - ${formatIdDate(state.rangeEnd)}` : '-';
            rangeStartInput.value = state.rangeStart || '';
            rangePreview.textContent = state.rangeStart && state.rangeEnd ? `${formatIdDate(state.rangeStart)} s.d ${formatIdDate(state.rangeEnd)}` : '-';
            return;
        }

        dateDisplay.textContent = state.month ? formatMonthId(state.month) : '-';
        if (state.availableMonths.length) {
            monthInput.min = state.availableMonths[0];
            monthInput.max = state.availableMonths[state.availableMonths.length - 1];
        }
        monthInput.value = state.month || '';
    };

    const renderCards = () => {
        const pm10Stat = stats(state.pm10Series);
        const pm25Stat = stats(state.pm25Series);
        const pm10Indicator = resolveIndicator(pm10Stat.avg);
        const pm25Indicator = resolveIndicator(pm25Stat.avg);

        pm10Status.textContent = pm10Indicator.label;
        pm25Status.textContent = pm25Indicator.label;
        pm10Icon.innerHTML = pm10Indicator.key === 'baik' ? ICON_BAIK : ICON_SEDANG;
        pm25Icon.innerHTML = pm25Indicator.key === 'baik' ? ICON_BAIK : ICON_SEDANG;

        pm10Card.style.background = gradientFromColor(pm10Indicator.hex);
        pm25Card.style.background = gradientFromColor(pm25Indicator.hex);

        pm10Avg.textContent = pm10Stat.avg;
        pm10High.textContent = String(pm10Stat.max);
        pm10Low.textContent = String(pm10Stat.min);
        pm25Avg.textContent = pm25Stat.avg;
        pm25High.textContent = String(pm25Stat.max);
        pm25Low.textContent = String(pm25Stat.min);

        pm10Avg.style.color = pm10Indicator.hex;
        pm10High.style.color = pm10Indicator.hex;
        pm10Low.style.color = pm10Indicator.hex;
        pm25Avg.style.color = pm25Indicator.hex;
        pm25High.style.color = pm25Indicator.hex;
        pm25Low.style.color = pm25Indicator.hex;
    };

    const renderCharts = () => {
        const periodText = state.range;
        pm10Title.textContent = `PM10 - Grafik Prediksi (${periodText})`;
        pm25Title.textContent = `PM2.5 - Grafik Prediksi (${periodText})`;

        const pm10Color = resolveIndicator(stats(state.pm10Series).avg).hex;
        const pm25Color = resolveIndicator(stats(state.pm25Series).avg).hex;

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
    };

    const indicatorLabel = (value) => resolveIndicator(value).label;

    const renderTable = () => {
        const isPm10 = state.activeTab === 'pm10';
        const series = isPm10 ? state.pm10Series : state.pm25Series;
        const timeLabel = state.range === '24 Jam' ? 'Jam' : 'Tanggal';
        tableTimeHead.textContent = timeLabel;

        tabPm10.classList.toggle('bg-primary-300', isPm10);
        tabPm10.classList.toggle('text-surface-50', isPm10);
        tabPm10.classList.toggle('text-slate-600', !isPm10);
        tabPm25.classList.toggle('bg-primary-300', !isPm10);
        tabPm25.classList.toggle('text-surface-50', !isPm10);
        tabPm25.classList.toggle('text-slate-600', isPm10);

        if (!state.labels.length) {
            tableBody.innerHTML = '<tr><td colspan="3" class="py-4 text-center text-slate-500">Prediksi belum tersedia.</td></tr>';
            return;
        }

        tableBody.innerHTML = state.labels.map((label, idx) => {
            const value = series[idx];
            const display = value === null || value === undefined ? '-' : value;
            const indikator = value === null || value === undefined ? '-' : indicatorLabel(value);
            return `<tr class="border-b border-slate-500/20"><td class="py-2 text-center text-slate-600">${label}</td><td class="py-2 text-center text-slate-600">${display}</td><td class="py-2 text-center text-slate-600">${indikator}</td></tr>`;
        }).join('');
    };

    const loadMeta = async () => {
        const payload = await api('/api/public/prediction/meta');
        const data = payload?.data || {};
        state.hasActiveRun = !!data.active_run;
        state.availableDates = Array.isArray(data.dates) ? data.dates : [];
        state.availableMonths = Array.isArray(data.months) ? data.months : [];
        state.date = data?.defaults?.single || state.availableDates[0] || '';
        state.rangeStart = data?.defaults?.start || state.date;
        state.rangeEnd = data?.defaults?.end || state.date;
        state.month = data?.defaults?.month || state.availableMonths[0] || (state.date ? state.date.slice(0, 7) : '');
        renderAccessibleRange();
    };

    const loadData = async () => {
        if (!state.hasActiveRun) {
            state.labels = [];
            state.pm10Series = [];
            state.pm25Series = [];
            renderDateMenu();
            renderSystemMessage();
            renderCards();
            renderCharts();
            renderTable();
            return;
        }

        const payload = await api(`/api/public/prediction?${buildQuery()}`);
        const data = payload?.data || {};
        const applied = data.applied || {};

        state.labels = Array.isArray(data?.pm10?.labels) ? data.pm10.labels : [];
        state.pm10Series = Array.isArray(data?.pm10?.series) ? data.pm10.series : [];
        state.pm25Series = Array.isArray(data?.pm25?.series) ? data.pm25.series : [];

        if (applied.date_single) state.date = applied.date_single;
        if (applied.date_start) state.rangeStart = applied.date_start;
        if (applied.date_end) state.rangeEnd = applied.date_end;
        if (applied.date_month) state.month = applied.date_month;

        renderDateMenu();
        renderSystemMessage();
        renderCards();
        renderCharts();
        renderTable();
    };

    periodButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.getAttribute('data-period-btn');
            if (!value) return;
            state.range = value;
            renderPeriodButtons();
            renderDateMenu();
            await loadData();
        });
    });

    tabPm10.addEventListener('click', () => {
        state.activeTab = 'pm10';
        renderTable();
    });

    tabPm25.addEventListener('click', () => {
        state.activeTab = 'pm25';
        renderTable();
    });

    dateToggle.addEventListener('click', () => {
        dateMenu.classList.toggle('hidden');
    });

    dateSingleApply.addEventListener('click', async () => {
        if (!dateSingleInput.value) return;
        state.date = normalizeToAvailableDate(dateSingleInput.value);
        dateMenu.classList.add('hidden');
        await loadData();
    });

    rangeApplyBtn.addEventListener('click', async () => {
        if (!rangeStartInput.value) return;
        state.rangeStart = rangeStartInput.value;
        const end = new Date(`${state.rangeStart}T00:00:00`);
        end.setDate(end.getDate() + 6);
        state.rangeEnd = end.toISOString().slice(0, 10);
        dateMenu.classList.add('hidden');
        renderDateMenu();
        await loadData();
    });

    monthApplyBtn.addEventListener('click', async () => {
        if (!monthInput.value) return;
        if (state.availableMonths.length && !state.availableMonths.includes(monthInput.value)) {
            return;
        }
        state.month = monthInput.value;
        dateMenu.classList.add('hidden');
        renderDateMenu();
        await loadData();
    });

    document.addEventListener('click', (event) => {
        if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
            dateMenu.classList.add('hidden');
        }
    });

    (async () => {
        try {
            await loadMeta();
            renderPeriodButtons();
            renderDateMenu();
            renderSystemMessage();
            await loadData();
        } catch (error) {
            console.error(error);
            dateDisplay.textContent = 'Gagal memuat data';
        }
    })();
};

document.addEventListener('DOMContentLoaded', initPredictionPage);
