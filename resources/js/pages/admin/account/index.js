const ROLE_OPTIONS = ['Super Admin', 'Admin'];

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const api = async (url, options = {}) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers || {}),
        },
        ...options,
    });

    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
        const message = payload?.message || payload?.errors?.[Object.keys(payload.errors || {})[0]]?.[0] || 'Terjadi kesalahan.';
        throw new Error(message);
    }

    return payload;
};

const initAdminAccountSettings = () => {
    const root = document.querySelector('[data-page="admin-account-settings"]');
    if (!root) return;

    const state = {
        accounts: [],
        editId: null,
        deleteId: null,
        resetId: null,
        resetDone: false,
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

    const resetModal = root.querySelector('[data-account-reset-modal]');
    const resetName = root.querySelector('[data-account-reset-name]');
    const resetPassword = root.querySelector('[data-account-reset-password]');
    const resetError = root.querySelector('[data-account-reset-error]');
    const resetResult = root.querySelector('[data-account-reset-result]');
    const resetCancel = root.querySelector('[data-account-reset-cancel]');
    const resetConfirm = root.querySelector('[data-account-reset-confirm]');

    if (
        !tableBody || !addBtn ||
        !formModal || !formTitle || !form || !nameInput || !emailInput || !passwordInput || !formCancel ||
        !errName || !errEmail || !errPassword || !errRole ||
        !roleToggle || !roleLabel || !roleMenu || !roleOptions.length ||
        !deleteModal || !deleteName || !deleteCancel || !deleteConfirm ||
        !resetModal || !resetName || !resetPassword || !resetError || !resetResult || !resetCancel || !resetConfirm
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

    const openResetModal = (account) => {
        state.resetId = account.id;
        state.resetDone = false;
        resetName.textContent = ` ${account.name}`;
        resetPassword.value = '';
        resetError.textContent = '';
        resetError.classList.add('hidden');
        resetResult.textContent = '';
        resetResult.classList.add('hidden');
        resetCancel.classList.remove('hidden');
        resetConfirm.textContent = 'Reset';
        resetModal.classList.remove('hidden');
        resetModal.classList.add('flex');
    };

    const closeResetModal = () => {
        resetModal.classList.add('hidden');
        resetModal.classList.remove('flex');
        state.resetId = null;
        state.resetDone = false;
    };

    const refreshAccounts = async () => {
        const payload = await api('/admin/api/accounts');
        state.accounts = payload.data || [];
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
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-action="edit" data-id="${account.id}" class="inline-flex items-center gap-2 text-ispu-tidak-sehat transition-opacity hover:opacity-80">
                            <span class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-ispu-tidak-sehat text-surface-50">
                                <i class="ph ph-pencil-simple-line text-base"></i>
                            </span>
                            <span class="text-base underline">Edit</span>
                        </button>
                        <button type="button" data-action="reset" data-id="${account.id}" class="inline-flex items-center gap-2 text-primary-300 transition-opacity hover:opacity-80">
                            <span class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-primary-300 text-surface-50">
                                <i class="ph ph-key text-base"></i>
                            </span>
                            <span class="text-base underline">Reset</span>
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

        tableBody.querySelectorAll('[data-action="reset"]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-id'));
                if (Number.isNaN(id)) return;
                const account = state.accounts.find((item) => item.id === id);
                if (account) openResetModal(account);
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

    form.addEventListener('submit', async (event) => {
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

        try {
            const payload = { name, email, role };
            if (password) payload.password = password;

            if (state.editId) {
                await api(`/admin/api/accounts/${state.editId}`, {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                });
            } else {
                await api('/admin/api/accounts', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
            }

            await refreshAccounts();
            renderTable();
            closeFormModal();
        } catch (error) {
            if (error.message.toLowerCase().includes('email')) setError(errEmail, error.message);
            else setError(errName, error.message);
        }
    });

    deleteCancel.addEventListener('click', closeDeleteModal);
    deleteConfirm.addEventListener('click', async () => {
        if (!state.deleteId) return;
        try {
            await api(`/admin/api/accounts/${state.deleteId}`, { method: 'DELETE' });
            await refreshAccounts();
            renderTable();
            closeDeleteModal();
        } catch (error) {
            window.alert(error.message);
        }
    });

    resetCancel.addEventListener('click', closeResetModal);
    resetConfirm.addEventListener('click', async () => {
        if (state.resetDone) {
            closeResetModal();
            return;
        }

        if (!state.resetId) return;

        const newPassword = resetPassword.value.trim();
        resetError.textContent = '';
        resetError.classList.add('hidden');

        if (newPassword && newPassword.length < 8) {
            resetError.textContent = 'Kata sandi minimal 8 karakter.';
            resetError.classList.remove('hidden');
            return;
        }

        try {
            const body = newPassword ? { new_password: newPassword } : {};
            const payload = await api(`/admin/api/accounts/${state.resetId}/reset-password`, {
                method: 'POST',
                body: JSON.stringify(body),
            });

            if (payload.temp_password) {
                resetResult.textContent = `Reset berhasil. Password sementara: ${payload.temp_password}`;
            } else {
                resetResult.textContent = 'Reset kata sandi berhasil.';
            }

            resetResult.classList.remove('hidden');
            resetCancel.classList.add('hidden');
            resetConfirm.textContent = 'Tutup';
            state.resetDone = true;
            await refreshAccounts();
            renderTable();
        } catch (error) {
            resetError.textContent = error.message;
            resetError.classList.remove('hidden');
        }
    });

    formModal.addEventListener('click', (event) => {
        if (event.target === formModal) closeFormModal();
    });

    deleteModal.addEventListener('click', (event) => {
        if (event.target === deleteModal) closeDeleteModal();
    });

    resetModal.addEventListener('click', (event) => {
        if (event.target === resetModal) closeResetModal();
    });

    document.addEventListener('click', (event) => {
        if (!roleMenu.contains(event.target) && !roleToggle.contains(event.target)) {
            state.showRoleMenu = false;
            roleMenu.classList.add('hidden');
        }
    });

    refreshAccounts()
        .then(renderTable)
        .catch((error) => {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="border-b border-l border-r border-surface-200 bg-primary-50 px-4 py-8 text-center text-base text-ispu-sangat-tidak-sehat">${error.message}</td>
                </tr>
            `;
        });
};

document.addEventListener('DOMContentLoaded', initAdminAccountSettings);
