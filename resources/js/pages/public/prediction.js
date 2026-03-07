import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const parseJsonScript = (id) => {
    const node = document.getElementById(id);
    if (!node) return [];

    try {
        return JSON.parse(node.textContent || '[]');
    } catch {
        return [];
    }
};

const getRows = (data, threshold) =>
    data.map((d) => ({
        hour: `${String(d.hour).padStart(2, '0')}.00`,
        concentration: d.value,
        indicator: d.value > threshold ? 'Sedang' : 'Baik',
    }));

const calcStats = (data) => {
    const values = data.map((d) => d.value);
    const total = values.reduce((a, b) => a + b, 0);
    const average = values.length ? (total / values.length).toFixed(1) : '0.0';
    return {
        average,
        highest: values.length ? Math.max(...values) : 0,
        lowest: values.length ? Math.min(...values) : 0,
    };
};

const ISPU = {
    baik: { label: 'Baik', hex: '#16A34A', rgba: 'rgba(22, 163, 74, 0.25)' },
    sedang: { label: 'Sedang', hex: '#2563EB', rgba: 'rgba(37, 99, 235, 0.25)' },
    tidakSehat: { label: 'Tidak Sehat', hex: '#FACC15', rgba: 'rgba(250, 204, 21, 0.25)' },
    sangatTidakSehat: { label: 'Sangat Tidak Sehat', hex: '#DC2626', rgba: 'rgba(220, 38, 38, 0.25)' },
    berbahaya: { label: 'Berbahaya', hex: '#111827', rgba: 'rgba(17, 24, 39, 0.25)' },
};

