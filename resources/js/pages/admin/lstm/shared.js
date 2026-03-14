export const ITEMS_PER_PAGE = 5;

export const MOCK_DATA = [
    { id: 1, waktuEksekusi: '01-01-2025 23.59', tanggalPrediksi: '01 Januari 2026', status: 'Success' },
    { id: 2, waktuEksekusi: '01-01-2025 23.59', tanggalPrediksi: '01 Januari 2026', status: 'Failed' },
    { id: 3, waktuEksekusi: '01-01-2025 23.59', tanggalPrediksi: '01 Januari 2026', status: 'Failed' },
    { id: 4, waktuEksekusi: '01-01-2025 23.59', tanggalPrediksi: '01 Januari 2026', status: 'Failed' },
    { id: 5, waktuEksekusi: '01-01-2025 23.59', tanggalPrediksi: '01 Januari 2026', status: 'Failed' },
    { id: 6, waktuEksekusi: '02-01-2025 10.00', tanggalPrediksi: '02 Januari 2026', status: 'Success' },
    { id: 7, waktuEksekusi: '03-01-2025 08.30', tanggalPrediksi: '03 Januari 2026', status: 'Failed' },
];

export const EVAL_RUNS = {
    'run-1': {
        label: '30-12-2025 22.59',
        metrics: {
            pm10: { mae: '0,0000', mse: '0,0000', rmse: '0,0000', r2: '0,0000' },
            pm25: { mae: '0,0000', mse: '0,0000', rmse: '0,0000', r2: '0,0000' },
        },
    },
    'run-2': {
        label: '31-12-2025 08.15',
        metrics: {
            pm10: { mae: '0,3211', mse: '0,2182', rmse: '0,4671', r2: '0,9123' },
            pm25: { mae: '0,1823', mse: '0,0931', rmse: '0,3051', r2: '0,9275' },
        },
    },
    'run-3': {
        label: '01-01-2026 06.45',
        metrics: {
            pm10: { mae: '0,2870', mse: '0,1877', rmse: '0,4332', r2: '0,9342' },
            pm25: { mae: '0,1515', mse: '0,0764', rmse: '0,2764', r2: '0,9488' },
        },
    },
};

export const LOG_STEPS = [
    {
        number: 1,
        title: 'Preprocessing',
        duration: '5s',
        statCards: [
            { label: 'Missing value awal', value: '82' },
            { label: 'Missing value akhir', value: '0' },
            { label: 'Outlier ditangani', value: '182' },
            { label: 'Interval inkonsisten', value: '0' },
        ],
        notes: ['Missing value ditangani dengan interpolasi.', 'Outlier ditangani dengan winsorizing.', 'Interval data sudah konsisten per jam.'],
    },
    {
        number: 2,
        title: 'Scaling',
        duration: '3s',
        statCards: [
            { label: 'Min-Max PM10', value: '12 / 120' },
            { label: 'Min-Max PM2.5', value: '2 / 75' },
        ],
        notes: ['Normalisasi Min-Max diterapkan pada fitur PM10 dan PM2.5.'],
    },
    {
        number: 3,
        title: 'Windowing',
        duration: '4s',
        statCards: [
            { label: 'Lookback', value: '48 step' },
            { label: 'Horizon', value: '24 step' },
            { label: 'Train', value: '80%' },
            { label: 'Test', value: '20%' },
        ],
        notes: ['Dataset dipecah menjadi train/test setelah proses windowing.'],
    },
    {
        number: 4,
        title: 'Training',
        duration: '18s',
        statCards: [
            { label: 'Status', value: 'Trained', valueClass: 'text-ispu-baik' },
            { label: 'Training Loss', value: '0.0112' },
            { label: 'Validation Loss', value: '0.0134' },
        ],
        note: 'Model berhasil dilatih dan siap digunakan untuk prediksi.',
    },
    {
        number: 5,
        title: 'Generate',
        duration: '2s',
        statCards: [
            { label: 'Jumlah titik prediksi', value: '24' },
            { label: 'Timestamp run', value: '30-12-2025 23:59' },
            { label: 'Lama prediksi', value: '01-01-2026 00:00 s.d 02-01-2026 00:00', wide: true },
        ],
        showLink: true,
    },
    {
        number: 6,
        title: 'Evaluasi',
        duration: '2s',
        statCards: [],
        actionLabel: 'Buka',
    },
];

export const addDays = (isoDate, days) => {
    const d = new Date(`${isoDate}T00:00:00`);
    d.setDate(d.getDate() + days);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

export const formatIdDate = (isoDate) => {
    try {
        return new Date(`${isoDate}T00:00:00`).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return isoDate;
    }
};

export const formatShortDate = (isoDate) => {
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

export const formatMonthId = (monthStr) => {
    try {
        const [year, month] = monthStr.split('-').map(Number);
        return new Date(year, month - 1, 1).toLocaleDateString('id-ID', {
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return monthStr;
    }
};

export const buildDateOptions = (anchor, count = 20) =>
    Array.from({ length: count }, (_, idx) => addDays(anchor, -idx));

export const getDateMode = (range) => {
    if (range === '7hari') return 'range';
    if (range === '30hari') return 'month';
    return 'single';
};

const createSeries = (base, variance, length) => {
    const labels = Array.from({ length }, (_, i) => i);
    const actual = labels.map((i) => Math.max(0, Math.round(base + Math.sin(i * 0.5) * variance + ((i * 7) % 5))));
    const predicted = actual.map((v, i) => Math.max(0, Number((v + Math.sin(i * 0.3) * 1.2).toFixed(1))));
    return { labels, actual, predicted };
};

export const getEvalChartData = (range, runId) => {
    const runOffset = runId === 'run-1' ? 0 : runId === 'run-2' ? 2 : 4;
    if (range === '7hari') {
        return {
            pm10: createSeries(28 + runOffset, 8, 7),
            pm25: createSeries(8 + runOffset * 0.3, 3, 7),
        };
    }
    if (range === '30hari') {
        return {
            pm10: createSeries(27 + runOffset, 10, 30),
            pm25: createSeries(7 + runOffset * 0.3, 4, 30),
        };
    }
    return {
        pm10: createSeries(30 + runOffset, 12, 24),
        pm25: createSeries(9 + runOffset * 0.4, 5, 24),
    };
};
