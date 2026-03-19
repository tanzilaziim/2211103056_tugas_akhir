import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const ISPU = {
    baik: { key: 'baik', label: 'Baik', hex: '#16A34A' },
    sedang: { key: 'sedang', label: 'Sedang', hex: '#2563EB' },
    tidakSehat: { key: 'tidakSehat', label: 'Tidak Sehat', hex: '#FACC15' },
    sangatTidakSehat: { key: 'sangatTidakSehat', label: 'Sangat Tidak Sehat', hex: '#DC2626' },
    berbahaya: { key: 'berbahaya', label: 'Berbahaya', hex: '#111827' },
};

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

const parseJsonScript = (id) => {
    const node = document.getElementById(id);
    if (!node) return [];

    try {
        return JSON.parse(node.textContent || '[]');
    } catch {
        return [];
    }
};

const classify = (value) => {
    if (value <= 15.5) return ISPU.baik;
    if (value <= 55.4) return ISPU.sedang;
    if (value <= 150.4) return ISPU.tidakSehat;
    if (value <= 250.4) return ISPU.sangatTidakSehat;
    return ISPU.berbahaya;
};

const calcStats = (data) => {
    const values = data.map((d) => d.value);
    const total = values.reduce((a, b) => a + b, 0);

    return {
        average: values.length ? (total / values.length).toFixed(1) : '0.0',
        highest: values.length ? Math.max(...values) : 0,
        lowest: values.length ? Math.min(...values) : 0,
    };
};

