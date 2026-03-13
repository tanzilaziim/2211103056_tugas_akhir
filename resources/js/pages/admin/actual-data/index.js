import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

const buildSeriesForDate = (rows, date, key) => {
    if (!date) return [];

    return rows
        .filter((row) => row.waktu.startsWith(date))
        .map((row, index) => {
            const timePart = row.waktu.split(' ')[1] || '';
            const label = timePart.length >= 5 ? timePart.slice(0, 5) : String(index);
            const rawValue = Number(row[key]);
            return {
                label,
                value: Number.isFinite(rawValue) ? rawValue : null,
            };
        });
};

const initAdminActualData = () => {
    const root = document.querySelector('[data-page="admin-actual-data"]');
    if (!root) return;

    const state = {
        rows: [],
        availableDates: [],
        fileName: null,
        pendingFile: null,
        error: null,
        selectedDate: null,
        toastTimer: null,
        isUploading: false,
    };

    const api = async (url, options = {}) => {
        const headers = {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers || {}),
        };

        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            headers,
        });

        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            const message = payload?.message || 'Terjadi kesalahan.';
            throw new Error(message);
        }

        return payload;
    };

    const inputFile = root.querySelector('[data-file-input]');
    const dropZone = root.querySelector('[data-drop-zone]');
    const btnChoose = root.querySelector('[data-btn-choose-file]');
    const btnUpload = root.querySelector('[data-btn-upload]');
    const errorText = root.querySelector('[data-file-error]');
    const toast = root.querySelector('[data-upload-toast]');
    const toastMessage = root.querySelector('[data-upload-toast-message]');

    const dateToggle = root.querySelector('[data-date-toggle]');
    const dateInput = root.querySelector('[data-date-input]');
    const dateDisplay = root.querySelector('[data-date-display]');

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
                            title: (items) => `Waktu ${items[0]?.label}`,
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

    const getFilteredRows = () => {
        if (!state.selectedDate) return [];
        return state.rows.filter((row) => row.waktu.startsWith(state.selectedDate));
    };

    const renderCharts = () => {
        const pm10Data = buildSeriesForDate(state.rows, state.selectedDate, 'pm10');
        const pm25Data = buildSeriesForDate(state.rows, state.selectedDate, 'pm25');

        pm10Chart.data.labels = pm10Data.map((item) => item.label);
        pm10Chart.data.datasets[0].data = pm10Data.map((item) => item.value);
        pm10Chart.update();

        pm25Chart.data.labels = pm25Data.map((item) => item.label);
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
        if (btnChoose) {
            if (state.fileName) {
                btnChoose.innerHTML = `<i class="ph ph-file text-base"></i><span class="truncate">${state.fileName}</span>`;
                btnChoose.classList.remove('border-surface-200', 'bg-surface-100', 'text-surface-300');
                btnChoose.classList.add('border-primary-300/35', 'bg-primary-50', 'text-primary-300');
                btnChoose.title = state.fileName;
            } else {
                btnChoose.innerHTML = '<i class="ph ph-magnifying-glass text-base"></i><span class="truncate">Cari File CSV</span>';
                btnChoose.classList.remove('border-primary-300/35', 'bg-primary-50', 'text-primary-300');
                btnChoose.classList.add('border-surface-200', 'bg-surface-100', 'text-surface-300');
                btnChoose.title = '';
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

    const renderUploadState = () => {
        if (!btnUpload) return;

        if (state.isUploading) {
            btnUpload.disabled = true;
            btnUpload.classList.add('cursor-not-allowed', 'opacity-70');
            btnUpload.innerHTML = '<i class="ph ph-circle-notch animate-spin text-lg"></i><span>Mengunggah...</span>';
        } else {
            btnUpload.disabled = false;
            btnUpload.classList.remove('cursor-not-allowed', 'opacity-70');
            btnUpload.innerHTML = '<i class="ph ph-upload-simple text-lg"></i><span>Unggah File</span>';
        }

        if (btnChoose) {
            btnChoose.disabled = state.isUploading;
            btnChoose.classList.toggle('cursor-not-allowed', state.isUploading);
            btnChoose.classList.toggle('opacity-70', state.isUploading);
        }
    };

    const showToast = (type, message) => {
        if (!toast || !toastMessage) return;

        if (state.toastTimer) {
            clearTimeout(state.toastTimer);
            state.toastTimer = null;
        }

        toast.classList.remove('hidden', 'border-primary-300/30', 'bg-primary-50', 'border-danger-300/40', 'bg-danger-50');
        toastMessage.classList.remove('text-primary-400', 'text-danger-300');

        if (type === 'success') {
            toast.classList.add('border-primary-300/30', 'bg-primary-50');
            toastMessage.classList.add('text-primary-400');
        } else {
            toast.classList.add('border-danger-300/40', 'bg-danger-50');
            toastMessage.classList.add('text-danger-300');
        }

        toastMessage.textContent = message;

        state.toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            state.toastTimer = null;
        }, 3200);
    };

    const renderDatePicker = () => {
        const allDates = state.availableDates;
        if (!allDates.length) {
            if (dateDisplay) dateDisplay.textContent = 'Belum ada data';
            if (dateInput instanceof HTMLInputElement) {
                dateInput.value = '';
                dateInput.min = '';
                dateInput.max = '';
                dateInput.disabled = true;
            }
            return;
        }

        if (!allDates.includes(state.selectedDate)) {
            state.selectedDate = allDates[0];
        }

        if (dateDisplay) dateDisplay.textContent = formatDateId(state.selectedDate);
        if (dateInput instanceof HTMLInputElement) {
            dateInput.disabled = false;
            dateInput.min = allDates[0];
            dateInput.max = allDates[allDates.length - 1];
            dateInput.value = state.selectedDate || '';
        }
    };

    const renderAll = () => {
        renderFileMeta();
        renderUploadState();
        renderDatePicker();
        renderCharts();
        renderTable();
    };

    const applyDatasetPayload = (payloadData) => {
        if (!payloadData) return;

        const rows = Array.isArray(payloadData.rows) ? payloadData.rows : [];
        const dates = Array.isArray(payloadData.dates) ? payloadData.dates : getDatesFromData(rows);

        state.rows = rows;
        state.availableDates = dates;

        if (payloadData.selected_date && typeof payloadData.selected_date === 'string') {
            state.selectedDate = payloadData.selected_date;
        } else if (dates.length) {
            state.selectedDate = dates[0];
        } else {
            state.selectedDate = null;
        }
    };

    const loadDataset = (date = null) => {
        const query = date ? `?date=${encodeURIComponent(date)}` : '';
        return api(`/admin/api/actual-data${query}`).then((payload) => {
            applyDatasetPayload(payload.data);
            renderAll();
        });
    };

    const setPendingFile = (file) => {
        state.error = null;
        const ext = file.name.split('.').pop()?.toLowerCase();
        if (ext !== 'csv') {
            state.pendingFile = null;
            state.fileName = null;
            state.error = 'Format file tidak didukung. Gunakan format CSV.';
            showToast('error', state.error);
            renderFileMeta();
            return;
        }

        state.pendingFile = file;
        state.fileName = file.name;
        renderFileMeta();
    };

    const uploadPendingFile = () => {
        if (state.isUploading) return;

        if (!state.pendingFile) {
            state.error = 'Pilih file terlebih dahulu melalui tombol Cari File atau drag & drop.';
            showToast('error', state.error);
            renderFileMeta();
            return;
        }

        state.error = null;
        renderFileMeta();

        const formData = new FormData();
        formData.append('file', state.pendingFile);
        state.isUploading = true;
        renderUploadState();

        api('/admin/api/actual-data/import', {
            method: 'POST',
            body: formData,
        })
            .then((payload) => {
                applyDatasetPayload(payload.data);
                state.fileName = state.pendingFile.name;
                state.error = null;
                state.pendingFile = null;
                if (inputFile) inputFile.value = '';
                renderAll();
                showToast('success', payload?.message || 'File berhasil diunggah.');
            })
            .catch((error) => {
                state.error = error.message;
                renderFileMeta();
                showToast('error', state.error);
            })
            .finally(() => {
                state.isUploading = false;
                renderUploadState();
            });
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
        if (!(dateInput instanceof HTMLInputElement)) return;
        if (typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();
        } else {
            dateInput.focus();
            dateInput.click();
        }
    });

    dateInput?.addEventListener('change', (event) => {
        const value = event.target.value;
        if (!value) return;
        if (!state.availableDates.includes(value)) {
            showToast('error', 'Tanggal ini tidak ada di dataset.');
            if (dateInput instanceof HTMLInputElement) {
                dateInput.value = state.selectedDate || '';
            }
            return;
        }
        loadDataset(value);
    });

    loadDataset()
        .catch(() => {
            renderAll();
        });
};

document.addEventListener('DOMContentLoaded', initAdminActualData);
