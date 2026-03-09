import { Chart, registerables } from 'chart.js';
import {
    ITEMS_PER_PAGE,
    MOCK_DATA,
    EVAL_RUNS,
    LOG_STEPS,
    addDays,
    formatIdDate,
    formatShortDate,
    formatMonthId,
    buildDateOptions,
    getDateMode,
    getEvalChartData,
} from './shared';

Chart.register(...registerables);

const initLstmOverview = () => {
    const root = document.querySelector('[data-page="admin-lstm-overview"]');
    if (!root) return;

    const baseDate = '2026-01-01';
    const state = {
        activeTab: 'overview',
        controlRange: '24jam',
        controlDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        tableRange: '24jam',
        tableDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        evalRange: '24jam',
        evalDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        evalRun: 'run-1',
        logRange: '24jam',
        logDate: { single: baseDate, start: addDays(baseDate, -6), end: baseDate, month: baseDate.slice(0, 7) },
        logRun: 'run-1',
        logOpenSteps: new Set(LOG_STEPS.map((step) => step.number)),
        tableRows: [...MOCK_DATA],
        activeRunId: 1,
        page: 1,
        deleteId: null,
    };

    const tabButtons = root.querySelectorAll('[data-lstm-tab]');
    const sections = root.querySelectorAll('[data-lstm-section]');
    const controlRangeButtons = root.querySelectorAll('[data-control-range]');
    const tableRangeButtons = root.querySelectorAll('[data-table-range]');
    const evalRangeButtons = root.querySelectorAll('[data-eval-range]');
    const logRangeButtons = root.querySelectorAll('[data-log-range]');
    const startButton = root.querySelector('[data-start-prediction]');
    const logButton = root.querySelector('[data-go-log]');
    const resultButton = root.querySelector('[data-see-result]');
    const tableBody = root.querySelector('[data-lstm-table-body]');
    const pagination = root.querySelector('[data-lstm-pagination]');
    const activeRunLabel = root.querySelector('[data-active-run-label]');

    const evalRunToggle = root.querySelector('[data-eval-run-toggle]');
    const evalRunLabel = root.querySelector('[data-eval-run-label]');
    const evalRunMenu = root.querySelector('[data-eval-run-menu]');
    const evalRunOptions = root.querySelectorAll('[data-eval-run-option]');
    const logRunToggle = root.querySelector('[data-log-run-toggle]');
    const logRunLabel = root.querySelector('[data-log-run-label]');
    const logRunMenu = root.querySelector('[data-log-run-menu]');
    const logRunOptions = root.querySelectorAll('[data-log-run-option]');
    const logStepsContainer = root.querySelector('[data-log-steps]');

    const evalPm10Mae = root.querySelector('[data-eval-pm10-mae]');
    const evalPm10Mse = root.querySelector('[data-eval-pm10-mse]');
    const evalPm10Rmse = root.querySelector('[data-eval-pm10-rmse]');
    const evalPm10R2 = root.querySelector('[data-eval-pm10-r2]');
    const evalPm25Mae = root.querySelector('[data-eval-pm25-mae]');
    const evalPm25Mse = root.querySelector('[data-eval-pm25-mse]');
    const evalPm25Rmse = root.querySelector('[data-eval-pm25-rmse]');
    const evalPm25R2 = root.querySelector('[data-eval-pm25-r2]');

    const deleteModal = root.querySelector('[data-lstm-delete-modal]');
    const deleteName = root.querySelector('[data-lstm-delete-name]');
    const deleteCancel = root.querySelector('[data-lstm-delete-cancel]');
    const deleteConfirm = root.querySelector('[data-lstm-delete-confirm]');

    const pm10Canvas = document.getElementById('lstm-eval-pm10-chart');
    const pm25Canvas = document.getElementById('lstm-eval-pm25-chart');

    if (
        !tabButtons.length || !sections.length || !controlRangeButtons.length || !tableRangeButtons.length ||
        !evalRangeButtons.length || !logRangeButtons.length || !startButton || !logButton || !resultButton || !tableBody || !pagination || !activeRunLabel ||
        !evalRunToggle || !evalRunLabel || !evalRunMenu || !evalRunOptions.length ||
        !logRunToggle || !logRunLabel || !logRunMenu || !logRunOptions.length || !logStepsContainer ||
        !evalPm10Mae || !evalPm10Mse || !evalPm10Rmse || !evalPm10R2 ||
        !evalPm25Mae || !evalPm25Mse || !evalPm25Rmse || !evalPm25R2 ||
        !deleteModal || !deleteName || !deleteCancel || !deleteConfirm ||
        !(pm10Canvas instanceof HTMLCanvasElement) || !(pm25Canvas instanceof HTMLCanvasElement)
    ) return;

    const buildEvalChart = (ctx, color, maxTicks = 12) => new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Aktual', data: [], borderColor: color, backgroundColor: color, borderDash: [6, 4], pointRadius: 4, pointHoverRadius: 5, borderWidth: 2, tension: 0 },
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
                        title: (items) => `Index ${items[0]?.label}`,
                        label: (item) => `${item.dataset.label}: ${item.parsed.y}`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#64748B', maxRotation: 0, autoSkip: true, maxTicksLimit: maxTicks },
                    grid: { color: '#E2E8F0', drawOnChartArea: false },
                    border: { color: '#CBD5E1' },
                },
                y: {
                    ticks: { color: '#64748B' },
                    grid: { color: '#E2E8F0' },
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
        if (scope === 'log') return state.logDate;
        return state.evalDate;
    };

    const getRangeByScope = (scope) => {
        if (scope === 'control') return state.controlRange;
        if (scope === 'table') return state.tableRange;
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
        controlRangeButtons.forEach((button) => {
            const value = button.getAttribute('data-control-range');
            const active = value === state.controlRange;
            button.classList.toggle('bg-primary-300', active);
            button.classList.toggle('text-surface-50', active);
            button.classList.toggle('text-surface-300', !active);
            button.classList.toggle('hover:text-primary-300', !active);
        });

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

    const renderTable = () => {
        const renderActiveRunInfo = () => {
            const active = state.tableRows.find((row) => row.id === state.activeRunId);
            if (!active) {
                activeRunLabel.textContent = 'Belum ada run aktif';
                return;
            }
            activeRunLabel.textContent = `#${active.id} - ${active.waktuEksekusi}`;
        };

        const totalPages = Math.max(1, Math.ceil(state.tableRows.length / ITEMS_PER_PAGE));
        state.page = Math.min(state.page, totalPages);
        const start = (state.page - 1) * ITEMS_PER_PAGE;
        const pageRows = state.tableRows.slice(start, start + ITEMS_PER_PAGE);

        if (!pageRows.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="border-b-2 border-l-2 border-r-2 border-surface-200 px-4 py-8 text-center text-base text-surface-300">Belum ada output prediksi.</td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageRows.map((row) => `
                <tr>
                    <td class="border-b border-l border-r-0 border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${row.waktuEksekusi}</td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${row.tanggalPrediksi}</td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base">
                        <div class="flex flex-col gap-1">
                            <span class="${row.status === 'Success' ? 'text-ispu-baik' : 'text-ispu-sangat-tidak-sehat'}">${row.status}</span>
                            ${row.id === state.activeRunId ? '<span class="inline-flex w-fit rounded-full bg-primary-300 px-2 py-0.5 text-xs text-surface-50">Digunakan</span>' : ''}
                        </div>
                    </td>
                    <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5">
                        <button type="button" data-view="${row.id}" class="inline-flex items-center gap-1.5 text-base text-surface-300 underline transition-colors hover:text-primary-300">
                            <i class="ph ph-file text-xl text-primary-300"></i>
                            Lihat
                        </button>
                    </td>
                    <td class="border-b border-r border-surface-200 bg-primary-50 px-4 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                data-use="${row.id}"
                                class="rounded-full px-3 py-1 text-xs font-semibold transition-colors ${row.id === state.activeRunId ? 'cursor-default bg-primary-100 text-primary-300' : 'bg-primary-300 text-surface-50 hover:bg-primary-400'}"
                                ${row.id === state.activeRunId ? 'disabled' : ''}
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
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-use'));
                if (Number.isNaN(id) || id === state.activeRunId) return;
                state.activeRunId = id;
                renderTable();
            });
        });

        tableBody.querySelectorAll('[data-view]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-view'));
                window.alert(`Lihat data prediksi #${id}`);
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

        const pages = [];
        pages.push(`<button type="button" data-page="${state.page - 1}" ${state.page <= 1 ? 'disabled' : ''} class="rounded-[10px] border border-surface-200 bg-primary-50 px-4 py-1.5 text-base font-bold text-surface-300 transition-colors hover:bg-surface-200 disabled:opacity-50">Prev</button>`);
        for (let page = 1; page <= totalPages; page += 1) {
            pages.push(`<button type="button" data-page="${page}" class="h-[30px] w-[30px] rounded-[10px] border border-surface-200 text-base font-bold ${page === state.page ? 'bg-primary-300 text-surface-50' : 'bg-primary-50 text-surface-300 hover:bg-surface-200'}">${page}</button>`);
        }
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

    const renderEvaluation = () => {
        const run = EVAL_RUNS[state.evalRun] || EVAL_RUNS['run-1'];
        evalRunLabel.textContent = run.label;

        evalPm10Mae.textContent = run.metrics.pm10.mae;
        evalPm10Mse.textContent = run.metrics.pm10.mse;
        evalPm10Rmse.textContent = run.metrics.pm10.rmse;
        evalPm10R2.textContent = run.metrics.pm10.r2;

        evalPm25Mae.textContent = run.metrics.pm25.mae;
        evalPm25Mse.textContent = run.metrics.pm25.mse;
        evalPm25Rmse.textContent = run.metrics.pm25.rmse;
        evalPm25R2.textContent = run.metrics.pm25.r2;

        const chartData = getEvalChartData(state.evalRange, state.evalRun);
        const maxTicks = state.evalRange === '30hari' ? 10 : 12;

        pm10Chart.data.labels = chartData.pm10.labels;
        pm10Chart.data.datasets[0].data = chartData.pm10.actual;
        pm10Chart.data.datasets[1].data = chartData.pm10.predicted;
        pm10Chart.options.scales.x.ticks.maxTicksLimit = maxTicks;
        pm10Chart.update();

        pm25Chart.data.labels = chartData.pm25.labels;
        pm25Chart.data.datasets[0].data = chartData.pm25.actual;
        pm25Chart.data.datasets[1].data = chartData.pm25.predicted;
        pm25Chart.options.scales.x.ticks.maxTicksLimit = maxTicks;
        pm25Chart.update();
    };

    const renderLogSteps = () => {
        const run = EVAL_RUNS[state.logRun] || EVAL_RUNS['run-1'];
        logRunLabel.textContent = run.label;

        logStepsContainer.innerHTML = LOG_STEPS.map((step, idx) => {
            const isOpen = state.logOpenSteps.has(step.number);
            const statCards = step.statCards?.length
                ? `<div class="grid gap-3 ${step.statCards.some((card) => card.wide) ? 'grid-cols-2' : step.statCards.length === 3 ? 'grid-cols-1 sm:grid-cols-3' : 'grid-cols-2'}">
                    ${step.statCards.map((card) => `
                        <div class="${card.wide ? 'col-span-2' : 'col-span-1'} rounded-[15px] border border-surface-200 bg-primary-50 p-4">
                            <p class="text-sm font-bold text-surface-300 sm:text-base">${card.label}</p>
                            <p class="text-sm font-normal sm:text-base ${card.valueClass || 'text-surface-300'}">${card.value}</p>
                        </div>
                    `).join('')}
                   </div>`
                : '';

            const notes = step.notes?.length
                ? `<div>
                    <p class="text-sm text-surface-300 sm:text-base">Keterangan:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        ${step.notes.map((note) => `<li class="text-sm text-surface-300 sm:text-base">${note}</li>`).join('')}
                    </ul>
                   </div>`
                : '';

            const note = step.note ? `<p class="text-sm text-surface-300 sm:text-base">${step.note}</p>` : '';
            const link = step.showLink
                ? `<div class="inline-flex items-center gap-1.5">
                    <i class="ph ph-file text-xl text-primary-300"></i>
                    <button type="button" data-log-link="${step.number}" class="text-sm text-primary-300 underline sm:text-base">Lihat</button>
                   </div>`
                : '';
            const action = step.actionLabel
                ? `<div class="inline-flex items-center gap-2">
                    <span class="text-sm text-surface-300 sm:text-base">Hasil evaluasi bisa dilihat di halaman ini</span>
                    <button type="button" data-log-action="${step.number}" class="rounded-full bg-primary-300 px-4 py-1 text-sm font-bold text-surface-50">${step.actionLabel}</button>
                   </div>`
                : '';

            return `
                <div class="flex gap-2 sm:gap-4">
                    <div class="flex flex-col items-center pt-3">
                        <span class="mb-1 w-6 text-right text-base font-bold leading-none text-surface-400 sm:text-lg">${step.number}.</span>
                        <div class="flex flex-col items-center">
                            <i class="ph-fill ph-check-circle text-4xl text-ispu-baik"></i>
                            ${idx === LOG_STEPS.length - 1 ? '' : '<div class="mt-1 w-px min-h-[24px] flex-1 bg-surface-200"></div>'}
                        </div>
                    </div>
                    <div class="min-w-0 flex-1 pb-4">
                        <button type="button" data-log-step-toggle="${step.number}" class="flex w-full items-center justify-between rounded-t-[15px] border border-surface-200 bg-primary-50 px-4 py-3 text-left">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-base font-bold text-surface-400 sm:text-lg">${step.number}. ${step.title}</span>
                                <span class="rounded-full bg-ispu-baik px-4 py-1 text-sm text-surface-50">Success</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-base text-surface-400 sm:text-lg">${step.duration}</span>
                                <i class="ph ${isOpen ? 'ph-caret-up' : 'ph-caret-down'} text-base text-surface-400"></i>
                            </div>
                        </button>
                        ${isOpen ? `<div class="space-y-4 rounded-b-[15px] border-b border-l border-r border-surface-200 bg-surface-100 p-4 sm:p-6">${statCards}${notes}${note}${link}${action}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    };

    const setupDatePicker = (scope, onAfterChange = null) => {
        const toggle = root.querySelector(`[data-date-toggle="${scope}"]`);
        const label = root.querySelector(`[data-date-label="${scope}"]`);
        const menu = root.querySelector(`[data-date-menu="${scope}"]`);
        const current = root.querySelector(`[data-date-current="${scope}"]`);
        const prev = root.querySelector(`[data-date-prev="${scope}"]`);
        const next = root.querySelector(`[data-date-next="${scope}"]`);
        const list = root.querySelector(`[data-date-list="${scope}"]`);
        const modeSingle = root.querySelector(`[data-date-mode="single"][data-date-scope="${scope}"]`);
        const modeRange = root.querySelector(`[data-date-mode="range"][data-date-scope="${scope}"]`);
        const modeMonth = root.querySelector(`[data-date-mode="month"][data-date-scope="${scope}"]`);
        const rangeStart = root.querySelector(`[data-date-range-start="${scope}"]`);
        const rangeEnd = root.querySelector(`[data-date-range-end="${scope}"]`);
        const rangeApply = root.querySelector(`[data-date-range-apply="${scope}"]`);
        const monthInput = root.querySelector(`[data-date-month="${scope}"]`);
        const monthApply = root.querySelector(`[data-date-month-apply="${scope}"]`);

        if (
            !toggle || !label || !menu || !current || !prev || !next || !list ||
            !modeSingle || !modeRange || !modeMonth || !rangeStart || !rangeEnd || !rangeApply || !monthInput || !monthApply
        ) return null;

        const render = () => {
            const range = getRangeByScope(scope);
            const mode = getDateMode(range);
            const ds = getDateStateByScope(scope);

            modeSingle.classList.toggle('hidden', mode !== 'single');
            modeRange.classList.toggle('hidden', mode !== 'range');
            modeMonth.classList.toggle('hidden', mode !== 'month');

            label.textContent = getScopeLabel(scope);

            if (mode === 'range') {
                rangeStart.value = ds.start;
                rangeEnd.value = ds.end;
                return;
            }

            if (mode === 'month') {
                monthInput.value = ds.month;
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

        rangeApply.addEventListener('click', () => {
            const ds = getDateStateByScope(scope);
            if (!rangeStart.value || !rangeEnd.value) return;
            ds.start = rangeStart.value;
            ds.end = rangeEnd.value;
            menu.classList.add('hidden');
            render();
            onAfterChange?.();
        });

        monthApply.addEventListener('click', () => {
            const ds = getDateStateByScope(scope);
            if (!monthInput.value) return;
            ds.month = monthInput.value;
            menu.classList.add('hidden');
            render();
            onAfterChange?.();
        });

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
    const renderEvalDate = setupDatePicker('evaluation', () => {
        renderEvaluation();
    });
    const renderLogDate = setupDatePicker('log', () => {
        renderLogSteps();
    });

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-lstm-tab');
            if (!tab) return;
            state.activeTab = tab;
            renderTabs();
        });
    });

    controlRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-control-range');
            if (!value) return;
            state.controlRange = value;
            renderRanges();
            renderControlDate?.();
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
            renderEvalDate?.();
            renderEvaluation();
        });
    });

    logRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-log-range');
            if (!value) return;
            state.logRange = value;
            renderRanges();
            renderLogDate?.();
            renderLogSteps();
        });
    });

    evalRunToggle.addEventListener('click', () => {
        evalRunMenu.classList.toggle('hidden');
    });

    evalRunOptions.forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-eval-run-option');
            if (!id) return;
            state.evalRun = id;
            evalRunMenu.classList.add('hidden');
            renderEvaluation();
        });
    });

    logRunToggle.addEventListener('click', () => {
        logRunMenu.classList.toggle('hidden');
    });

    logRunOptions.forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-log-run-option');
            if (!id) return;
            state.logRun = id;
            logRunMenu.classList.add('hidden');
            renderLogSteps();
        });
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

    startButton.addEventListener('click', () => {
        const range = state.controlRange;
        const ds = state.controlDate;
        let dateLabel = formatIdDate(ds.single);
        if (getDateMode(range) === 'range') dateLabel = `${formatShortDate(ds.start)} - ${formatShortDate(ds.end)}`;
        if (getDateMode(range) === 'month') dateLabel = formatMonthId(ds.month);
        window.alert(`Memulai prediksi: ${range} - ${dateLabel}`);
    });

    logButton.addEventListener('click', () => {
        state.activeTab = 'log';
        renderTabs();
    });

    resultButton.addEventListener('click', () => {
        window.alert('Navigasi ke hasil run prediksi akan disiapkan di tahap backend.');
    });

    deleteCancel.addEventListener('click', closeDeleteModal);
    deleteConfirm.addEventListener('click', () => {
        if (!state.deleteId) return;
        state.tableRows = state.tableRows.filter((row) => row.id !== state.deleteId);
        if (state.activeRunId === state.deleteId) {
            state.activeRunId = state.tableRows[0]?.id ?? null;
        }
        state.page = 1;
        renderTable();
        closeDeleteModal();
    });

    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) closeDeleteModal();
    });

    renderTabs();
    renderRanges();
    renderControlDate?.();
    renderTableDate?.();
    renderEvalDate?.();
    renderLogDate?.();
    renderTable();
    renderEvaluation();
    renderLogSteps();
};

document.addEventListener('DOMContentLoaded', initLstmOverview);
