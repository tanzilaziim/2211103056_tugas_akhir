const ROLE_OPTIONS = ['Super Admin', 'Admin', 'Operator', 'Viewer'];

const formatToday = () =>
    new Date()
        .toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        })
        .replace(/\//g, '-');

const initialAccounts = [
    { id: 1, name: 'Super Admin DLH', email: 'superadmin@dlhindramayu.id', role: 'Super Admin', lastLogin: '23-03-2026' },
    { id: 2, name: 'Admin Operasional', email: 'admin.ops@dlhindramayu.id', role: 'Admin', lastLogin: '22-03-2026' },
    { id: 3, name: 'Admin Data', email: 'admin.data@dlhindramayu.id', role: 'Admin', lastLogin: '21-03-2026' },
    { id: 4, name: 'Viewer DLH', email: 'viewer@dlhindramayu.id', role: 'Viewer', lastLogin: '20-03-2026' },
    { id: 5, name: 'Operator Lapangan', email: 'operator@dlhindramayu.id', role: 'Operator', lastLogin: '19-03-2026' },
];

const initAdminAccountSettings = () => {
    const root = document.querySelector('[data-page="admin-account-settings"]');
    if (!root) return;

    const state = {
        accounts: [...initialAccounts],
        editId: null,
        deleteId: null,
        selectedRole: ROLE_OPTIONS[0],
        showRoleMenu: false,
    };

    const tableBody = root.querySelector('[data-account-table-body]');
    const addBtn = root.querySelector('[data-account-add-btn]');

    const formModal = root.querySelector('[data-account-form-modal]');
    const formTitle = root.querySelector('[data-account-form-title]');
    const form = root.querySelector('[data-account-form]');
    const nameInput = root.querySelector('[data-account-name]');
    const emailInput = root.querySelector('[data-account-email]');
    const passwordInput = root.querySelector('[data-account-password]');
    const formCancel = root.querySelector('[data-account-form-cancel]');
    const errName = root.querySelector('[data-error-name]');
    const errEmail = root.querySelector('[data-error-email]');
    const errPassword = root.querySelector('[data-error-password]');
    const errRole = root.querySelector('[data-error-role]');

    const roleToggle = root.querySelector('[data-role-toggle]');
    const roleLabel = root.querySelector('[data-role-label]');
    const roleMenu = root.querySelector('[data-role-menu]');
    const roleOptions = root.querySelectorAll('[data-role-option]');

    const deleteModal = root.querySelector('[data-account-delete-modal]');
    const deleteName = root.querySelector('[data-account-delete-name]');
    const deleteCancel = root.querySelector('[data-account-delete-cancel]');
    const deleteConfirm = root.querySelector('[data-account-delete-confirm]');

    if (
        !tableBody || !addBtn ||
        !formModal || !formTitle || !form || !nameInput || !emailInput || !passwordInput || !formCancel ||
        !errName || !errEmail || !errPassword || !errRole ||
        !roleToggle || !roleLabel || !roleMenu || !roleOptions.length ||
        !deleteModal || !deleteName || !deleteCancel || !deleteConfirm
    ) return;

    const clearErrors = () => {
        [errName, errEmail, errPassword, errRole].forEach((el) => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    };

    const setError = (el, message) => {
        el.textContent = message;
        el.classList.remove('hidden');
    };

    const resetForm = () => {
        nameInput.value = '';
        emailInput.value = '';
        passwordInput.value = '';
        state.selectedRole = ROLE_OPTIONS[0];
        roleLabel.textContent = state.selectedRole;
        clearErrors();
    };

    const openFormModal = (mode, account = null) => {
        state.editId = account?.id ?? null;
        formTitle.textContent = mode === 'edit' ? 'Edit Akun' : 'Tambah Akun';
        if (mode === 'edit' && account) {
            nameInput.value = account.name;
            emailInput.value = account.email;
            passwordInput.value = '';
            state.selectedRole = account.role;
            roleLabel.textContent = account.role;
        } else {
            resetForm();
        }
        formModal.classList.remove('hidden');
        formModal.classList.add('flex');
    };

    const closeFormModal = () => {
        formModal.classList.add('hidden');
        formModal.classList.remove('flex');
        state.showRoleMenu = false;
        roleMenu.classList.add('hidden');
        state.editId = null;
        clearErrors();
    };

    const openDeleteModal = (account) => {
        state.deleteId = account.id;
        deleteName.textContent = ` ${account.name}`;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    };

    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
        state.deleteId = null;
    };

    const renderTable = () => {
        if (!state.accounts.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="border-b border-l border-r border-surface-200 bg-primary-50 px-4 py-8 text-center text-base text-surface-300">Belum ada akun terdaftar</td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = state.accounts.map((account) => `
            <tr>
                <td class="border-b border-l border-r-0 border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${account.name}</td>
                <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${account.email}</td>
                <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${account.role}</td>
                <td class="border-b border-surface-200 bg-primary-50 px-4 py-3.5 text-base text-surface-300">${account.lastLogin}</td>
                <td class="border-b border-r border-surface-200 bg-primary-50 px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <button type="button" data-action="edit" data-id="${account.id}" class="inline-flex items-center gap-2 text-ispu-tidak-sehat transition-opacity hover:opacity-80">
                            <span class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-ispu-tidak-sehat text-surface-50">
                                <i class="ph ph-pencil-simple-line text-base"></i>
                            </span>
                            <span class="text-base underline">Edit</span>
                        </button>
                        <button type="button" data-action="delete" data-id="${account.id}" class="inline-flex items-center gap-2 text-ispu-sangat-tidak-sehat transition-opacity hover:opacity-80">
                            <span class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-ispu-sangat-tidak-sehat text-surface-50">
                                <i class="ph ph-trash text-base"></i>
                            </span>
                            <span class="text-base underline">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableBody.querySelectorAll('[data-action="edit"]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-id'));
                const account = state.accounts.find((item) => item.id === id);
                if (account) openFormModal('edit', account);
            });
        });

        tableBody.querySelectorAll('[data-action="delete"]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-id'));
                const account = state.accounts.find((item) => item.id === id);
                if (account) openDeleteModal(account);
            });
        });
    };

    addBtn.addEventListener('click', () => openFormModal('add'));
    formCancel.addEventListener('click', closeFormModal);

    roleToggle.addEventListener('click', () => {
        state.showRoleMenu = !state.showRoleMenu;
        roleMenu.classList.toggle('hidden', !state.showRoleMenu);
    });

    roleOptions.forEach((option) => {
        option.addEventListener('click', () => {
            const value = option.getAttribute('data-role-option');
            if (!value) return;
            state.selectedRole = value;
            roleLabel.textContent = value;
            state.showRoleMenu = false;
            roleMenu.classList.add('hidden');
            errRole.classList.add('hidden');
            errRole.textContent = '';
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearErrors();

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const role = state.selectedRole;

        let invalid = false;
        if (!name) {
            setError(errName, 'Nama wajib diisi');
            invalid = true;
        }

        if (!email) {
            setError(errEmail, 'Email wajib diisi');
            invalid = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError(errEmail, 'Format email tidak valid');
            invalid = true;
        }

        if (!state.editId && !password) {
            setError(errPassword, 'Kata sandi wajib diisi');
            invalid = true;
        }

        if (!role) {
            setError(errRole, 'Role wajib dipilih');
            invalid = true;
        }

        if (invalid) return;

        if (state.editId) {
            state.accounts = state.accounts.map((account) =>
                account.id === state.editId
                    ? { ...account, name, email, role }
                    : account,
            );
        } else {
            state.accounts.push({
                id: Date.now(),
                name,
                email,
                role,
                lastLogin: formatToday(),
            });
        }

        renderTable();
        closeFormModal();
    });

    deleteCancel.addEventListener('click', closeDeleteModal);
    deleteConfirm.addEventListener('click', () => {
        if (!state.deleteId) return;
        state.accounts = state.accounts.filter((account) => account.id !== state.deleteId);
        renderTable();
        closeDeleteModal();
    });

    formModal.addEventListener('click', (event) => {
        if (event.target === formModal) closeFormModal();
    });

    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) closeDeleteModal();
    });

    document.addEventListener('click', (event) => {
        if (!roleMenu.contains(event.target) && !roleToggle.contains(event.target)) {
            state.showRoleMenu = false;
            roleMenu.classList.add('hidden');
        }
    });

    renderTable();
};

document.addEventListener('DOMContentLoaded', initAdminAccountSettings);
