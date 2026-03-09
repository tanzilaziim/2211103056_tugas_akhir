import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const SAMPLE_DATA = Array.from({ length: 24 }, (_, hour) => ({
    waktu: `2025-01-01 ${String(hour).padStart(2, '0')}:00:00`,
    pm10: 41,
    pm25: 15,
}));

const FALLBACK_PM10 = [35, 35, 25, 22, 16, 28, 37, 50, 43, 36, 30, 30, 42, 43, 43, 30, 19, 15, 30, 45, 47, 29, 25, 20];
const FALLBACK_PM25 = [10, 9, 13, 14, 6, 4, 2, 8, 12, 14, 14, 10, 8, 13, 15, 2, 3, 10, 12, 15, 2, 3, 13, 5];

const parseCsv = (text) => {
    const lines = text
        .trim()
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);

    if (lines.length < 2) return [];

    const header = lines[0].split(',').map((cell) => cell.trim().toLowerCase());
    const waktuIdx = header.findIndex((h) => h === 'waktu');
    const pm10Idx = header.findIndex((h) => h === 'pm10');
    const pm25Idx = header.findIndex((h) => ['pm2.5', 'pm25', 'pm2,5'].includes(h));

    if (waktuIdx < 0 || pm10Idx < 0 || pm25Idx < 0) return [];

    return lines
        .slice(1)
        .map((line) => {
            const cols = line.split(',');
            return {
                waktu: (cols[waktuIdx] || '').trim(),
                pm10: Number.parseFloat((cols[pm10Idx] || '0').trim()),
                pm25: Number.parseFloat((cols[pm25Idx] || '0').trim()),
            };
        })
        .filter((row) => row.waktu);
};

const getDatesFromData = (rows) => {
    const dateSet = new Set();
    rows.forEach((row) => {
        const datePart = row.waktu.split(' ')[0];
        if (datePart) dateSet.add(datePart);
    });
    return Array.from(dateSet).sort();
};

const parseDate = (dateStr) => new Date(`${dateStr}T00:00:00`);

