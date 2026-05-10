import { formatIdDate } from '../shared';

const PAGE_SIZE = 12;

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

const initAdminPredictionData = () => {
    const root = document.querySelector('[data-page="admin-prediction-data"]');
    if (!root) return;

    const state = {
        range: '24 Jam',
        page: 1,
        date: '',
        rangeStart: '',
        rangeEnd: '',
        month: '',
        showDateMenu: false,
        availableDates: [],
        availableMonths: [],
        rows: [],
        hasActiveRun: false,
    };

    const rangeButtons = root.querySelectorAll('[data-range]');
    const dateToggle = root.querySelector('[data-prediction-date-toggle]');
    const dateMenu = root.querySelector('[data-prediction-date-menu]');
    const dateLabel = root.querySelector('[data-prediction-date-label]');
    const dateCurrent = root.querySelector('[data-prediction-date-current]');
    const datePrev = root.querySelector('[data-prediction-date-prev]');
    const dateNext = root.querySelector('[data-prediction-date-next]');
    const dateList = root.querySelector('[data-prediction-date-list]');
    const dateModeSingle = root.querySelector('[data-date-mode="single"]');
    const dateModeRange = root.querySelector('[data-date-mode="range"]');
    const dateModeMonth = root.querySelector('[data-date-mode="month"]');
    const rangeStartInput = root.querySelector('[data-prediction-range-start]');
    const rangePreview = root.querySelector('[data-prediction-range-preview]');
    const rangeApplyBtn = root.querySelector('[data-prediction-range-apply]');
    const monthInput = root.querySelector('[data-prediction-month]');
    const monthApplyBtn = root.querySelector('[data-prediction-month-apply]');
    const tableBody = root.querySelector('[data-prediction-table-body]');
    const pagination = root.querySelector('[data-prediction-pagination]');

    if (
        !dateToggle || !dateMenu || !dateLabel || !dateCurrent || !datePrev || !dateNext || !dateList ||
        !dateModeSingle || !dateModeRange || !dateModeMonth ||
        !rangeStartInput || !rangePreview || !rangeApplyBtn || !monthInput || !monthApplyBtn ||
        !tableBody || !pagination
    ) return;

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

    const renderRange = () => {
        rangeButtons.forEach((button) => {
            const isActive = button.getAttribute('data-range') === state.range;
            button.classList.toggle('bg-primary-300', isActive);
            button.classList.toggle('text-surface-50', isActive);
            button.classList.toggle('text-surface-300', !isActive);
            button.classList.toggle('hover:text-primary-300', !isActive);
        });
    };

    const renderDate = () => {
        const mode = getDateMode(state.range);
        dateModeSingle.classList.toggle('hidden', mode !== 'single');
        dateModeRange.classList.toggle('hidden', mode !== 'range');
        dateModeMonth.classList.toggle('hidden', mode !== 'month');

        if (mode === 'single') {
            dateLabel.textContent = state.date ? formatIdDate(state.date) : '-';
            dateCurrent.textContent = state.date ? formatIdDate(state.date) : '-';
            const options = state.availableDates;
            const currentIdx = options.indexOf(state.date);
            datePrev.disabled = currentIdx <= 0;
            dateNext.disabled = currentIdx < 0 || currentIdx >= options.length - 1;
            dateList.innerHTML = options.map((dateStr) => {
                const active = dateStr === state.date;
                return `<button type="button" data-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatIdDate(dateStr)}</button>`;
            }).join('');
            dateList.querySelectorAll('[data-date-value]').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const val = btn.getAttribute('data-date-value');
                    if (!val) return;
                    state.date = val;
                    state.page = 1;
                    state.showDateMenu = false;
                    dateMenu.classList.add('hidden');
                    renderDate();
                    await loadRows();
                });
            });
            return;
        }

        if (mode === 'range') {
            dateLabel.textContent = state.rangeStart && state.rangeEnd ? `${formatIdDate(state.rangeStart)} - ${formatIdDate(state.rangeEnd)}` : '-';
            rangeStartInput.value = state.rangeStart || '';
            rangePreview.textContent = state.rangeStart && state.rangeEnd
                ? `${formatIdDate(state.rangeStart)} s.d ${formatIdDate(state.rangeEnd)}`
                : '-';
            datePrev.disabled = true;
            dateNext.disabled = true;
            dateList.innerHTML = '';
            return;
        }

        dateLabel.textContent = state.month ? formatIdDate(`${state.month}-01`).replace(/^\d+\s/, '') : '-';
        if (state.availableMonths.length) {
            monthInput.min = state.availableMonths[0];
            monthInput.max = state.availableMonths[state.availableMonths.length - 1];
        }
        monthInput.value = state.month || '';
        datePrev.disabled = true;
        dateNext.disabled = true;
        dateList.innerHTML = '';
    };

    const renderTable = () => {
        const filteredRows = state.rows;

        const totalPages = Math.max(1, Math.ceil(filteredRows.length / PAGE_SIZE));
        state.page = Math.min(state.page, totalPages);
        const start = (state.page - 1) * PAGE_SIZE;
        const pageRows = filteredRows.slice(start, start + PAGE_SIZE);

        if (!state.hasActiveRun) {
            tableBody.innerHTML = `
                <tr class="border-x border-b border-surface-200">
                    <td colspan="4" class="rounded-b-[15px] px-5 py-8 text-center text-base text-surface-300">
                        Belum ada run aktif. Pilih hasil run sukses di halaman LSTM terlebih dahulu.
                    </td>
                </tr>
            `;
            pagination.innerHTML = '';
            return;
        }

        if (!pageRows.length) {
            tableBody.innerHTML = `
                <tr class="border-x border-b border-surface-200">
                    <td colspan="4" class="rounded-b-[15px] px-5 py-8 text-center text-base text-surface-300">
                        Tidak ada data untuk filter ini.
                    </td>
                </tr>
            `;
            pagination.innerHTML = '';
            return;
        }

        tableBody.innerHTML = pageRows.map((row, idx) => `
            <tr class="border-x border-b border-surface-200">
                <td class="${idx === pageRows.length - 1 ? 'rounded-bl-[15px]' : ''} px-5 py-4 text-base font-normal text-surface-300">${row.jam || '-'}</td>
                <td class="px-5 py-4 text-base font-normal text-surface-300">${row.pm10 ?? '-'}</td>
                <td class="px-5 py-4 text-base font-normal text-surface-300">${row.pm25 ?? '-'}</td>
                <td class="${idx === pageRows.length - 1 ? 'rounded-br-[15px]' : ''} px-5 py-4 text-base font-normal text-surface-300">${row.indikator || '-'}</td>
            </tr>
        `).join('');

        const buttonTpl = (label, page, disabled, active = false, isDots = false) => `
            <button type="button" data-page="${page}" ${disabled ? 'disabled' : ''}
                class="flex h-[30px] items-center justify-center rounded-[10px] border border-surface-200 text-base font-bold transition-colors ${
                    active ? 'w-[30px] bg-primary-300 text-surface-50' : 'bg-primary-50 text-surface-300 hover:bg-surface-200'
                } ${label === 'Prev' || label === 'Next' ? 'w-[62px]' : (isDots ? 'w-[36px]' : 'w-[30px]')} disabled:opacity-40 ${isDots ? 'cursor-default hover:bg-primary-50' : ''}">${label}</button>`;

        const pageButtons = [buttonTpl('Prev', state.page - 1, state.page === 1)];
        const compactPages = buildCompactPages(state.page, totalPages);
        compactPages.forEach((token) => {
            if (token === '...') {
                pageButtons.push(buttonTpl('...', -1, true, false, true));
                return;
            }
            pageButtons.push(buttonTpl(String(token), token, false, token === state.page));
        });
        pageButtons.push(buttonTpl('Next', state.page + 1, state.page === totalPages));
        pagination.innerHTML = pageButtons.join('');

        pagination.querySelectorAll('[data-page]').forEach((button) => {
            button.addEventListener('click', () => {
                if (button.hasAttribute('disabled')) return;
                const next = Number.parseInt(button.getAttribute('data-page') || '1', 10);
                if (!Number.isNaN(next) && next > 0) {
                    state.page = next;
                    renderTable();
                }
            });
        });
    };

    const loadMeta = async () => {
        const payload = await api('/admin/api/prediction/meta');
        const data = payload?.data || {};
        state.hasActiveRun = !!data.active_run;
        state.availableDates = Array.isArray(data.dates) ? data.dates : [];
        state.availableMonths = Array.isArray(data.months) ? data.months : [];
        state.date = data?.defaults?.single || state.availableDates[0] || '';
        state.rangeStart = data?.defaults?.start || state.date;
        state.rangeEnd = data?.defaults?.end || state.date;
        state.month = data?.defaults?.month || state.availableMonths[0] || '';
    };

    const loadRows = async () => {
        if (!state.hasActiveRun) {
            state.rows = [];
            renderTable();
            return;
        }
        const payload = await api(`/admin/api/prediction/data?${buildQuery()}`);
        state.rows = Array.isArray(payload?.data?.rows) ? payload.data.rows : [];
        const applied = payload?.data?.applied || {};
        if (applied.date_single) state.date = applied.date_single;
        if (applied.date_start) state.rangeStart = applied.date_start;
        if (applied.date_end) state.rangeEnd = applied.date_end;
        if (applied.date_month) state.month = applied.date_month;
        renderDate();
        renderTable();
    };

    rangeButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            state.range = button.getAttribute('data-range') || '24 Jam';
            state.page = 1;
            renderRange();
            renderDate();
            await loadRows();
        });
    });

    dateToggle.addEventListener('click', () => {
        state.showDateMenu = !state.showDateMenu;
        dateMenu.classList.toggle('hidden', !state.showDateMenu);
    });

    datePrev.addEventListener('click', async () => {
        if (getDateMode(state.range) !== 'single') return;
        const idx = state.availableDates.indexOf(state.date);
        if (idx > 0) {
            state.date = state.availableDates[idx - 1];
            state.page = 1;
            renderDate();
            await loadRows();
        }
    });

    dateNext.addEventListener('click', async () => {
        if (getDateMode(state.range) !== 'single') return;
        const idx = state.availableDates.indexOf(state.date);
        if (idx >= 0 && idx < state.availableDates.length - 1) {
            state.date = state.availableDates[idx + 1];
            state.page = 1;
            renderDate();
            await loadRows();
        }
    });

    rangeApplyBtn.addEventListener('click', async () => {
        if (!rangeStartInput.value) return;
        state.rangeStart = rangeStartInput.value;
        const end = new Date(`${state.rangeStart}T00:00:00`);
        end.setDate(end.getDate() + 6);
        state.rangeEnd = end.toISOString().slice(0, 10);
        state.page = 1;
        state.showDateMenu = false;
        dateMenu.classList.add('hidden');
        renderDate();
        await loadRows();
    });

    monthApplyBtn.addEventListener('click', async () => {
        if (!monthInput.value) return;
        if (state.availableMonths.length && !state.availableMonths.includes(monthInput.value)) return;
        state.month = monthInput.value;
        state.page = 1;
        state.showDateMenu = false;
        dateMenu.classList.add('hidden');
        renderDate();
        await loadRows();
    });

    document.addEventListener('click', (event) => {
        if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
            state.showDateMenu = false;
            dateMenu.classList.add('hidden');
        }
    });

    (async () => {
        try {
            await loadMeta();
            renderRange();
            renderDate();
            await loadRows();
        } catch (error) {
            tableBody.innerHTML = `
                <tr class="border-x border-b border-surface-200">
                    <td colspan="4" class="rounded-b-[15px] px-5 py-8 text-center text-base text-ispu-sangat-tidak-sehat">${error.message}</td>
                </tr>
            `;
            pagination.innerHTML = '';
        }
    })();
};

document.addEventListener('DOMContentLoaded', initAdminPredictionData);