const hexToRgba = (hex, alpha) => {
    const normalized = hex.replace('#', '');
    const value = parseInt(
        normalized.length === 3 ? normalized.split('').map((c) => c + c).join('') : normalized,
        16,
    );

    const r = (value >> 16) & 255;
    const g = (value >> 8) & 255;
    const b = value & 255;

    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const gradientFromCategory = (category) =>
    `linear-gradient(180deg, ${hexToRgba(category.hex, 0.25)} 0%, rgba(255, 255, 255, 0.25) 100%)`;

const formatDateId = (date) =>
    date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

const addDays = (isoDate, days) => {
    const date = new Date(`${isoDate}T00:00:00`);
    date.setDate(date.getDate() + days);
    return date.toISOString().slice(0, 10);
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

const formatMonthId = (monthStr) => {
    try {
        const [year, month] = monthStr.split('-').map(Number);
        const date = new Date(year, month - 1, 1);
        return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    } catch {
        return monthStr;
    }
};

const aggregateByDay = (data, days) => {
    const baseAvg = data.reduce((sum, item) => sum + item.value, 0) / Math.max(data.length, 1);

    return Array.from({ length: days }, (_, idx) => {
        const swing = Math.sin((idx + 1) * 0.7) * 7;
        const trend = (idx % 6) - 2;
        return {
            hour: idx + 1,
            value: Math.max(0, Math.round(baseAvg + swing + trend)),
        };
    });
};

const buildFallbackRanges = (pm10Data24h, pm25Data24h) => ({
    '24 Jam': { pm10: pm10Data24h, pm25: pm25Data24h },
    '7 Hari': { pm10: aggregateByDay(pm10Data24h, 7), pm25: aggregateByDay(pm25Data24h, 7) },
    '30 Hari': { pm10: aggregateByDay(pm10Data24h, 30), pm25: aggregateByDay(pm25Data24h, 30) },
});

const normalizeRanges = (payloadRanges, fallbackRanges) => {
    const normalizePoints = (points) =>
        Array.isArray(points)
            ? points
                  .map((point, idx) => ({
                      hour: Number(point?.hour ?? idx),
                      label: typeof point?.label === 'string' ? point.label : null,
                      value: Number(point?.value ?? 0),
                  }))
                  .filter((point) => Number.isFinite(point.hour) && Number.isFinite(point.value))
            : [];

    const pick = (points, fallback) => (Array.isArray(points) && points.length ? points : fallback);

    return {
        '24 Jam': {
            pm10: pick(normalizePoints(payloadRanges?.['24 Jam']?.pm10), fallbackRanges['24 Jam'].pm10),
            pm25: pick(normalizePoints(payloadRanges?.['24 Jam']?.pm25), fallbackRanges['24 Jam'].pm25),
        },
        '7 Hari': {
            pm10: pick(normalizePoints(payloadRanges?.['7 Hari']?.pm10), fallbackRanges['7 Hari'].pm10),
            pm25: pick(normalizePoints(payloadRanges?.['7 Hari']?.pm25), fallbackRanges['7 Hari'].pm25),
        },
        '30 Hari': {
            pm10: pick(normalizePoints(payloadRanges?.['30 Hari']?.pm10), fallbackRanges['30 Hari'].pm10),
            pm25: pick(normalizePoints(payloadRanges?.['30 Hari']?.pm25), fallbackRanges['30 Hari'].pm25),
        },
    };
};

const initActualDataPage = () => {
    const root = document.querySelector('[data-page="actual-data"]');
    if (!root) return;

    const apiUrl = root.getAttribute('data-api-url') || '/api/public/actual-data';
    const pm10Data24h = parseJsonScript('actual-pm10-data-24h');
    const pm25Data24h = parseJsonScript('actual-pm25-data-24h');
    const fallbackRanges = buildFallbackRanges(pm10Data24h, pm25Data24h);
    let rangeData = fallbackRanges;

    let activeRange = '24 Jam';
    let availableDates = [];
    let availableMonths = [];

    const rangeButtons = Array.from(document.querySelectorAll('[data-range-btn]'));

    const pm10CardEl = document.querySelector('[data-pm10-card]');
    const pm10IconEl = document.querySelector('[data-pm10-icon]');
    const pm10StatusEl = document.querySelector('[data-pm10-status]');
    const pm10AvgEl = document.querySelector('[data-pm10-average]');
    const pm10HighestEl = document.querySelector('[data-pm10-highest]');
    const pm10LowestEl = document.querySelector('[data-pm10-lowest]');

    const pm25CardEl = document.querySelector('[data-pm25-card]');
    const pm25IconEl = document.querySelector('[data-pm25-icon]');
    const pm25StatusEl = document.querySelector('[data-pm25-status]');
    const pm25AvgEl = document.querySelector('[data-pm25-average]');
    const pm25HighestEl = document.querySelector('[data-pm25-highest]');
    const pm25LowestEl = document.querySelector('[data-pm25-lowest]');

    const pm10TitleEl = document.querySelector('[data-chart-title="pm10"]');
    const pm25TitleEl = document.querySelector('[data-chart-title="pm25"]');

    const dateToggle = document.querySelector('[data-date-toggle]');
    const dateMenu = document.querySelector('[data-date-menu]');
    const dateSingleInput = document.querySelector('[data-date-single]');
    const dateSingleApply = document.querySelector('[data-date-single-apply]');
    const dateModeSingle = document.querySelector('[data-date-mode="single"]');
    const dateModeRange = document.querySelector('[data-date-mode="range"]');
    const dateModeMonth = document.querySelector('[data-date-mode="month"]');
    const dateRangeStart = document.querySelector('[data-date-range-start]');
    const dateRangePreview = document.querySelector('[data-date-range-preview]');
    const dateRangeApply = document.querySelector('[data-date-range-apply]');
    const dateMonthInput = document.querySelector('[data-date-month]');
    const dateMonthApply = document.querySelector('[data-date-month-apply]');
    const dateDisplay = document.querySelector('[data-date-display]');
    const accessRange = document.querySelector('[data-access-range]');
    let selectedDate = new Date().toISOString().slice(0, 10);
    let selectedRangeStart = selectedDate;
    let selectedRangeEnd = addDays(selectedDate, 6);
    let selectedMonth = selectedDate.slice(0, 7);

    const pm10Ctx = document.getElementById('actual-pm10-chart');
    const pm25Ctx = document.getElementById('actual-pm25-chart');

    if (!(pm10Ctx instanceof HTMLCanvasElement) || !(pm25Ctx instanceof HTMLCanvasElement)) return;

    const getActiveData = () => rangeData[activeRange] || fallbackRanges[activeRange] || fallbackRanges['24 Jam'];

    const makeLabels = (data) => {
        return data.map((item) => {
            if (item?.label) return item.label;
            if (activeRange === '24 Jam') return `${String(item.hour).padStart(2, '0')}:00`;
            return String(item.hour);
        });
    };

    const buildChart = (ctx, color, pollutant) =>
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: pollutant,
                        data: [],
                        borderColor: color,
                        backgroundColor: color,
                        pointRadius: 2.5,
                        pointHoverRadius: 4,
                        borderWidth: 2,
                        tension: 0.25,
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
                            title: (items) => {
                                const label = items[0]?.label ?? '';
                                return activeRange === '24 Jam' ? `Jam ${label}` : label;
                            },
                            label: (item) => `${item.parsed.y} ug/m3`,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: activeRange === '30 Hari' ? 15 : 24,
                        },
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

    const pm10Chart = buildChart(pm10Ctx, '#2563EB', 'PM10');
    const pm25Chart = buildChart(pm25Ctx, '#16A34A', 'PM2.5');

    const renderCard = (elements, stats, category) => {
        const { cardEl, iconEl, statusEl, avgEl, highestEl, lowestEl } = elements;

        if (cardEl) {
            cardEl.style.backgroundColor = 'white';
            cardEl.style.backgroundImage = gradientFromCategory(category);
        }

        if (iconEl) iconEl.innerHTML = ICONS[category.key] || '';
        if (statusEl) statusEl.textContent = category.label;
        if (avgEl) {
            avgEl.textContent = stats.average;
            avgEl.style.color = category.hex;
        }
        if (highestEl) {
            highestEl.textContent = String(stats.highest);
            highestEl.style.color = category.hex;
        }
        if (lowestEl) {
            lowestEl.textContent = String(stats.lowest);
            lowestEl.style.color = category.hex;
        }
    };

    const renderSummary = () => {
        const { pm10, pm25 } = getActiveData();

        const pm10Stats = calcStats(pm10);
        const pm25Stats = calcStats(pm25);

        const pm10Category = classify(Number(pm10Stats.average));
        const pm25Category = classify(Number(pm25Stats.average));

        renderCard(
            {
                cardEl: pm10CardEl,
                iconEl: pm10IconEl,
                statusEl: pm10StatusEl,
                avgEl: pm10AvgEl,
                highestEl: pm10HighestEl,
                lowestEl: pm10LowestEl,
            },
            pm10Stats,
            pm10Category,
        );

        renderCard(
            {
                cardEl: pm25CardEl,
                iconEl: pm25IconEl,
                statusEl: pm25StatusEl,
                avgEl: pm25AvgEl,
                highestEl: pm25HighestEl,
                lowestEl: pm25LowestEl,
            },
            pm25Stats,
            pm25Category,
        );
    };

    const renderCharts = () => {
        const { pm10, pm25 } = getActiveData();
        const pm10Stats = calcStats(pm10);
        const pm25Stats = calcStats(pm25);
        const pm10Color = classify(Number(pm10Stats.average)).hex;
        const pm25Color = classify(Number(pm25Stats.average)).hex;

        pm10Chart.data.labels = makeLabels(pm10);
        pm10Chart.data.datasets[0].data = pm10.map((item) => item.value);
        pm10Chart.data.datasets[0].borderColor = pm10Color;
        pm10Chart.data.datasets[0].backgroundColor = pm10Color;
        pm10Chart.options.scales.x.ticks.maxTicksLimit = activeRange === '30 Hari' ? 15 : 24;
        pm10Chart.update();

        pm25Chart.data.labels = makeLabels(pm25);
        pm25Chart.data.datasets[0].data = pm25.map((item) => item.value);
        pm25Chart.data.datasets[0].borderColor = pm25Color;
        pm25Chart.data.datasets[0].backgroundColor = pm25Color;
        pm25Chart.options.scales.x.ticks.maxTicksLimit = activeRange === '30 Hari' ? 15 : 24;
        pm25Chart.update();

        if (pm10TitleEl) pm10TitleEl.textContent = `PM10 - Grafik Data Aktual (${activeRange})`;
        if (pm25TitleEl) pm25TitleEl.textContent = `PM2.5 - Grafik Data Aktual (${activeRange})`;
    };

    const setActiveRangeButton = () => {
        rangeButtons.forEach((btn) => {
            const range = btn.getAttribute('data-range-btn');
            const isActive = range === activeRange;

            btn.classList.toggle('bg-primary-300', isActive);
            btn.classList.toggle('text-surface-50', isActive);
            btn.classList.toggle('text-slate-600', !isActive);
        });
    };

    const normalizeToAvailableDate = (dateValue) => {
        if (!availableDates.length) return dateValue;
        if (availableDates.includes(dateValue)) return dateValue;

        const candidate = availableDates.filter((d) => d <= dateValue).at(-1);
        return candidate || availableDates[0];
    };

    const fetchRanges = async (dateValue) => {
        try {
            const normalizedDate = normalizeToAvailableDate(dateValue);
            const query = new URLSearchParams({ date: normalizedDate });
            const response = await fetch(`${apiUrl}?${query.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) return;

            const payload = await response.json();
            availableDates = Array.isArray(payload?.available_dates) ? payload.available_dates : [];
            availableMonths = Array.isArray(payload?.available_months) ? payload.available_months : [];
            if (typeof payload?.date === 'string' && payload.date) {
                selectedDate = payload.date;
            }
            if (!selectedRangeStart || !availableDates.includes(selectedRangeStart)) {
                selectedRangeStart = selectedDate;
            }
            selectedRangeEnd = addDays(selectedRangeStart, 6);
            selectedMonth = selectedDate.slice(0, 7);
            rangeData = normalizeRanges(payload?.ranges, fallbackRanges);
            renderDatePicker();
            renderAccessibleRange();
            renderSummary();
            renderCharts();
        } catch {
            rangeData = fallbackRanges;
            renderAccessibleRange();
        }
    };

    const getDateMode = () => {
        if (activeRange === '7 Hari') return 'range';
        if (activeRange === '30 Hari') return 'month';
        return 'single';
    };

    const renderAccessibleRange = () => {
        if (!(accessRange instanceof HTMLElement)) return;

        if (activeRange === '30 Hari') {
            const months = availableMonths.length
                ? [...availableMonths].sort()
                : [...new Set(availableDates.map((date) => date.slice(0, 7)))].sort();

            if (!months.length) {
                accessRange.textContent = 'Rentang data aktual tersedia: -';
                return;
            }

            accessRange.textContent = `Rentang data aktual tersedia: ${formatMonthId(months[0])} - ${formatMonthId(months[months.length - 1])}`;
            return;
        }

        if (!availableDates.length) {
            accessRange.textContent = 'Rentang data aktual tersedia: -';
            return;
        }

        const sortedDates = [...availableDates].sort();
        accessRange.textContent = `Rentang data aktual tersedia: ${formatShortDate(sortedDates[0])} - ${formatShortDate(sortedDates[sortedDates.length - 1])}`;
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
            if (dateRangeStart instanceof HTMLInputElement) {
                dateRangeStart.value = selectedRangeStart;
                if (availableDates.length) {
                    dateRangeStart.min = availableDates[0];
                    dateRangeStart.max = availableDates[availableDates.length - 1];
                }
            }
            if (dateRangePreview instanceof HTMLElement) {
                dateRangePreview.textContent = `${formatShortDate(selectedRangeStart)} - ${formatShortDate(selectedRangeEnd)}`;
            }
            return;
        }

        if (mode === 'month') {
            dateDisplay.textContent = formatMonthId(selectedMonth);
            if (dateMonthInput instanceof HTMLInputElement) {
                dateMonthInput.value = selectedMonth;
                if (availableMonths.length) {
                    dateMonthInput.min = availableMonths[0];
                    dateMonthInput.max = availableMonths[availableMonths.length - 1];
                }
            }
            return;
        }

        dateDisplay.textContent = formatDateId(new Date(`${selectedDate}T00:00:00`));
        if (dateSingleInput instanceof HTMLInputElement) {
            dateSingleInput.value = selectedDate;
            if (availableDates.length) {
                dateSingleInput.min = availableDates[0];
                dateSingleInput.max = availableDates[availableDates.length - 1];
            }
        }
    };

    const initDatePicker = () => {
        if (!(dateToggle instanceof HTMLButtonElement) || !(dateMenu instanceof HTMLElement)) return;

        dateToggle.addEventListener('click', () => {
            dateMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
                dateMenu.classList.add('hidden');
            }
        });

        if (
            dateSingleApply instanceof HTMLButtonElement &&
            dateSingleInput instanceof HTMLInputElement
        ) {
            dateSingleApply.addEventListener('click', async () => {
                if (!dateSingleInput.value) return;
                selectedDate = normalizeToAvailableDate(dateSingleInput.value);
                dateMenu.classList.add('hidden');
                renderDatePicker();
                await fetchRanges(selectedDate);
            });
        }

        if (
            dateRangeApply instanceof HTMLButtonElement &&
            dateRangeStart instanceof HTMLInputElement
        ) {
            dateRangeApply.addEventListener('click', async () => {
                if (!dateRangeStart.value) return;
                selectedRangeStart = normalizeToAvailableDate(dateRangeStart.value);
                selectedRangeEnd = addDays(selectedRangeStart, 6);
                dateMenu.classList.add('hidden');
                renderDatePicker();
                await fetchRanges(selectedRangeEnd);
            });
        }

        if (
            dateMonthApply instanceof HTMLButtonElement &&
            dateMonthInput instanceof HTMLInputElement
        ) {
            dateMonthApply.addEventListener('click', async () => {
                if (!dateMonthInput.value) return;
                if (availableMonths.length && !availableMonths.includes(dateMonthInput.value)) return;
                selectedMonth = dateMonthInput.value;
                selectedDate = `${selectedMonth}-01`;
                dateMenu.classList.add('hidden');
                renderDatePicker();
                await fetchRanges(selectedDate);
            });
        }

        renderDatePicker();
    };

    rangeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const selected = btn.getAttribute('data-range-btn');
            if (!selected || selected === activeRange) return;

            activeRange = selected;
            if (activeRange === '7 Hari') {
                selectedRangeStart = selectedDate;
                selectedRangeEnd = addDays(selectedDate, 6);
            }
            if (activeRange === '30 Hari') {
                selectedMonth = selectedDate.slice(0, 7);
            }
            setActiveRangeButton();
            renderDatePicker();
            renderAccessibleRange();
            renderSummary();
            renderCharts();
        });
    });

    initDatePicker();
    setActiveRangeButton();
    renderAccessibleRange();
    renderSummary();
    renderCharts();
    fetchRanges(selectedDate);
};

document.addEventListener('DOMContentLoaded', initActualDataPage);