const formatDateId = (dateStr, short = false) => {
    try {
        const date = parseDate(dateStr);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: short ? 'short' : 'long',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
};

const buildHourlyData = (rows, date, key) => {
    const filtered = rows.filter((row) => row.waktu.startsWith(date));
    if (filtered.length) {
        return filtered.map((row, index) => ({
            hour: index,
            value: Number.isFinite(row[key]) ? row[key] : 0,
        }));
    }

    const fallback = key === 'pm10' ? FALLBACK_PM10 : FALLBACK_PM25;
    return fallback.map((value, hour) => ({ hour, value }));
};

const initAdminActualData = () => {
    const root = document.querySelector('[data-page="admin-actual-data"]');
    if (!root) return;

    const state = {
        rows: [...SAMPLE_DATA],
        fileName: null,
        pendingFile: null,
        error: null,
        selectedDate: '2025-01-01',
        showDateMenu: false,
    };

    const inputFile = root.querySelector('[data-file-input]');
    const dropZone = root.querySelector('[data-drop-zone]');
    const btnChoose = root.querySelector('[data-btn-choose-file]');
    const btnUpload = root.querySelector('[data-btn-upload]');
    const fileNameText = root.querySelector('[data-file-name]');
    const errorText = root.querySelector('[data-file-error]');

    const dateToggle = root.querySelector('[data-date-toggle]');
    const dateMenu = root.querySelector('[data-date-menu]');
    const dateDisplay = root.querySelector('[data-date-display]');
    const dateCurrent = root.querySelector('[data-date-current]');
    const datePrev = root.querySelector('[data-date-prev]');
    const dateNext = root.querySelector('[data-date-next]');
    const dateList = root.querySelector('[data-date-list]');

    const datasetBody = root.querySelector('[data-dataset-body]');

    const pm10Canvas = document.getElementById('admin-actual-pm10-chart');
    const pm25Canvas = document.getElementById('admin-actual-pm25-chart');
    if (!(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)) return;

    const buildChart = (ctx, color, pollutant) =>
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: pollutant,
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

    const pm10Chart = buildChart(pm10Canvas, '#3b82f6', 'PM10');
    const pm25Chart = buildChart(pm25Canvas, '#22c55e', 'PM2.5');

    const getFilteredRows = () => state.rows.filter((row) => row.waktu.startsWith(state.selectedDate));

    const renderCharts = () => {
        const pm10Data = buildHourlyData(state.rows, state.selectedDate, 'pm10');
        const pm25Data = buildHourlyData(state.rows, state.selectedDate, 'pm25');

        pm10Chart.data.labels = pm10Data.map((item) => String(item.hour));
        pm10Chart.data.datasets[0].data = pm10Data.map((item) => item.value);
        pm10Chart.update();

        pm25Chart.data.labels = pm25Data.map((item) => String(item.hour));
        pm25Chart.data.datasets[0].data = pm25Data.map((item) => item.value);
        pm25Chart.update();
    };

    const renderTable = () => {
        const filtered = getFilteredRows();

        if (!datasetBody) return;

        if (!filtered.length) {
            datasetBody.innerHTML = `
                <tr>
                    <td colspan="3" class="py-8 text-center text-sm text-surface-300">Tidak ada data untuk tanggal ini</td>
                </tr>
            `;
            return;
        }

        datasetBody.innerHTML = filtered
            .map(
                (row) => `
                    <tr class="border-b border-surface-300/20">
                        <td class="py-1.5 text-center text-surface-300">${row.waktu}</td>
                        <td class="py-1.5 text-center text-surface-300">${row.pm10}</td>
                        <td class="py-1.5 text-center text-surface-300">${row.pm25}</td>
                    </tr>
                `,
            )
            .join('');
    };

    const renderFileMeta = () => {
        if (fileNameText) {
            if (state.fileName) {
                fileNameText.textContent = `File: ${state.fileName}`;
                fileNameText.classList.remove('hidden');
            } else {
                fileNameText.classList.add('hidden');
            }
        }

        if (errorText) {
            if (state.error) {
                errorText.textContent = state.error;
                errorText.classList.remove('hidden');
            } else {
                errorText.classList.add('hidden');
            }
        }
    };

    const renderDatePicker = () => {
        const allDates = getDatesFromData(state.rows);
        if (!allDates.length) return;

        if (!allDates.includes(state.selectedDate)) {
            state.selectedDate = allDates[0];
        }

        const currentIdx = allDates.indexOf(state.selectedDate);

        if (dateDisplay) dateDisplay.textContent = formatDateId(state.selectedDate);
        if (dateCurrent) dateCurrent.textContent = formatDateId(state.selectedDate);

        if (datePrev instanceof HTMLButtonElement) datePrev.disabled = currentIdx <= 0;
        if (dateNext instanceof HTMLButtonElement) dateNext.disabled = currentIdx >= allDates.length - 1;

        if (dateList) {
            dateList.innerHTML = allDates
                .map((dateStr) => {
                    const activeClass =
                        dateStr === state.selectedDate
                            ? 'bg-primary-300 text-surface-50'
                            : 'text-surface-300 hover:bg-surface-200';
                    return `<button type="button" data-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${activeClass}">${formatDateId(dateStr, true)}</button>`;
                })
                .join('');

            dateList.querySelectorAll('[data-date-value]').forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    const value = event.currentTarget.getAttribute('data-date-value');
                    if (!value) return;
                    state.selectedDate = value;
                    state.showDateMenu = false;
                    if (dateMenu) dateMenu.classList.add('hidden');
                    renderDatePicker();
                    renderCharts();
                    renderTable();
                });
            });
        }
    };

    const renderAll = () => {
        renderFileMeta();
        renderDatePicker();
        renderCharts();
        renderTable();
    };

    const setPendingFile = (file) => {
        state.error = null;
        const ext = file.name.split('.').pop()?.toLowerCase();
        if (ext !== 'csv') {
            state.pendingFile = null;
            state.fileName = null;
            state.error = 'Format file tidak didukung. Gunakan format CSV.';
            renderFileMeta();
            return;
        }

        state.pendingFile = file;
        state.fileName = file.name;
        renderFileMeta();
    };

    const uploadPendingFile = () => {
        if (!state.pendingFile) {
            state.error = 'Pilih file terlebih dahulu melalui tombol Cari File atau drag & drop.';
            renderFileMeta();
            return;
        }

        state.error = null;

        const reader = new FileReader();
        reader.onload = (event) => {
            const text = String(event.target?.result || '');
            const rows = parseCsv(text);
            if (!rows.length) {
                state.error = 'File tidak dapat dibaca. Pastikan kolom: waktu, pm10, pm2.5';
                renderFileMeta();
                return;
            }

            state.rows = rows;
            state.fileName = state.pendingFile.name;
            state.error = null;
            const dates = getDatesFromData(rows);
            state.selectedDate = dates[0] || '2025-01-01';
            renderAll();
        };
        reader.readAsText(state.pendingFile);
    };

    btnChoose?.addEventListener('click', () => inputFile?.click());
    btnUpload?.addEventListener('click', uploadPendingFile);

    inputFile?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file) setPendingFile(file);
    });

    dropZone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('border-primary-300', 'bg-primary-50');
    });

    dropZone?.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary-300', 'bg-primary-50');
    });

    dropZone?.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('border-primary-300', 'bg-primary-50');
        const file = event.dataTransfer?.files?.[0];
        if (file) setPendingFile(file);
    });

    dateToggle?.addEventListener('click', () => {
        state.showDateMenu = !state.showDateMenu;
        if (dateMenu) dateMenu.classList.toggle('hidden', !state.showDateMenu);
    });

    datePrev?.addEventListener('click', () => {
        const allDates = getDatesFromData(state.rows);
        const idx = allDates.indexOf(state.selectedDate);
        if (idx > 0) {
            state.selectedDate = allDates[idx - 1];
            renderDatePicker();
            renderCharts();
            renderTable();
        }
    });

    dateNext?.addEventListener('click', () => {
        const allDates = getDatesFromData(state.rows);
        const idx = allDates.indexOf(state.selectedDate);
        if (idx < allDates.length - 1) {
            state.selectedDate = allDates[idx + 1];
            renderDatePicker();
            renderCharts();
            renderTable();
        }
    });

    document.addEventListener('click', (event) => {
        if (!dateMenu || !dateToggle) return;
        if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
            state.showDateMenu = false;
            dateMenu.classList.add('hidden');
        }
    });

    renderAll();
};

document.addEventListener('DOMContentLoaded', initAdminActualData);
