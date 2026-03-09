import { DATA_BY_RANGE, formatIdDate } from '../shared';

const PAGE_SIZE = 12;

const addDays = (isoDate, days) => {
    const date = new Date(`${isoDate}T00:00:00`);
    date.setDate(date.getDate() + days);
    return date.toISOString().slice(0, 10);
};

const formatShortDate = (isoDate) => {
    try {
        const date = new Date(`${isoDate}T00:00:00`);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return isoDate;
    }
};

const buildDateOptions = (range, anchor) => {
    const count = range === '30 Hari' ? 30 : range === '7 Hari' ? 7 : 1;
    return Array.from({ length: count }, (_, idx) => addDays(anchor, -idx));
};

const initAdminPredictionData = () => {
    const root = document.querySelector('[data-page="admin-prediction-data"]');
    if (!root) return;

    const state = {
        range: '24 Jam',
        page: 1,
        category: 'Semua',
        date: '2026-01-01',
        rangeStart: '2026-01-01',
        rangeEnd: '2026-01-07',
        month: '2026-01',
        showDateMenu: false,
        showCategoryMenu: false,
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
    const rangeEndInput = root.querySelector('[data-prediction-range-end]');
    const rangeApplyBtn = root.querySelector('[data-prediction-range-apply]');
    const monthInput = root.querySelector('[data-prediction-month]');
    const monthApplyBtn = root.querySelector('[data-prediction-month-apply]');
    const categoryToggle = root.querySelector('[data-prediction-category-toggle]');
    const categoryLabel = root.querySelector('[data-prediction-category-label]');
    const categoryMenu = root.querySelector('[data-prediction-category-menu]');
    const categoryOptions = root.querySelectorAll('[data-prediction-category-option]');
    const tableBody = root.querySelector('[data-prediction-table-body]');
    const pagination = root.querySelector('[data-prediction-pagination]');

    if (
        !dateToggle || !dateMenu || !dateLabel || !dateCurrent || !datePrev || !dateNext || !dateList ||
        !dateModeSingle || !dateModeRange || !dateModeMonth ||
        !rangeStartInput || !rangeEndInput || !rangeApplyBtn || !monthInput || !monthApplyBtn ||
        !categoryToggle || !categoryLabel || !categoryMenu || !categoryOptions.length ||
        !tableBody || !pagination
    ) return;

    const getDateMode = () => {
        if (state.range === '7 Hari') return 'range';
        if (state.range === '30 Hari') return 'month';
        return 'single';
    };

    const getDateOptions = () => buildDateOptions(state.range, state.date);

    const getRows = () => DATA_BY_RANGE[state.range] || [];

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
        const mode = getDateMode();

        dateModeSingle.classList.toggle('hidden', mode !== 'single');
        dateModeRange.classList.toggle('hidden', mode !== 'range');
        dateModeMonth.classList.toggle('hidden', mode !== 'month');

        if (mode === 'single') {
            dateLabel.textContent = formatIdDate(state.date);
            dateCurrent.textContent = formatIdDate(state.date);
        } else if (mode === 'range') {
            dateLabel.textContent = `${formatShortDate(state.rangeStart)} - ${formatShortDate(state.rangeEnd)}`;
            rangeStartInput.value = state.rangeStart;
            rangeEndInput.value = state.rangeEnd;
        } else {
            const monthDate = `${state.month}-01`;
            dateLabel.textContent = formatIdDate(monthDate).replace(/^\d+\s/, '');
            monthInput.value = state.month;
        }

        const options = getDateOptions();
        const currentIdx = options.indexOf(state.date);
        datePrev.disabled = currentIdx <= 0;
        dateNext.disabled = currentIdx < 0 || currentIdx >= options.length - 1;

        dateList.innerHTML = options.map((dateStr) => {
            const active = dateStr === state.date;
            return `<button type="button" data-date-value="${dateStr}" class="w-full rounded-lg px-3 py-1.5 text-left text-sm transition-colors ${active ? 'bg-primary-300 text-surface-50' : 'text-surface-300 hover:bg-surface-200'}">${formatShortDate(dateStr)}</button>`;
        }).join('');

        dateList.querySelectorAll('[data-date-value]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const val = btn.getAttribute('data-date-value');
                if (!val) return;
                state.date = val;
                state.showDateMenu = false;
                dateMenu.classList.add('hidden');
                renderDate();
                renderTable();
            });
        });
    };

    const renderTable = () => {
        const rows = getRows();
        const totalPages = Math.max(1, Math.ceil(rows.length / PAGE_SIZE));
        state.page = Math.min(state.page, totalPages);
        const start = (state.page - 1) * PAGE_SIZE;
        const pageRows = rows.slice(start, start + PAGE_SIZE);

        if (!pageRows.length) {
            tableBody.innerHTML = `
                <tr class="border-x border-b border-surface-200">
                    <td colspan="4" class="rounded-b-[15px] px-5 py-8 text-center text-base text-surface-300">
                        Tidak ada data untuk filter ini.
                    </td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageRows.map((row, idx) => `
                <tr class="border-x border-b border-surface-200">
                    <td class="${idx === pageRows.length - 1 ? 'rounded-bl-[15px]' : ''} px-5 py-4 text-base font-normal text-surface-300">${row.time}</td>
                    <td class="px-5 py-4 text-base font-normal text-surface-300">${state.category === 'PM2.5' ? '-' : row.pm10}</td>
                    <td class="px-5 py-4 text-base font-normal text-surface-300">${state.category === 'PM10' ? '-' : row.pm25}</td>
                    <td class="${idx === pageRows.length - 1 ? 'rounded-br-[15px]' : ''} px-5 py-4 text-base font-normal text-surface-300">${row.indicator}</td>
                </tr>
            `).join('');
        }

        const pageButtons = [];
        const buttonTpl = (label, page, disabled, active = false) => `
            <button type="button" data-page="${page}" ${disabled ? 'disabled' : ''}
                class="flex h-[30px] items-center justify-center rounded-[10px] border border-surface-200 text-base font-bold transition-colors ${
                    active ? 'w-[30px] bg-primary-300 text-surface-50' : 'bg-primary-50 text-surface-300 hover:bg-surface-200'
                } ${label === 'Prev' || label === 'Next' ? 'w-[62px]' : 'w-[30px]'} disabled:opacity-40">${label}</button>`;

        pageButtons.push(buttonTpl('Prev', state.page - 1, state.page === 1));
        for (let p = 1; p <= totalPages; p += 1) pageButtons.push(buttonTpl(String(p), p, false, p === state.page));
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

    rangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.range = button.getAttribute('data-range') || '24 Jam';
            state.page = 1;
            const options = getDateOptions();
            if (!options.includes(state.date)) state.date = options[0];
            state.rangeStart = options[options.length - 1] || state.date;
            state.rangeEnd = options[0] || state.date;
            state.month = state.date.slice(0, 7);
            renderRange();
            renderDate();
            renderTable();
        });
    });

    dateToggle.addEventListener('click', () => {
        state.showDateMenu = !state.showDateMenu;
        dateMenu.classList.toggle('hidden', !state.showDateMenu);
    });

    datePrev.addEventListener('click', () => {
        if (getDateMode() !== 'single') return;
        const options = getDateOptions();
        const idx = options.indexOf(state.date);
        if (idx > 0) {
            state.date = options[idx - 1];
            renderDate();
            renderTable();
        }
    });

    dateNext.addEventListener('click', () => {
        if (getDateMode() !== 'single') return;
        const options = getDateOptions();
        const idx = options.indexOf(state.date);
        if (idx >= 0 && idx < options.length - 1) {
            state.date = options[idx + 1];
            renderDate();
            renderTable();
        }
    });

    rangeApplyBtn.addEventListener('click', () => {
        if (!rangeStartInput.value || !rangeEndInput.value) return;
        state.rangeStart = rangeStartInput.value;
        state.rangeEnd = rangeEndInput.value;
        state.showDateMenu = false;
        dateMenu.classList.add('hidden');
        renderDate();
        renderTable();
    });

    monthApplyBtn.addEventListener('click', () => {
        if (!monthInput.value) return;
        state.month = monthInput.value;
        state.showDateMenu = false;
        dateMenu.classList.add('hidden');
        renderDate();
        renderTable();
    });

    categoryToggle.addEventListener('click', () => {
        state.showCategoryMenu = !state.showCategoryMenu;
        categoryMenu.classList.toggle('hidden', !state.showCategoryMenu);
    });

    categoryOptions.forEach((option) => {
        option.addEventListener('click', () => {
            const value = option.getAttribute('data-prediction-category-option');
            if (!value) return;
            state.category = value;
            categoryLabel.textContent = value;
            state.page = 1;
            state.showCategoryMenu = false;
            categoryMenu.classList.add('hidden');
            renderTable();
        });
    });

    document.addEventListener('click', (event) => {
        if (!dateMenu.contains(event.target) && !dateToggle.contains(event.target)) {
            state.showDateMenu = false;
            dateMenu.classList.add('hidden');
        }
        if (!categoryMenu.contains(event.target) && !categoryToggle.contains(event.target)) {
            state.showCategoryMenu = false;
            categoryMenu.classList.add('hidden');
        }
    });

    categoryLabel.textContent = state.category;
    renderRange();
    renderDate();
    renderTable();
};

document.addEventListener('DOMContentLoaded', initAdminPredictionData);