const hexToRgba = (hex, alpha) => {
    const normalized = hex.replace('#', '');
    const bigint = parseInt(normalized.length === 3
        ? normalized.split('').map((c) => c + c).join('')
        : normalized, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const gradientFromCategory = (category) =>
    `linear-gradient(180deg, ${hexToRgba(category.hex, 0.25)} 0%, rgba(255, 255, 255, 0.25) 100%)`;

const ICONS = {
    baik:
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#007B00"></circle><circle cx="135" cy="288.829" r="25.7" fill="#007B00"></circle><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="15" d="M179.4 390.429s14.3 29.6 48.6 29.6c22 0 35.3-9.3 48.6-29.6z" clip-rule="evenodd"></path><path stroke="#007B00" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>',
    sedang:
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0133CC"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0133CC"></circle><path stroke="#0133CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.4 390.429s13.4.5 48.6.5c0 0 32.5 0 48.6-.5M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path></svg>',
    tidakSehat:
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#F0B100" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.4 145.2s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8S386.9 493 227.7 493 47.9 337.6 47.9 337.6s-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.3" cy="289.5" r="25.7" fill="#F0B100"></circle><circle cx="135.2" cy="289.5" r="25.7" fill="#F0B100"></circle><path stroke="#F0B100" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M179.6 420.7s14.3-29.6 48.6-29.6c22 0 35.3 9.3 48.6 29.6M32.1 266.8S14.6 156.3 64.2 96.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5C222.6-8 443.8.8 423.5 265.2"></path></svg>',
    sangatTidakSehat:
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 503" class="w-5 h-5 md:w-8 md:h-8"><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M348.2 144.529s28.1 4.4 26.6 34.8-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-17.8 156.1-177 156.1-179.8-155.4-179.8-155.4-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.3 240.6 0" clip-rule="evenodd"></path><circle cx="320.1" cy="288.829" r="25.7" fill="red"></circle><circle cx="135" cy="288.829" r="25.7" fill="red"></circle><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M76.8 340.829v72c24.8 39.4 70 79.5 150.7 79.5 80.8 0 125.2-40.3 149.3-79.9v-71.6s-98.493-24.121-150-24.121-150 24.121-150 24.121" clip-rule="evenodd"></path><rect width="60" height="60" x="197.8" y="374.329" stroke="red" stroke-width="10" rx="10"></rect><path stroke="red" stroke-linecap="round" stroke-width="10" d="M193.8 348.351s33.35-10.52 66.7 0"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M76.8 340.829S178.794 318.3 229.136 318.3 376.8 340.829 376.8 340.829l19.5-85.3"></path><path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M376.8 340.829s-98.036-22.57-148.233-22.57S76.8 340.829 76.8 340.829l-19.5-85.3"></path><circle cx="228" cy="404.529" r="17.2" fill="red" stroke="red" stroke-width="10"></circle></svg>',
    berbahaya:
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 454 510" class="w-5 h-5 md:w-8 md:h-8"><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M70.8 402.529c-18.9-34.4-23.1-65.6-23.1-65.6s-37.7-2.2-37.7-37.7 39.2-36.3 54-47.4 15.5-22.2 15.5-45.9-4.4-51.8 28.1-61.4c0 0 105.1 47.4 240.5 0 0 0 28.1 4.4 26.6 34.8s-3 70.3 21.5 76.2c24.4 5.9 47.4 12.6 47.4 45.9 0 30.4-39.2 34.8-39.2 34.8s-2.7 30.1-21 64.6M290.2 482.729c-18 6-38.7 9.6-62.7 9.6-24.3 0-45.5-3.6-63.7-9.8"></path><circle cx="320.1" cy="288.829" r="25.7" fill="#0F172A"></circle><circle cx="135" cy="288.829" r="25.7" fill="#0F172A"></circle><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M31.9 266.129s-17.5-110.5 32.1-170.4c0 0-21.5-36.3-11.8-63.6 0 0 50.7 12.3 99.9-9.5 70.3-31.3 291.5-22.5 271.2 241.9"></path><path stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M397.2 367.029c-15.4-21.1-100.3-47.6-118.7-54.2-18.8-6.8-46.3-6.6-51.7-6.5h-.2c-5.4-.1-32.9-.2-51.7 6.5-18.6 6.7-105.9 33.9-119.3 55.2"></path><ellipse cx="99.72" cy="448.953" stroke="#0F172A" stroke-width="20" rx="26" ry="55.101" transform="rotate(-28.092 99.72 448.953)"></ellipse><path stroke="#0F172A" stroke-width="15" d="M117.9 376.729c12.7-6.8 34.5 9.5 48.9 36.4 14.3 26.9 15.7 54.1 3.1 60.9M117.9 376.729l-44.1 23.5M169.8 474.029l-44.1 23.5"></path><ellipse cx="353.905" cy="448.87" stroke="#0F172A" stroke-width="20" rx="55.101" ry="26" transform="rotate(-61.908 353.905 448.87)"></ellipse><path stroke="#0F172A" stroke-width="15" d="M335.7 376.729c-12.7-6.8-34.5 9.5-48.9 36.4-14.3 26.9-15.7 54.1-3.1 60.9M335.7 376.729l44.1 23.5M283.8 474.029l44.1 23.5"></path><circle cx="226.8" cy="408.929" r="21.2" stroke="#0F172A" stroke-width="10"></circle><path stroke="#0F172A" stroke-linecap="round" stroke-width="10" d="M193.4 338.329s37.4-15.8 66.7 0M202.9 358.929s26.8-10.8 47.8 0"></path></svg>',
};

const classify = (value) => {
    if (value <= 15.5) return ISPU.baik;
    if (value <= 55.4) return ISPU.sedang;
    if (value <= 150.4) return ISPU.tidakSehat;
    if (value <= 250.4) return ISPU.sangatTidakSehat;
    return ISPU.berbahaya;
};

const create7d = (base, amp, phase, mod) =>
    Array.from({ length: 24 * 7 }, (_, i) => ({
        hour: i,
        value: Math.max(0, Math.round(base + Math.sin(i * phase) * amp + ((i * mod) % 10))),
    }));

const addDays = (isoDate, days) => {
    const date = new Date(`${isoDate}T00:00:00`);
    date.setDate(date.getDate() + days);
    return date.toISOString().slice(0, 10);
};

const buildDateOptions = (anchor, count = 14) =>
    Array.from({ length: count }, (_, idx) => addDays(anchor, -idx));

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

const formatMonthId = (monthStr) => {
    try {
        const [year, month] = monthStr.split('-').map(Number);
        const date = new Date(year, month - 1, 1);
        return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    } catch {
        return monthStr;
    }
};

const initPredictionPage = () => {
    const root = document.querySelector('[data-page="prediction"]');
    if (!root) return;

    const pm10Data24h = parseJsonScript('pm10-data-24h');
    const pm25Data24h = parseJsonScript('pm25-data-24h');

    const pm10Data12h = pm10Data24h.slice(0, 12);
    const pm25Data12h = pm25Data24h.slice(0, 12);
    const pm10Data7d = create7d(20, 15, 0.4, 7);
    const pm25Data7d = create7d(5, 5, 0.5, 3);

    let activePeriod = '24 Jam';
    let activeTab = 'pm25';

    const periodButtons = Array.from(document.querySelectorAll('[data-period-btn]'));
    const pm10TabBtn = document.querySelector('[data-tab="pm10"]');
    const pm25TabBtn = document.querySelector('[data-tab="pm25"]');
    const tableBody = document.querySelector('[data-detail-table-body]');

    const pm10AvgEl = document.querySelector('[data-pm10-average]');
    const pm10HighEl = document.querySelector('[data-pm10-highest]');
    const pm10LowEl = document.querySelector('[data-pm10-lowest]');
    const pm10StatusEl = document.querySelector('[data-pm10-status]');
    const pm10IconEl = document.querySelector('[data-pm10-icon]');
    const pm10CardEl = document.querySelector('[data-pm10-card]');

    const pm25AvgEl = document.querySelector('[data-pm25-average]');
    const pm25HighEl = document.querySelector('[data-pm25-highest]');
    const pm25LowEl = document.querySelector('[data-pm25-lowest]');
    const pm25StatusEl = document.querySelector('[data-pm25-status]');
    const pm25IconEl = document.querySelector('[data-pm25-icon]');
    const pm25CardEl = document.querySelector('[data-pm25-card]');

    const pm10TitleEl = document.querySelector('[data-chart-title="pm10"]');
    const pm25TitleEl = document.querySelector('[data-chart-title="pm25"]');
    const dateToggle = document.querySelector('[data-date-toggle]');
    const dateMenu = document.querySelector('[data-date-menu]');
    const dateCurrent = document.querySelector('[data-date-current]');
    const datePrev = document.querySelector('[data-date-prev]');
    const dateNext = document.querySelector('[data-date-next]');
    const dateList = document.querySelector('[data-date-list]');
    const dateModeSingle = document.querySelector('[data-date-mode="single"]');
    const dateModeRange = document.querySelector('[data-date-mode="range"]');
    const dateModeMonth = document.querySelector('[data-date-mode="month"]');
    const dateRangeStart = document.querySelector('[data-date-range-start]');
    const dateRangeEnd = document.querySelector('[data-date-range-end]');
    const dateRangeApply = document.querySelector('[data-date-range-apply]');
    const dateMonthInput = document.querySelector('[data-date-month]');
    const dateMonthApply = document.querySelector('[data-date-month-apply]');
    const dateDisplay = document.querySelector('[data-date-display]');
    let selectedDate = new Date().toISOString().slice(0, 10);
    let selectedRangeStart = addDays(selectedDate, -6);
    let selectedRangeEnd = selectedDate;
    let selectedMonth = selectedDate.slice(0, 7);

    const formatDateId = (date) =>
        date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });

    const periodLabel = () => (activePeriod === '7 Hari' ? '7 Hari' : activePeriod);

    const getChartData = () => {
        if (activePeriod === '12 Jam') return { pm10: pm10Data12h, pm25: pm25Data12h };
        if (activePeriod === '7 Hari') return { pm10: pm10Data7d, pm25: pm25Data7d };
        return { pm10: pm10Data24h, pm25: pm25Data24h };
    };

    const getTableData = () => {
        if (activeTab === 'pm10') return getRows(pm10Data24h, 30);
        return getRows(pm25Data24h, 10);
    };

    const makeLabels = (data) =>
        data.map((d) => (activePeriod === '7 Hari' ? `${d.hour}` : `${d.hour}`));

    const pm10Ctx = document.getElementById('pm10-chart');
    const pm25Ctx = document.getElementById('pm25-chart');
    if (!(pm10Ctx instanceof HTMLCanvasElement) || !(pm25Ctx instanceof HTMLCanvasElement)) return;

    const buildChart = (ctx, color, titlePrefix) =>
        new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ label: titlePrefix, data: [], borderColor: color, backgroundColor: color, pointRadius: 4, pointHoverRadius: 5, borderWidth: 2, tension: 0 }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (item) => `${item.parsed.y} µg/m³`,
                            title: (items) => (activePeriod === '7 Hari' ? `Titik ${items[0].label}` : `Jam ${items[0].label}:00`),
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: { color: '#475569', maxRotation: 0, autoSkip: true, maxTicksLimit: activePeriod === '7 Hari' ? 14 : 24 },
                        grid: { color: '#E2E8F0' },
                        border: { color: '#475569' },
                    },
                    y: {
                        ticks: { color: '#475569' },
                        grid: { color: '#E2E8F0' },
                        border: { display: false },
                    },
                },
            },
        });

    const pm10Chart = buildChart(pm10Ctx, '#2563EB', 'PM10');
    const pm25Chart = buildChart(pm25Ctx, '#16A34A', 'PM2.5');

    const renderTable = () => {
        if (!tableBody) return;
        const rows = getTableData();
        tableBody.innerHTML = rows
            .map(
                (row) => `
                <tr class="border-b border-slate-500/20">
                    <td class="py-2 text-center text-slate-600">${row.hour}</td>
                    <td class="py-2 text-center text-slate-600">${row.concentration}</td>
                    <td class="py-2 text-center text-slate-600">${row.indicator}</td>
                </tr>
            `,
            )
            .join('');
    };

    const renderSummary = () => {
        const { pm10, pm25 } = getChartData();
        const pm10Stats = calcStats(pm10);
        const pm25Stats = calcStats(pm25);
        const pm10Class = classify(Number(pm10Stats.average));
        const pm25Class = classify(Number(pm25Stats.average));

        if (pm10AvgEl) pm10AvgEl.textContent = pm10Stats.average;
        if (pm10HighEl) pm10HighEl.textContent = String(pm10Stats.highest);
        if (pm10LowEl) pm10LowEl.textContent = String(pm10Stats.lowest);
        if (pm10StatusEl) pm10StatusEl.textContent = pm10Class.label;
        if (pm10IconEl) pm10IconEl.innerHTML = ICONS[Object.keys(ISPU).find((k) => ISPU[k].label === pm10Class.label) || 'sedang'];
        if (pm10CardEl) {
            pm10CardEl.style.backgroundColor = 'white';
            pm10CardEl.style.backgroundImage = gradientFromCategory(pm10Class);
        }
        if (pm10AvgEl) pm10AvgEl.style.color = pm10Class.hex;
        if (pm10HighEl) pm10HighEl.style.color = pm10Class.hex;
        if (pm10LowEl) pm10LowEl.style.color = pm10Class.hex;

        if (pm25AvgEl) pm25AvgEl.textContent = pm25Stats.average;
        if (pm25HighEl) pm25HighEl.textContent = String(pm25Stats.highest);
        if (pm25LowEl) pm25LowEl.textContent = String(pm25Stats.lowest);
        if (pm25StatusEl) pm25StatusEl.textContent = pm25Class.label;
        if (pm25IconEl) pm25IconEl.innerHTML = ICONS[Object.keys(ISPU).find((k) => ISPU[k].label === pm25Class.label) || 'baik'];
        if (pm25CardEl) {
            pm25CardEl.style.backgroundColor = 'white';
            pm25CardEl.style.backgroundImage = gradientFromCategory(pm25Class);
        }
        if (pm25AvgEl) pm25AvgEl.style.color = pm25Class.hex;
        if (pm25HighEl) pm25HighEl.style.color = pm25Class.hex;
        if (pm25LowEl) pm25LowEl.style.color = pm25Class.hex;
    };

    const renderCharts = () => {
        const { pm10, pm25 } = getChartData();
        const labels10 = makeLabels(pm10);
        const labels25 = makeLabels(pm25);

        pm10Chart.data.labels = labels10;
        pm10Chart.data.datasets[0].data = pm10.map((d) => d.value);
        pm10Chart.options.scales.x.ticks.maxTicksLimit = activePeriod === '7 Hari' ? 14 : 24;
        pm10Chart.update();

        pm25Chart.data.labels = labels25;
        pm25Chart.data.datasets[0].data = pm25.map((d) => d.value);
        pm25Chart.options.scales.x.ticks.maxTicksLimit = activePeriod === '7 Hari' ? 14 : 24;
        pm25Chart.update();

        if (pm10TitleEl) pm10TitleEl.textContent = `PM10 - Prediksi ${periodLabel()} Terakhir`;
        if (pm25TitleEl) pm25TitleEl.textContent = `PM2.5 - Prediksi ${periodLabel()} Terakhir`;
    };

    const setActivePeriodButton = () => {
        periodButtons.forEach((btn) => {
            const period = btn.getAttribute('data-period-btn');
            const active = period === activePeriod;
            btn.classList.toggle('bg-primary-300', active);
            btn.classList.toggle('text-surface-50', active);
            btn.classList.toggle('text-slate-600', !active);
        });
    };

    const setActiveTabButton = () => {
        const setState = (btn, active) => {
            if (!btn) return;
            btn.classList.toggle('bg-primary-300', active);
            btn.classList.toggle('text-surface-50', active);
            btn.classList.toggle('text-slate-600', !active);
        };
        setState(pm10TabBtn, activeTab === 'pm10');
        setState(pm25TabBtn, activeTab === 'pm25');
    };

    const getDateMode = () => {
        if (activePeriod === '7 Hari') return 'range';
        if (activePeriod === '30 Hari') return 'month';
        return 'single';
    };

    const renderDatePicker = () => {
        if (
            !(dateDisplay instanceof HTMLElement) ||
            !(dateModeSingle instanceof HTMLElement) ||
            !(dateModeRange instanceof HTMLElement) ||
            !(dateModeMonth instanceof HTMLElement)
        ) return;

        const mode = getDateMode();
        dateModeSingle.classList.toggle('hidden', mode !== 'single');
        dateModeRange.classList.toggle('hidden', mode !== 'range');
        dateModeMonth.classList.toggle('hidden', mode !== 'month');

        if (mode === 'range') {
            dateDisplay.textContent = `${formatShortDate(selectedRangeStart)} - ${formatShortDate(selectedRangeEnd)}`;
            if (dateRangeStart instanceof HTMLInputElement) dateRangeStart.value = selectedRangeStart;
            if (dateRangeEnd instanceof HTMLInputElement) dateRangeEnd.value = selectedRangeEnd;
            return;
        }

        if (mode === 'month') {
            dateDisplay.textContent = formatMonthId(selectedMonth);
            if (dateMonthInput instanceof HTMLInputElement) dateMonthInput.value = selectedMonth;
            return;
        }

        if (
            !(dateCurrent instanceof HTMLElement) ||
            !(datePrev instanceof HTMLButtonElement) ||
            !(dateNext instanceof HTMLButtonElement) ||
            !(dateList instanceof HTMLElement)
        ) return;

        dateDisplay.textContent = formatDateId(new Date(`${selectedDate}T00:00:00`));
        dateCurrent.textContent = formatDateId(new Date(`${selectedDate}T00:00:00`));

        const options = buildDateOptions(selectedDate, 14);
        const idx = options.indexOf(selectedDate);
        datePrev.disabled = idx <= 0;
        dateNext.disabled = idx < 0 || idx >= options.length - 1;

        dateList.innerHTML = options.map((dateStr) => {
            const active = dateStr === selectedDate;
            return `<button type="button" data-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatShortDate(dateStr)}</button>`;
        }).join('');

        dateList.querySelectorAll('[data-date-value]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const value = btn.getAttribute('data-date-value');
                if (!value) return;
                selectedDate = value;
                dateMenu?.classList.add('hidden');
                renderDatePicker();
            });
        });
    };

    const initDatePicker = () => {
        if (!(dateToggle instanceof HTMLButtonElement) || !(dateMenu instanceof HTMLElement)) return;

        dateToggle.addEventListener('click', () => {
            dateMenu.classList.toggle('hidden');
        });

        datePrev?.addEventListener('click', () => {
            const options = buildDateOptions(selectedDate, 14);
            const idx = options.indexOf(selectedDate);
            if (idx > 0) {
                selectedDate = options[idx - 1];
                renderDatePicker();
            }
        });

        dateNext?.addEventListener('click', () => {
            const options = buildDateOptions(selectedDate, 14);
            const idx = options.indexOf(selectedDate);
            if (idx >= 0 && idx < options.length - 1) {
                selectedDate = options[idx + 1];
                renderDatePicker();
            }
        });

        document.addEventListener('click', (event) => {
            if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
                dateMenu.classList.add('hidden');
            }
        });

        if (
            dateRangeApply instanceof HTMLButtonElement &&
            dateRangeStart instanceof HTMLInputElement &&
            dateRangeEnd instanceof HTMLInputElement
        ) {
            dateRangeApply.addEventListener('click', () => {
                if (!dateRangeStart.value || !dateRangeEnd.value) return;
                selectedRangeStart = dateRangeStart.value;
                selectedRangeEnd = dateRangeEnd.value;
                dateMenu.classList.add('hidden');
                renderDatePicker();
            });
        }

        if (
            dateMonthApply instanceof HTMLButtonElement &&
            dateMonthInput instanceof HTMLInputElement
        ) {
            dateMonthApply.addEventListener('click', () => {
                if (!dateMonthInput.value) return;
                selectedMonth = dateMonthInput.value;
                dateMenu.classList.add('hidden');
                renderDatePicker();
            });
        }

        renderDatePicker();
    };

    periodButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const period = btn.getAttribute('data-period-btn');
            if (!period || period === activePeriod) return;
            activePeriod = period;
            if (activePeriod === '7 Hari') {
                selectedRangeEnd = selectedDate;
                selectedRangeStart = addDays(selectedDate, -6);
            }
            if (activePeriod === '30 Hari') {
                selectedMonth = selectedDate.slice(0, 7);
            }
            setActivePeriodButton();
            renderDatePicker();
            renderSummary();
            renderCharts();
        });
    });

    pm10TabBtn?.addEventListener('click', () => {
        activeTab = 'pm10';
        setActiveTabButton();
        renderTable();
    });

    pm25TabBtn?.addEventListener('click', () => {
        activeTab = 'pm25';
        setActiveTabButton();
        renderTable();
    });

    setActivePeriodButton();
    setActiveTabButton();
    initDatePicker();
    renderSummary();
    renderCharts();
    renderTable();
};

document.addEventListener('DOMContentLoaded', initPredictionPage);
