import { Chart, registerables } from 'chart.js';
import {
    ITEMS_PER_PAGE,
    MOCK_DATA,
    addDays,
    formatIdDate,
    formatShortDate,
    formatMonthId,
    buildDateOptions,
    getDateMode,
} from './shared';

Chart.register(...registerables);

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const toUiStatus = (status) => {
    const value = String(status || '').toLowerCase();
    if (value === 'success') return 'Success';
    if (value === 'failed') return 'Failed';
    if (value === 'running') return 'Running';
    if (value === 'pending') return 'Pending';
    return status || '-';
};

const buildCompactPages = (current, total) => {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

    const pages = new Set([1, total, current - 1, current, current + 1]);
    if (current <= 3) {
        pages.add(2);
        pages.add(3);
    }
    if (current >= total - 2) {
        pages.add(total - 1);
        pages.add(total - 2);
    }

    const sorted = Array.from(pages)
        .filter((p) => p >= 1 && p <= total)
        .sort((a, b) => a - b);

    const compact = [];
    for (let i = 0; i < sorted.length; i += 1) {
        const page = sorted[i];
        const prev = sorted[i - 1];
        if (i > 0 && page - prev > 1) compact.push('...');
        compact.push(page);
    }
    return compact;
};

const initLstmOverview = () => {
    const root = document.querySelector('[data-page="admin-lstm-overview"]');
    if (!root) return;

    const baseDate = new Date().toISOString().slice(0, 10);
    const state = {
        activeTab: 'overview',
        controlRange: '30hari',
        controlDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        tableRange: '24jam',
        tableDate: { single: 'all', start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        evalRange: '24jam',
        evalDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        evalRunDate: { single: 'all', start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        evalRun: null,
        logRange: '24jam',
        logDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        logRunDate: { single: 'all', start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        logRun: null,
        logOpenSteps: new Set(),
        tableRows: [...MOCK_DATA],
        activeRunId: 1,
        runDetailCache: {},
        page: 1,
        deleteId: null,
        isRunning: false,
        toastTimer: null,
        statusPollTimer: null,
        statusSessionActive: false,
        statusRunId: null,
        runRequestedAtMs: null,
        runDateDefaultInitialized: false,
    };

    const tabButtons = root.querySelectorAll('[data-lstm-tab]');
    const sections = root.querySelectorAll('[data-lstm-section]');
    const tableRangeButtons = root.querySelectorAll('[data-table-range]');
    const evalRangeButtons = root.querySelectorAll('[data-eval-range]');
    const logRangeButtons = root.querySelectorAll('[data-log-range]');
    const startButton = root.querySelector('[data-start-prediction]');
    const stopButton = root.querySelector('[data-stop-prediction]');
    const logButton = root.querySelector('[data-go-log]');
    const resultButton = root.querySelector('[data-see-result]');
    const tableBody = root.querySelector('[data-lstm-table-body]');
    const pagination = root.querySelector('[data-lstm-pagination]');
    const activeRunLabel = root.querySelector('[data-active-run-label]');
    const statusDateText = root.querySelector('[data-status-date]');
    const statusPill = root.querySelector('[data-status-pill]');
    const statusStepText = root.querySelector('[data-status-step]');

    const evalRunToggle = root.querySelector('[data-eval-run-toggle]');
    const evalRunLabel = root.querySelector('[data-eval-run-label]');
    const evalRunMenu = root.querySelector('[data-eval-run-menu]');
    const logRunToggle = root.querySelector('[data-log-run-toggle]');
    const logRunLabel = root.querySelector('[data-log-run-label]');
    const logRunMenu = root.querySelector('[data-log-run-menu]');
    const logStepsContainer = root.querySelector('[data-log-steps]');

    const evalPm10Mae = root.querySelector('[data-eval-pm10-mae]');
    const evalPm10Mse = root.querySelector('[data-eval-pm10-mse]');
    const evalPm10Rmse = root.querySelector('[data-eval-pm10-rmse]');
    const evalPm10R2 = root.querySelector('[data-eval-pm10-r2]');
    const evalPm25Mae = root.querySelector('[data-eval-pm25-mae]');
    const evalPm25Mse = root.querySelector('[data-eval-pm25-mse]');
    const evalPm25Rmse = root.querySelector('[data-eval-pm25-rmse]');
    const evalPm25R2 = root.querySelector('[data-eval-pm25-r2]');
    const evalWindowInfo = root.querySelector('[data-eval-window-info]');

    const deleteModal = root.querySelector('[data-lstm-delete-modal]');
    const deleteName = root.querySelector('[data-lstm-delete-name]');
    const deleteCancel = root.querySelector('[data-lstm-delete-cancel]');
    const deleteConfirm = root.querySelector('[data-lstm-delete-confirm]');

    const pm10Canvas = document.getElementById('lstm-eval-pm10-chart');
    const pm25Canvas = document.getElementById('lstm-eval-pm25-chart');

    if (
        !tabButtons.length || !sections.length ||
        !evalRangeButtons.length || !startButton || !stopButton || !logButton || !resultButton || !tableBody || !pagination || !activeRunLabel ||
        !evalRunToggle || !evalRunLabel || !evalRunMenu ||
        !logRunToggle || !logRunLabel || !logRunMenu || !logStepsContainer ||
        !evalPm10Mae || !evalPm10Mse || !evalPm10Rmse || !evalPm10R2 ||
        !evalPm25Mae || !evalPm25Mse || !evalPm25Rmse || !evalPm25R2 || !evalWindowInfo ||
        !deleteModal || !deleteName || !deleteCancel || !deleteConfirm || !statusDateText || !statusPill || !statusStepText ||
        !(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)
    ) return;

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
            throw new Error(payload?.message || 'Terjadi kesalahan.');
        }
        return payload;
    };

    const extractRunDate = (waktuEksekusi) => {
        if (!waktuEksekusi || waktuEksekusi === '-') return null;
        const datePart = String(waktuEksekusi).split(' ')[0] || '';
        const [dd, mm, yyyy] = datePart.split('-');
        if (!dd || !mm || !yyyy) return null;
        return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
    };

    const formatDateTimeId = (value) => {
        if (!value) return '-';
        const dt = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(dt.getTime())) return value;
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(dt);
    };

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

    const numericAverage = (arr) => {
        const values = (Array.isArray(arr) ? arr : []).filter((v) => Number.isFinite(Number(v))).map((v) => Number(v));
        if (values.length === 0) return null;
        return values.reduce((sum, v) => sum + v, 0) / values.length;
    };

    const toast = document.createElement('div');
    toast.className = 'fixed right-4 top-4 z-[70] hidden min-w-[280px] rounded-xl border px-4 py-3 shadow-lg';
    toast.innerHTML = '<p class="text-sm font-medium" data-toast-text></p>';
    document.body.appendChild(toast);
    const toastText = toast.querySelector('[data-toast-text]');

    const showToast = (type, message) => {
        if (!(toastText instanceof HTMLElement)) return;
        if (state.toastTimer) clearTimeout(state.toastTimer);
        toast.classList.remove('hidden', 'border-primary-300/40', 'bg-primary-50', 'border-danger-300/40', 'bg-danger-50');
        toastText.classList.remove('text-primary-400', 'text-danger-300');

        if (type === 'success') {
            toast.classList.add('border-primary-300/40', 'bg-primary-50');
            toastText.classList.add('text-primary-400');
        } else {
            toast.classList.add('border-danger-300/40', 'bg-danger-50');
            toastText.classList.add('text-danger-300');
        }
        toastText.textContent = message;
        state.toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            state.toastTimer = null;
        }, 3000);
    };

    const estimateRunningProgress = () => {
        if (!state.runRequestedAtMs) {
            return { step: 'Preprocessing', progress: 5 };
        }

        const elapsedSec = Math.max(0, Math.floor((Date.now() - state.runRequestedAtMs) / 1000));
        if (elapsedSec < 3) return { step: 'Preprocessing', progress: Math.min(15, 5 + (elapsedSec * 3)) };
        if (elapsedSec < 6) return { step: 'Scaling', progress: Math.min(25, 16 + ((elapsedSec - 3) * 3)) };
        if (elapsedSec < 10) return { step: 'Windowing', progress: Math.min(35, 26 + ((elapsedSec - 6) * 2)) };
        if (elapsedSec < 30) return { step: 'Training', progress: Math.min(75, 36 + ((elapsedSec - 10) * 2)) };
        if (elapsedSec < 40) return { step: 'Generate', progress: Math.min(90, 76 + ((elapsedSec - 30) * 2)) };
        return { step: 'Evaluasi', progress: Math.min(99, 91 + (elapsedSec - 40)) };
    };

    const setStatusDisplay = (status, dateText) => {
        if (!state.statusSessionActive) {
            statusDateText.textContent = '';
            statusPill.textContent = '';
            statusStepText.textContent = '';
            statusStepText.classList.add('hidden');
            stopButton.classList.add('hidden');
            stopButton.classList.remove('inline-flex');
            stopButton.disabled = true;
            statusPill.classList.remove('bg-primary-100', 'text-ispu-baik', 'bg-danger-50', 'text-ispu-sangat-tidak-sehat', 'bg-warning-100', 'bg-warning-100/50', 'text-warning-300', 'bg-surface-200', 'text-surface-300');
            statusPill.classList.add('bg-surface-200', 'text-surface-300');
            return;
        }

        statusDateText.textContent = dateText || '';
        statusStepText.textContent = '';
        statusStepText.classList.add('hidden');
        statusPill.classList.remove('bg-primary-100', 'text-ispu-baik', 'bg-danger-50', 'text-ispu-sangat-tidak-sehat', 'bg-warning-100', 'bg-warning-100/50', 'text-warning-300', 'bg-surface-200', 'text-surface-300');
        if (status === 'running') {
            const current = estimateRunningProgress();
            statusPill.textContent = `Memproses ${current.progress}%`;
            statusPill.classList.add('bg-warning-100/50', 'text-warning-300');
            statusStepText.textContent = `Tahap: ${current.step}`;
            statusStepText.classList.remove('hidden');
            return;
        }
        stopButton.classList.add('hidden');
        stopButton.classList.remove('inline-flex');
        stopButton.disabled = true;
        if (status === 'success') {
            statusPill.textContent = 'Sukses';
            statusPill.classList.add('bg-primary-100', 'text-ispu-baik');
            return;
        }
        if (status === 'failed') {
            statusPill.textContent = 'Gagal';
            statusPill.classList.add('bg-danger-50', 'text-ispu-sangat-tidak-sehat');
            return;
        }
        statusPill.textContent = '';
        statusPill.classList.add('bg-surface-200', 'text-surface-300');
    };

    const setStartButtonState = () => {
        if (state.isRunning) {
            startButton.disabled = true;
            startButton.classList.add('opacity-80', 'cursor-not-allowed');
            startButton.innerHTML = '<i class="ph ph-circle-notch animate-spin text-xl"></i>Memprediksi...';
            stopButton.classList.remove('hidden');
            stopButton.classList.add('inline-flex');
            stopButton.disabled = false;
            stopButton.classList.remove('opacity-70', 'cursor-not-allowed');
            return;
        }
        startButton.disabled = false;
        startButton.classList.remove('opacity-80', 'cursor-not-allowed');
        startButton.innerHTML = '<i class="ph ph-rocket-launch text-xl"></i>Mulai Prediksi';
        stopButton.classList.add('hidden');
        stopButton.classList.remove('inline-flex');
        stopButton.disabled = true;
        stopButton.classList.add('opacity-70', 'cursor-not-allowed');
    };

    const buildEvalChart = (ctx, color, maxTicks = 12) => new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Aktual', data: [], borderColor: color, backgroundColor: color, borderDash: [6, 4], pointRadius: 2.5, pointHoverRadius: 4, borderWidth: 2, tension: 0.25 },
                { label: 'Prediksi', data: [], borderColor: color, backgroundColor: color, pointRadius: 0, pointHoverRadius: 4, borderWidth: 2, tension: 0 },
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
                            return state.evalRange === '24jam' ? `Jam ${label}` : `Tanggal ${label}`;
                        },
                        label: (item) => `${item.dataset.label}: ${item.parsed.y} ug/m3`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: maxTicks },
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

    const pm10Chart = buildEvalChart(pm10Canvas, '#2563EB');
    const pm25Chart = buildEvalChart(pm25Canvas, '#16A34A');

    const getDateStateByScope = (scope) => {
        if (scope === 'control') return state.controlDate;
        if (scope === 'table') return state.tableDate;
        if (scope === 'evalrun') return state.evalRunDate;
        if (scope === 'logrun') return state.logRunDate;
        if (scope === 'log') return state.logDate;
        return state.evalDate;
    };

    const getRangeByScope = (scope) => {
        if (scope === 'control') return state.controlRange;
        if (scope === 'table') return state.tableRange;
        if (scope === 'evalrun') return '24jam';
        if (scope === 'logrun') return '24jam';
        if (scope === 'log') return state.logRange;
        return state.evalRange;
    };

    const getScopeLabel = (scope) => {
        const range = getRangeByScope(scope);
        const ds = getDateStateByScope(scope);
        const mode = getDateMode(range);
        if (mode === 'range') return `${formatShortDate(ds.start)} - ${formatShortDate(ds.end)}`;
        if (mode === 'month') return formatMonthId(ds.month);
        return formatIdDate(ds.single);
    };

    const openDeleteModal = (row) => {
        state.deleteId = row.id;
        deleteName.textContent = ` #${row.id}`;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    };

    const closeDeleteModal = () => {
        state.deleteId = null;
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    };

    const renderTabs = () => {
        tabButtons.forEach((button) => {
            const tab = button.getAttribute('data-lstm-tab');
            const active = tab === state.activeTab;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-surface-300', !active);
            button.classList.toggle('hover:text-primary-300', !active);
        });

        sections.forEach((section) => {
            const key = section.getAttribute('data-lstm-section');
            section.classList.toggle('hidden', key !== state.activeTab);
        });
    };

    const renderRanges = () => {
        tableRangeButtons.forEach((button) => {
            const value = button.getAttribute('data-table-range');
            const active = value === state.tableRange;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-surface-300', !active);
            button.classList.toggle('hover:text-primary-300', !active);
        });

        evalRangeButtons.forEach((button) => {
            const value = button.getAttribute('data-eval-range');
            const active = value === state.evalRange;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-surface-300', !active);
            button.classList.toggle('hover:text-primary-300', !active);
        });

        logRangeButtons.forEach((button) => {
            const value = button.getAttribute('data-log-range');
            const active = value === state.logRange;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-surface-300', !active);
            button.classList.toggle('hover:text-primary-300', !active);
        });
    };

    const syncDefaultControlDateFromDataset = async () => {
        try {
            const payload = await api('/admin/api/actual-data');
            const dates = Array.isArray(payload?.data?.dates) ? payload.data.dates.filter(Boolean) : [];
            if (!dates.length) return;

            const lastDate = String([...dates].sort()[dates.length - 1]);
            const [year, month] = lastDate.split('-').map(Number);
            if (!year || !month) return;

            const nextMonthYear = month === 12 ? year + 1 : year;
            const nextMonthValue = month === 12 ? 1 : month + 1;
            const nextMonthDate = `${nextMonthYear}-${String(nextMonthValue).padStart(2, '0')}-01`;

            state.controlDate.single = nextMonthDate;
            state.controlDate.start = nextMonthDate;
            state.controlDate.end = addDays(nextMonthDate, 6);
            state.controlDate.month = nextMonthDate.slice(0, 7);
        } catch (_) {}
    };

    const syncRuns = async ({ syncStatus = true } = {}) => {
        const payload = await api('/admin/api/lstm-runs');
        const rows = Array.isArray(payload?.data?.runs) ? payload.data.runs : [];
        state.tableRows = rows.map((row) => ({
            id: Number(row.id),
            waktuEksekusi: row.waktu_eksekusi ?? '-',
            tanggalPrediksi: row.tanggal_prediksi ?? '-',
            status: toUiStatus(row.status),
        }));
        const availableRunDates = [...new Set(state.tableRows.map((row) => extractRunDate(row.waktuEksekusi)).filter(Boolean))];
        if (availableRunDates.length > 0 && state.tableDate.single !== 'all' && !availableRunDates.includes(state.tableDate.single)) {
            state.tableDate.single = availableRunDates[0];
            renderTableDate?.();
        }
        state.activeRunId = Number(payload?.data?.active_run_id) || null;
        if (syncStatus && state.statusSessionActive) {
            let statusSource = null;
            if (state.statusRunId) {
                statusSource = rows.find((row) => Number(row.id) === Number(state.statusRunId)) || null;
            }
            if (!statusSource) {
                statusSource = rows[0] || null;
            }
            const startedAt = statusSource?.waktu_eksekusi || '';
            const statusRaw = String(statusSource?.status || '').toLowerCase();
            state.isRunning = statusRaw === 'running';
            setStatusDisplay(statusRaw, startedAt);
            setStartButtonState();
        }

        const fallbackRunId = state.activeRunId || rows[0]?.id || null;
        if (!state.evalRun || !rows.find((row) => Number(row.id) === Number(state.evalRun))) {
            state.evalRun = fallbackRunId;
        }
        if (!state.logRun || !rows.find((row) => Number(row.id) === Number(state.logRun))) {
            state.logRun = fallbackRunId;
        }

        const selectedEvalRow = rows.find((row) => Number(row.id) === Number(state.evalRun)) || null;
        const selectedLogRow = rows.find((row) => Number(row.id) === Number(state.logRun)) || null;
        const selectedEvalDate = extractRunDate(selectedEvalRow?.waktu_eksekusi || '');
        const selectedLogDate = extractRunDate(selectedLogRow?.waktu_eksekusi || '');

        if (rows.length === 0) {
            state.tableDate.single = 'all';
            state.evalRunDate.single = 'all';
            state.logRunDate.single = 'all';
            renderTableDate?.();
            renderEvalRunDate?.();
            renderLogRunDate?.();
        }

        if (!state.runDateDefaultInitialized) {
            if (rows.length === 0) {
                state.tableDate.single = 'all';
                state.evalRunDate.single = 'all';
                state.logRunDate.single = 'all';
            } else {
                const selectedTableRow = rows.find((row) => Number(row.id) === Number(state.activeRunId || state.evalRun || state.logRun)) || rows[0];
                const selectedTableDate = extractRunDate(selectedTableRow?.waktu_eksekusi || '');
                state.tableDate.single = selectedTableDate || 'all';
                state.evalRunDate.single = selectedEvalDate || 'all';
                state.logRunDate.single = selectedLogDate || 'all';
            }
            state.runDateDefaultInitialized = true;
            renderTableDate?.();
            renderEvalRunDate?.();
            renderLogRunDate?.();
        }

        if (selectedEvalDate && state.evalRunDate.single !== 'all') {
            state.evalRunDate.single = selectedEvalDate;
            renderEvalRunDate?.();
        }
        if (selectedLogDate && state.logRunDate.single !== 'all') {
            state.logRunDate.single = selectedLogDate;
            renderLogRunDate?.();
        }

        renderRunMenus();
    };

    const formatMetricValue = (value) => {
        if (value === null || value === undefined || Number.isNaN(Number(value))) return '-';
        return Number(value).toFixed(4);
    };

    const resolveApiDateParams = (scope) => {
        const range = getRangeByScope(scope);
        const ds = getDateStateByScope(scope);
        const params = new URLSearchParams();
        params.set('range', range);
        const mode = getDateMode(range);
        if (mode === 'single') params.set('date_single', ds.single);
        if (mode === 'range') {
            params.set('date_start', ds.start);
            params.set('date_end', ds.end);
        }
        if (mode === 'month') params.set('date_month', ds.month);
        return params.toString();
    };

    const getRunDetail = async (runId, scope) => {
        if (!runId) return null;
        const query = resolveApiDateParams(scope);
        const cacheKey = `${runId}:${scope}:${query}`;
        if (state.runDetailCache[cacheKey]) return state.runDetailCache[cacheKey];
        const payload = await api(`/admin/api/lstm-runs/${runId}?${query}`);
        const data = payload?.data ?? null;
        state.runDetailCache[cacheKey] = data;
        return data;
    };

    const renderRunMenus = () => {
        const evalFilteredRuns = state.tableRows.filter((run) => {
            if (state.evalRunDate.single === 'all') return true;
            const runDate = extractRunDate(run.waktuEksekusi);
            return !!runDate && runDate === state.evalRunDate.single;
        });
        const logFilteredRuns = state.tableRows.filter((run) => {
            if (state.logRunDate.single === 'all') return true;
            const runDate = extractRunDate(run.waktuEksekusi);
            return !!runDate && runDate === state.logRunDate.single;
        });

        const runButtons = state.tableRows.map((run) => {
            const activeEval = Number(state.evalRun) === Number(run.id);
            const activeLog = Number(state.logRun) === Number(run.id);
            const label = `${run.id} - ${run.waktuEksekusi}`;
            return { run, activeEval, activeLog, label };
        });
        const evalButtons = evalFilteredRuns.map((run) => {
            const activeEval = Number(state.evalRun) === Number(run.id);
            const label = `${run.id} - ${run.waktuEksekusi}`;
            return { run, activeEval, label };
        });
        const logButtons = logFilteredRuns.map((run) => {
            const activeLog = Number(state.logRun) === Number(run.id);
            const label = `${run.id} - ${run.waktuEksekusi}`;
            return { run, activeLog, label };
        });

        if (runButtons.length === 0) {
            evalRunLabel.textContent = 'Belum ada run';
            logRunLabel.textContent = 'Belum ada run';
            evalRunMenu.innerHTML = '<div class="px-3 py-2 text-sm text-surface-300">Belum ada run.</div>';
            logRunMenu.innerHTML = '<div class="px-3 py-2 text-sm text-surface-300">Belum ada run.</div>';
            return;
        }

        if (evalButtons.length === 0) {
            state.evalRun = null;
            evalRunLabel.textContent = 'Tidak ada run di tanggal ini';
            evalRunMenu.innerHTML = '<div class="px-3 py-2 text-sm text-surface-300">Tidak ada run di tanggal ini.</div>';
        } else {
            if (!evalButtons.some((item) => Number(item.run.id) === Number(state.evalRun))) {
                state.evalRun = evalButtons[0].run.id;
            }
            const selectedEval = evalButtons.find((item) => Number(item.run.id) === Number(state.evalRun)) || evalButtons[0];
            evalRunLabel.textContent = selectedEval.label;
            evalRunMenu.innerHTML = evalButtons.map(({ run, activeEval, label }) => `
                <button type="button" data-eval-run-option="${run.id}" class="block w-full rounded-lg px-3 py-2 text-left text-sm ${activeEval ? 'bg-primary-50 text-primary-300' : 'text-surface-300 hover:bg-surface-200'}">${label}</button>
            `).join('');
        }

        if (logButtons.length === 0) {
            state.logRun = null;
            logRunLabel.textContent = 'Tidak ada run di tanggal ini';
            logRunMenu.innerHTML = '<div class="px-3 py-2 text-sm text-surface-300">Tidak ada run di tanggal ini.</div>';
        } else {
            if (!logButtons.some((item) => Number(item.run.id) === Number(state.logRun))) {
                state.logRun = logButtons[0].run.id;
            }
            const selectedLog = logButtons.find((item) => Number(item.run.id) === Number(state.logRun)) || logButtons[0];
            logRunLabel.textContent = selectedLog.label;
            logRunMenu.innerHTML = logButtons.map(({ run, activeLog, label }) => `
                <button type="button" data-log-run-option="${run.id}" class="block w-full rounded-lg px-3 py-2 text-left text-sm ${activeLog ? 'bg-primary-50 text-primary-300' : 'text-surface-300 hover:bg-surface-200'}">${label}</button>
            `).join('');
        }
    };

    const stopStatusPolling = () => {
        if (state.statusPollTimer) {
            clearInterval(state.statusPollTimer);
            state.statusPollTimer = null;
        }
    };

    const startStatusPolling = () => {
        stopStatusPolling();
        state.statusPollTimer = setInterval(async () => {
            try {
                await syncRuns({ syncStatus: true });
                renderTable();
                const tracked = state.statusRunId
                    ? state.tableRows.find((row) => Number(row.id) === Number(state.statusRunId))
                    : state.tableRows[0];
                const isStillRunning = !!tracked && tracked.status === 'Running';
                state.isRunning = isStillRunning;
                setStartButtonState();
                if (!isStillRunning) {
                    stopStatusPolling();
                }
            } catch (_) {}
        }, 3000);
    };

    const getCurrentRunningRow = () => {
        if (state.statusRunId) {
            const tracked = state.tableRows.find((row) => Number(row.id) === Number(state.statusRunId));
            if (tracked && tracked.status === 'Running') return tracked;
        }
        return state.tableRows.find((row) => row.status === 'Running') || null;
    };

    const renderTable = () => {
        const renderActiveRunInfo = () => {
            const active = state.tableRows.find((row) => row.id === state.activeRunId);
            if (!active) {
                activeRunLabel.textContent = 'Belum ada run aktif';
                return;
            }
            activeRunLabel.textContent = `#${active.id} - ${active.waktuEksekusi}`;
        };

        const filteredRows = state.tableDate.single === 'all'
            ? state.tableRows
            : state.tableRows.filter((row) => {
                const runDate = extractRunDate(row.waktuEksekusi);
                if (!runDate) return false;
                return runDate === state.tableDate.single;
            });

        const totalPages = Math.max(1, Math.ceil(filteredRows.length / ITEMS_PER_PAGE));
        state.page = Math.min(state.page, totalPages);
        const start = (state.page - 1) * ITEMS_PER_PAGE;
        const pageRows = filteredRows.slice(start, start + ITEMS_PER_PAGE);

        if (!pageRows.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="border-b-2 border-l-2 border-r-2 border-surface-200 px-4 py-8 text-center text-base text-surface-300">${
                        state.tableDate.single === 'all'
                            ? 'Belum ada output prediksi.'
                            : 'Tidak ada output prediksi pada tanggal ini.'
                    }</td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageRows.map((row) => `
                <tr>
                    <td class="border-b border-l border-r-0 border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${row.waktuEksekusi}</td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${row.tanggalPrediksi}</td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base">
                        <div class="flex flex-col gap-1">
                            <span class="${row.status === 'Success' ? 'text-ispu-baik' : row.status === 'Running' ? 'text-warning-300' : 'text-ispu-sangat-tidak-sehat'}">${row.status}</span>
                            ${row.id === state.activeRunId ? '<span class="inline-flex w-fit rounded-full bg-primary-300 px-2 py-0.5 text-xs text-surface-50">Digunakan</span>' : ''}
                        </div>
                    </td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5">
                        ${
                            row.status === 'Failed'
                                ? '<span class="text-sm text-surface-300">-</span>'
                                : `<button type="button" data-view="${row.id}" class="inline-flex items-center gap-1.5 text-base text-surface-300 transition-colors hover:text-primary-300">
                                    <i class="ph ph-eye text-xl text-primary-300"></i>
                                    Lihat
                                </button>`
                        }
                    </td>
                    <td class="border-b border-r border-surface-200 bg-primary-50 px-4 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                data-use="${row.id}"
                                class="rounded-full px-3 py-1 text-xs font-semibold transition-colors ${
                                    row.id === state.activeRunId
                                        ? 'cursor-default bg-primary-100 text-primary-300'
                                        : row.status !== 'Success'
                                            ? 'cursor-not-allowed bg-surface-200 text-surface-300'
                                            : 'bg-primary-300 text-surface-50 hover:bg-primary-400'
                                }"
                                ${row.id === state.activeRunId || row.status !== 'Success' ? 'disabled' : ''}
                            >
                                ${row.id === state.activeRunId ? 'Digunakan' : 'Gunakan'}
                            </button>
                            <button type="button" data-delete="${row.id}" class="inline-flex items-center gap-2 text-ispu-sangat-tidak-sehat transition-opacity hover:opacity-80">
                                <span class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-ispu-sangat-tidak-sehat text-surface-50">
                                    <i class="ph ph-trash text-base"></i>
                                </span>
                                <span class="text-base underline">Hapus</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        renderActiveRunInfo();

        tableBody.querySelectorAll('[data-use]').forEach((button) => {
            button.addEventListener('click', async () => {
                const id = Number(button.getAttribute('data-use'));
                if (Number.isNaN(id) || id === state.activeRunId) return;
                try {
                    await api(`/admin/api/lstm-runs/${id}/activate`, { method: 'POST' });
                    state.activeRunId = id;
                    renderTable();
                    renderRunMenus();
                    showToast('success', 'Run aktif berhasil diperbarui.');
                } catch (error) {
                    showToast('error', error.message);
                }
            });
        });

        tableBody.querySelectorAll('[data-view]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-view'));
                if (!Number.isNaN(id)) {
                    const row = state.tableRows.find((item) => Number(item.id) === id);
                    const runDate = extractRunDate(row?.waktuEksekusi || '');
                    state.evalRun = id;
                    state.logRun = id;
                    if (runDate) {
                        state.evalRunDate.single = runDate;
                        state.logRunDate.single = runDate;
                        renderEvalRunDate?.();
                        renderLogRunDate?.();
                    }
                    renderRunMenus();
                    renderEvaluation();
                    renderLogSteps();
                }
                state.activeTab = 'evaluation';
                renderTabs();
            });
        });

        tableBody.querySelectorAll('[data-delete]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-delete'));
                const row = state.tableRows.find((item) => item.id === id);
                if (!row) return;
                openDeleteModal(row);
            });
        });

        const compactPages = buildCompactPages(state.page, totalPages);
        const pages = [];
        pages.push(`<button type="button" data-page="${state.page - 1}" ${state.page <= 1 ? 'disabled' : ''} class="rounded-[10px] border border-surface-200 bg-primary-50 px-4 py-1.5 text-base font-bold text-surface-300 transition-colors hover:bg-surface-200 disabled:opacity-50">Prev</button>`);
        compactPages.forEach((token) => {
            if (token === '...') {
                pages.push('<button type="button" data-page="-1" disabled class="h-[30px] w-[36px] cursor-default rounded-[10px] border border-surface-200 bg-primary-50 text-base font-bold text-surface-300 disabled:opacity-50">...</button>');
                return;
            }
            pages.push(`<button type="button" data-page="${token}" class="h-[30px] w-[30px] rounded-[10px] border border-surface-200 text-base font-bold ${token === state.page ? 'bg-primary-300 text-surface-50' : 'bg-primary-50 text-surface-300 hover:bg-surface-200'}">${token}</button>`);
        });
        pages.push(`<button type="button" data-page="${state.page + 1}" ${state.page >= totalPages ? 'disabled' : ''} class="rounded-[10px] border border-surface-200 bg-primary-50 px-4 py-1.5 text-base font-bold text-surface-300 transition-colors hover:bg-surface-200 disabled:opacity-50">Next</button>`);
        pagination.innerHTML = pages.join('');

        pagination.querySelectorAll('[data-page]').forEach((button) => {
            button.addEventListener('click', () => {
                if (button.hasAttribute('disabled')) return;
                const nextPage = Number(button.getAttribute('data-page'));
                if (Number.isNaN(nextPage)) return;
                state.page = nextPage;
                renderTable();
            });
        });
    };

    const renderEvaluation = async () => {
        if (!state.evalRun) {
            evalPm10Mae.textContent = '-';
            evalPm10Mse.textContent = '-';
            evalPm10Rmse.textContent = '-';
            evalPm10R2.textContent = '-';
            evalPm25Mae.textContent = '-';
            evalPm25Mse.textContent = '-';
            evalPm25Rmse.textContent = '-';
            evalPm25R2.textContent = '-';
            pm10Chart.data.labels = [];
            pm10Chart.data.datasets[0].data = [];
            pm10Chart.data.datasets[1].data = [];
            pm10Chart.update();
            pm25Chart.data.labels = [];
            pm25Chart.data.datasets[0].data = [];
            pm25Chart.data.datasets[1].data = [];
            pm25Chart.update();
            evalWindowInfo.textContent = 'Rentang data: -';
            return;
        }
        try {
            const data = await getRunDetail(state.evalRun, 'evaluation');
            const metricPm10 = data?.metrics?.pm10 ?? null;
            const metricPm25 = data?.metrics?.pm25 ?? null;
            const chart = data?.chart ?? null;

            evalPm10Mae.textContent = formatMetricValue(metricPm10?.mae);
            evalPm10Mse.textContent = formatMetricValue(metricPm10?.mse);
            evalPm10Rmse.textContent = formatMetricValue(metricPm10?.rmse);
            evalPm10R2.textContent = formatMetricValue(metricPm10?.r2);

            evalPm25Mae.textContent = formatMetricValue(metricPm25?.mae);
            evalPm25Mse.textContent = formatMetricValue(metricPm25?.mse);
            evalPm25Rmse.textContent = formatMetricValue(metricPm25?.rmse);
            evalPm25R2.textContent = formatMetricValue(metricPm25?.r2);

            pm10Chart.data.labels = chart?.labels || [];
            pm10Chart.data.datasets[0].data = chart?.pm10?.actual || [];
            pm10Chart.data.datasets[1].data = chart?.pm10?.predicted || [];
            {
                const pm10Avg = numericAverage(chart?.pm10?.predicted) ?? numericAverage(chart?.pm10?.actual) ?? 0;
                const pm10Color = resolveHealthColor(pm10Avg);
                pm10Chart.data.datasets[0].borderColor = pm10Color;
                pm10Chart.data.datasets[0].backgroundColor = pm10Color;
                pm10Chart.data.datasets[1].borderColor = pm10Color;
                pm10Chart.data.datasets[1].backgroundColor = pm10Color;
            }
            pm10Chart.update();

            pm25Chart.data.labels = chart?.labels || [];
            pm25Chart.data.datasets[0].data = chart?.pm25?.actual || [];
            pm25Chart.data.datasets[1].data = chart?.pm25?.predicted || [];
            {
                const pm25Avg = numericAverage(chart?.pm25?.predicted) ?? numericAverage(chart?.pm25?.actual) ?? 0;
                const pm25Color = resolveHealthColor(pm25Avg);
                pm25Chart.data.datasets[0].borderColor = pm25Color;
                pm25Chart.data.datasets[0].backgroundColor = pm25Color;
                pm25Chart.data.datasets[1].borderColor = pm25Color;
                pm25Chart.data.datasets[1].backgroundColor = pm25Color;
            }
            pm25Chart.update();

            const windowStart = data?.window?.start ?? null;
            const windowEnd = data?.window?.end ?? null;
            evalWindowInfo.textContent = `Rentang data: ${formatDateTimeId(windowStart)} - ${formatDateTimeId(windowEnd)}`;
        } catch (_) {}
    };

    const renderLogSteps = async () => {
        if (!state.logRun) {
            logStepsContainer.innerHTML = '<div class="rounded-lg border border-surface-200 bg-surface-100 p-4 text-sm text-surface-300">Tidak ada run di tanggal ini.</div>';
            return;
        }
        try {
            const data = await getRunDetail(state.logRun, 'log');
            const steps = Array.isArray(data?.steps) ? data.steps : [];
            if (steps.length === 0) {
                logStepsContainer.innerHTML = '<div class="rounded-lg border border-surface-200 bg-surface-100 p-4 text-sm text-surface-300">Detail proses belum tersedia.</div>';
                return;
            }

            if (state.logOpenSteps.size === 0) {
                steps.forEach((step) => state.logOpenSteps.add(Number(step.step_order)));
            }

            const titleMap = {
                preprocessing: 'Preprocessing',
                scaling: 'Scaling',
                windowing: 'Windowing',
                training: 'Training',
                generate: 'Generate',
                evaluation: 'Evaluasi',
            };
            const statusStyleMap = {
                success: 'bg-ispu-baik text-surface-50',
                failed: 'bg-ispu-sangat-tidak-sehat text-surface-50',
                running: 'bg-warning-100 text-warning-300',
                pending: 'bg-surface-200 text-surface-300',
            };

            logStepsContainer.innerHTML = steps.map((step, idx) => {
                const stepNo = Number(step.step_order);
                const isOpen = state.logOpenSteps.has(stepNo);
                const statusRaw = String(step.status || 'pending').toLowerCase();
                const statusLabel = statusRaw.charAt(0).toUpperCase() + statusRaw.slice(1);
                const statusClass = statusStyleMap[statusRaw] || statusStyleMap.pending;
                const summary = step.summary && typeof step.summary === 'object' ? step.summary : {};
                const summaryRows = Object.entries(summary).map(([key, value]) => `
                    <div class="rounded-[12px] border border-surface-200 bg-primary-50 px-3 py-2">
                        <p class="text-xs font-semibold text-surface-300">${String(key).replaceAll('_', ' ')}</p>
                        <p class="text-sm text-surface-300">${Array.isArray(value) ? value.join(' / ') : (value ?? '-')}</p>
                    </div>
                `).join('');

                return `
                    <div class="flex gap-2 sm:gap-4">
                        <div class="flex flex-col items-center pt-3">
                            <span class="mb-1 w-6 text-right text-base font-bold leading-none text-surface-400 sm:text-lg">${stepNo}.</span>
                            <div class="flex flex-col items-center">
                                <i class="ph-fill ph-check-circle text-4xl ${statusRaw === 'success' ? 'text-ispu-baik' : statusRaw === 'failed' ? 'text-ispu-sangat-tidak-sehat' : 'text-warning-300'}"></i>
                                ${idx === steps.length - 1 ? '' : '<div class="mt-1 w-px min-h-[24px] flex-1 bg-surface-200"></div>'}
                            </div>
                        </div>
                        <div class="min-w-0 flex-1 pb-4">
                            <button type="button" data-log-step-toggle="${stepNo}" class="flex w-full items-center justify-between rounded-t-[15px] border border-surface-200 bg-primary-50 px-4 py-3 text-left">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-base font-bold text-surface-400 sm:text-lg">${stepNo}. ${titleMap[step.step_key] || step.step_key || 'Step'}</span>
                                    <span class="rounded-full px-4 py-1 text-sm ${statusClass}">${statusLabel}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base text-surface-400 sm:text-lg">${Number(step.duration_seconds || 0)}s</span>
                                    <i class="ph ${isOpen ? 'ph-caret-up' : 'ph-caret-down'} text-base text-surface-400"></i>
                                </div>
                            </button>
                            ${isOpen ? `<div class="space-y-4 rounded-b-[15px] border-b border-l border-r border-surface-200 bg-surface-100 p-4 sm:p-6">${summaryRows || '<p class="text-sm text-surface-300">Tidak ada ringkasan step.</p>'}</div>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        } catch (_) {}
    };

    const setupDatePicker = (scope, onAfterChange = null) => {
        const toggle = root.querySelector(`[data-date-toggle="${scope}"]`);
        const label = root.querySelector(`[data-date-label="${scope}"]`);
        const menu = root.querySelector(`[data-date-menu="${scope}"]`);
        const current = root.querySelector(`[data-date-current="${scope}"]`);
        const prev = root.querySelector(`[data-date-prev="${scope}"]`);
        const next = root.querySelector(`[data-date-next="${scope}"]`);
        const list = root.querySelector(`[data-date-list="${scope}"]`);
        const singleInput = root.querySelector(`[data-date-single-input="${scope}"]`);
        const singleApply = root.querySelector(`[data-date-single-apply="${scope}"]`);
        const singleAll = root.querySelector(`[data-date-single-all="${scope}"]`);
        const modeSingle = root.querySelector(`[data-date-mode="single"][data-date-scope="${scope}"]`);
        const modeRange = root.querySelector(`[data-date-mode="range"][data-date-scope="${scope}"]`);
        const modeMonth = root.querySelector(`[data-date-mode="month"][data-date-scope="${scope}"]`);
        const rangeStart = root.querySelector(`[data-date-range-start="${scope}"]`);
        const rangeEnd = root.querySelector(`[data-date-range-end="${scope}"]`);
        const rangeApply = root.querySelector(`[data-date-range-apply="${scope}"]`);
        const monthInput = root.querySelector(`[data-date-month="${scope}"]`);
        const monthApply = root.querySelector(`[data-date-month-apply="${scope}"]`);

        const hasSingleCalendar = !!(singleInput && singleApply);
        const hasSingleList = !!(current && prev && next && list);
        if (!toggle || !label || !menu || !modeSingle || (!hasSingleCalendar && !hasSingleList)) return null;

        const render = () => {
            const range = getRangeByScope(scope);
            const mode = getDateMode(range);
            const ds = getDateStateByScope(scope);

            modeSingle.classList.toggle('hidden', mode !== 'single');
            if (modeRange) modeRange.classList.toggle('hidden', mode !== 'range');
            if (modeMonth) modeMonth.classList.toggle('hidden', mode !== 'month');

            if ((scope === 'table' || scope === 'evalrun' || scope === 'logrun') && ds.single === 'all') {
                label.textContent = 'Semua Data';
            } else {
                label.textContent = getScopeLabel(scope);
            }

            if (mode === 'range') {
                if (!rangeStart || !rangeEnd) return;
                rangeStart.value = ds.start;
                rangeEnd.value = ds.end;
                return;
            }

            if (mode === 'month') {
                if (!monthInput) return;
                monthInput.value = ds.month;
                return;
            }

            if (singleInput && singleApply) {
                singleInput.value = ds.single === 'all' ? '' : ds.single;
                return;
            }

            current.textContent = formatIdDate(ds.single);
            const options = buildDateOptions(ds.single, 20);
            const idx = options.indexOf(ds.single);
            prev.disabled = idx <= 0;
            next.disabled = idx < 0 || idx >= options.length - 1;

            list.innerHTML = options.map((dateStr) => {
                const active = dateStr === ds.single;
                return `<button type="button" data-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatShortDate(dateStr)}</button>`;
            }).join('');

            list.querySelectorAll('[data-date-value]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const value = btn.getAttribute('data-date-value');
                    if (!value) return;
                    ds.single = value;
                    menu.classList.add('hidden');
                    render();
                    onAfterChange?.();
                });
            });
        };

        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        if (prev && next && current && list) {
            prev.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                const options = buildDateOptions(ds.single, 20);
                const idx = options.indexOf(ds.single);
                if (idx > 0) {
                    ds.single = options[idx - 1];
                    render();
                    onAfterChange?.();
                }
            });

            next.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                const options = buildDateOptions(ds.single, 20);
                const idx = options.indexOf(ds.single);
                if (idx >= 0 && idx < options.length - 1) {
                    ds.single = options[idx + 1];
                    render();
                    onAfterChange?.();
                }
            });
        }

        if (singleInput && singleApply) {
            singleApply.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                if (!singleInput.value) return;
                ds.single = singleInput.value;
                menu.classList.add('hidden');
                render();
                onAfterChange?.();
            });
        }

        if (singleAll) {
            singleAll.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                ds.single = 'all';
                menu.classList.add('hidden');
                render();
                onAfterChange?.();
            });
        }

        if (rangeApply && rangeStart && rangeEnd) {
            rangeApply.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                if (!rangeStart.value || !rangeEnd.value) return;
                ds.start = rangeStart.value;
                ds.end = rangeEnd.value;
                menu.classList.add('hidden');
                render();
                onAfterChange?.();
            });
        }

        if (monthApply && monthInput) {
            monthApply.addEventListener('click', () => {
                const ds = getDateStateByScope(scope);
                if (!monthInput.value) return;
                ds.month = monthInput.value;
                menu.classList.add('hidden');
                render();
                onAfterChange?.();
            });
        }

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        return render;
    };

    const renderControlDate = setupDatePicker('control');
    const renderTableDate = setupDatePicker('table', () => {
        state.page = 1;
        renderTable();
    });
    const renderEvalRunDate = setupDatePicker('evalrun', () => {
        renderRunMenus();
        renderEvaluation();
    });
    const renderLogRunDate = setupDatePicker('logrun', () => {
        renderRunMenus();
        renderLogSteps();
    });

    const navigateToRunTab = (target) => {
        const preferredRunId = state.statusRunId || state.activeRunId || state.tableRows[0]?.id || null;
        const targetRow = preferredRunId
            ? state.tableRows.find((row) => Number(row.id) === Number(preferredRunId))
            : (state.tableRows[0] || null);
        const targetRunId = targetRow?.id || null;
        const targetRunDate = extractRunDate(targetRow?.waktuEksekusi || '');

        if (target === 'evaluation') {
            state.evalRun = targetRunId;
            if (targetRunDate) state.evalRunDate.single = targetRunDate;
            renderEvalRunDate?.();
            renderRunMenus();
            renderEvaluation();
            state.activeTab = 'evaluation';
            renderTabs();
            return;
        }

        state.logRun = targetRunId;
        if (targetRunDate) state.logRunDate.single = targetRunDate;
        renderLogRunDate?.();
        renderRunMenus();
        renderLogSteps();
        state.activeTab = 'log';
        renderTabs();
    };

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-lstm-tab');
            if (!tab) return;
            state.activeTab = tab;
            renderTabs();
            if (tab === 'overview') renderTable();
            if (tab === 'evaluation') renderEvaluation();
            if (tab === 'log') renderLogSteps();
        });
    });

    tableRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-table-range');
            if (!value) return;
            state.tableRange = value;
            state.page = 1;
            renderRanges();
            renderTableDate?.();
            renderTable();
        });
    });

    evalRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-eval-range');
            if (!value) return;
            state.evalRange = value;
            renderRanges();
            renderEvaluation();
        });
    });

    logRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-log-range');
            if (!value) return;
            state.logRange = value;
            renderRanges();
            renderLogSteps();
        });
    });

    evalRunToggle.addEventListener('click', () => {
        evalRunMenu.classList.toggle('hidden');
    });

    evalRunMenu.addEventListener('click', (event) => {
        const button = event.target.closest('[data-eval-run-option]');
        if (!button) return;
        const id = Number(button.getAttribute('data-eval-run-option'));
        if (!id) return;
        state.evalRun = id;
        renderRunMenus();
        evalRunMenu.classList.add('hidden');
        renderEvaluation();
    });

    logRunToggle.addEventListener('click', () => {
        logRunMenu.classList.toggle('hidden');
    });

    logRunMenu.addEventListener('click', (event) => {
        const button = event.target.closest('[data-log-run-option]');
        if (!button) return;
        const id = Number(button.getAttribute('data-log-run-option'));
        if (!id) return;
        state.logRun = id;
        renderRunMenus();
        logRunMenu.classList.add('hidden');
        renderLogSteps();
    });

    document.addEventListener('click', (event) => {
        if (!evalRunMenu.contains(event.target) && !evalRunToggle.contains(event.target)) {
            evalRunMenu.classList.add('hidden');
        }
        if (!logRunMenu.contains(event.target) && !logRunToggle.contains(event.target)) {
            logRunMenu.classList.add('hidden');
        }
    });

    logStepsContainer.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;

        const toggle = event.target.closest('[data-log-step-toggle]');
        if (toggle) {
            const stepNo = Number(toggle.getAttribute('data-log-step-toggle'));
            if (!Number.isNaN(stepNo)) {
                if (state.logOpenSteps.has(stepNo)) state.logOpenSteps.delete(stepNo);
                else state.logOpenSteps.add(stepNo);
                renderLogSteps();
            }
            return;
        }

        const link = event.target.closest('[data-log-link]');
        if (link) {
            window.alert('Detail data hasil generate akan dihubungkan saat backend siap.');
            return;
        }

        const action = event.target.closest('[data-log-action]');
        if (action) {
            state.activeTab = 'evaluation';
            renderTabs();
        }
    });

    startButton.addEventListener('click', async () => {
        if (state.isRunning) return;

        const payload = {
            range: '30hari',
            date_month: state.controlDate.month,
        };

        state.isRunning = true;
        state.statusSessionActive = true;
        state.statusRunId = null;
        state.runRequestedAtMs = Date.now();
        setStartButtonState();
        setStatusDisplay('running', new Date().toLocaleString('id-ID'));
        startStatusPolling();

        try {
            await api('/admin/api/lstm-runs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            await syncRuns({ syncStatus: false });
            renderTable();
            const latest = state.tableRows[0] || null;
            state.statusRunId = latest?.id || null;
            state.isRunning = !!latest && latest.status === 'Running';
            if (!state.isRunning && latest) {
                setStatusDisplay(String(latest.status || '').toLowerCase(), latest.waktuEksekusi);
            }
            setStartButtonState();
            showToast('success', 'Prediksi masuk antrean dan sedang diproses.');
        } catch (error) {
            setStatusDisplay('failed', new Date().toLocaleString('id-ID'));
            state.isRunning = false;
            setStartButtonState();
            stopStatusPolling();
            showToast('error', error.message);
        }
    });

    stopButton.addEventListener('click', async () => {
        if (!state.isRunning) return;
        const runningRow = getCurrentRunningRow();
        const targetRunId = runningRow?.id || state.statusRunId;
        if (!targetRunId) {
            showToast('error', 'Run yang sedang berjalan tidak ditemukan.');
            return;
        }

        stopButton.disabled = true;
        stopButton.classList.add('opacity-70', 'cursor-not-allowed');
        try {
            await api(`/admin/api/lstm-runs/${targetRunId}/stop`, { method: 'POST' });
            state.isRunning = false;
            state.statusRunId = targetRunId;
            setStatusDisplay('failed', new Date().toLocaleString('id-ID'));
            setStartButtonState();
            stopStatusPolling();
            await syncRuns({ syncStatus: true });
            renderTable();
            showToast('success', 'Run berhasil dihentikan.');
        } catch (error) {
            stopButton.disabled = false;
            stopButton.classList.remove('opacity-70', 'cursor-not-allowed');
            showToast('error', error.message);
        }
    });

    logButton.addEventListener('click', () => {
        navigateToRunTab('log');
    });

    resultButton.addEventListener('click', () => {
        navigateToRunTab('evaluation');
    });

    deleteCancel.addEventListener('click', closeDeleteModal);
    deleteConfirm.addEventListener('click', async () => {
        if (!state.deleteId) return;
        try {
            await api(`/admin/api/lstm-runs/${state.deleteId}`, { method: 'DELETE' });
            state.tableRows = state.tableRows.filter((row) => row.id !== state.deleteId);
            if (state.activeRunId === state.deleteId) {
                state.activeRunId = state.tableRows[0]?.id ?? null;
            }
            if (state.evalRun === state.deleteId) {
                state.evalRun = state.tableRows[0]?.id ?? null;
            }
            if (state.logRun === state.deleteId) {
                state.logRun = state.tableRows[0]?.id ?? null;
            }
            state.page = 1;
            renderTable();
            renderRunMenus();
            renderEvaluation();
            renderLogSteps();
            closeDeleteModal();
            showToast('success', 'Run berhasil dihapus.');
        } catch (error) {
            showToast('error', error.message);
        }
    });

    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) closeDeleteModal();
    });

    window.addEventListener('beforeunload', stopStatusPolling);

    setStartButtonState();
    setStatusDisplay('pending', '');
    syncDefaultControlDateFromDataset()
        .finally(() => {
            renderTabs();
            renderRanges();
            renderControlDate?.();
            renderTableDate?.();
            renderEvalRunDate?.();
            renderLogRunDate?.();
            renderTable();
            renderEvaluation();
            renderLogSteps();
            syncRuns({ syncStatus: false })
                .then(() => {
                    renderTable();
                    renderEvaluation();
                    renderLogSteps();
                })
                .catch(() => {});
        });
};

document.addEventListener('DOMContentLoaded', initLstmOverview);
