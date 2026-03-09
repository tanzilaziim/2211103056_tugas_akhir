@extends('layouts.admin')

@section('title', 'Pengaturan Akun Admin')
@section('admin_page_title', 'Pengaturan Akun')

@section('content')
<div data-page="admin-account-settings" class="min-h-full bg-surface-50">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-surface-400">Pengaturan Akun</h1>
            <p class="mt-2 text-base text-surface-300">
                Kelola akun yang berhak untuk mengakses halaman admin sistem
            </p>
        </div>
        <button
            type="button"
            data-account-add-btn
            class="inline-flex items-center gap-2 self-start rounded-full bg-primary-300 px-5 py-2.5 text-base font-bold text-surface-50 transition-colors hover:bg-primary-400"
        >
            <i class="ph-bold ph-plus text-xl"></i>
            Tambah Akun
        </button>
    </div>

    <div class="overflow-hidden rounded-[15px] border border-surface-200 bg-surface-100">
        <div class="px-6 py-5">
            <h2 class="text-2xl font-bold text-surface-400">Daftar Akun Admin</h2>
        </div>

        <div class="px-6 pb-8">
            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-primary-300">
                            <th class="rounded-tl-xl border-l-2 border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Nama</th>
                            <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Email</th>
                            <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Role</th>
                            <th class="border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Last Login</th>
                            <th class="rounded-tr-xl border-r-2 border-t-2 border-primary-300 px-4 py-3.5 text-left text-base font-bold text-surface-50">Action</th>
                        </tr>
                    </thead>
                    <tbody data-account-table-body class="bg-primary-50"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-account-form-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-[640px] rounded-[15px] border border-surface-200 bg-surface-100 px-8 py-8 shadow-xl sm:px-12 sm:py-10">
            <h3 data-account-form-title class="mb-8 text-2xl font-semibold text-surface-400">Tambah Akun</h3>

            <form data-account-form class="flex flex-col gap-3">
                <div class="flex flex-col gap-2">
                    <label class="text-lg font-normal text-surface-300">Nama</label>
                    <input data-account-name type="text" class="h-14 rounded-[15px] border border-surface-300/50 bg-transparent px-4 text-base text-surface-400 outline-none transition-colors focus:border-primary-300" placeholder="Masukkan nama">
                    <p data-error-name class="hidden text-xs text-ispu-sangat-tidak-sehat"></p>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-lg font-normal text-surface-300">Email</label>
                    <input data-account-email type="email" class="h-14 rounded-[15px] border border-surface-300/50 bg-transparent px-4 text-base text-surface-400 outline-none transition-colors focus:border-primary-300" placeholder="Masukkan email">
                    <p data-error-email class="hidden text-xs text-ispu-sangat-tidak-sehat"></p>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-lg font-normal text-surface-300">Kata sandi</label>
                    <input data-account-password type="password" class="h-14 rounded-[15px] border border-surface-300/50 bg-transparent px-4 text-base text-surface-400 outline-none transition-colors focus:border-primary-300" placeholder="Masukkan kata sandi">
                    <p data-error-password class="hidden text-xs text-ispu-sangat-tidak-sehat"></p>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-lg font-normal text-surface-300">Role</label>
                    <div class="relative">
                        <button
                            type="button"
                            data-role-toggle
                            class="flex h-14 w-full items-center justify-between rounded-[15px] border border-surface-300/50 bg-transparent px-4 text-left text-base text-surface-400 transition-colors hover:bg-surface-200/40"
                        >
                            <span data-role-label>Super Admin</span>
                            <i class="ph ph-caret-down text-xl text-surface-300"></i>
                        </button>
                        <div data-role-menu class="absolute left-0 top-full z-10 mt-1 hidden w-full overflow-hidden rounded-xl border border-surface-200 bg-surface-100 shadow-lg">
                            <button type="button" data-role-option="Super Admin" class="block w-full px-4 py-3 text-left text-base text-surface-400 transition-colors hover:bg-primary-50">Super Admin</button>
                            <button type="button" data-role-option="Admin" class="block w-full px-4 py-3 text-left text-base text-surface-400 transition-colors hover:bg-primary-50">Admin</button>
                            <button type="button" data-role-option="Operator" class="block w-full px-4 py-3 text-left text-base text-surface-400 transition-colors hover:bg-primary-50">Operator</button>
                            <button type="button" data-role-option="Viewer" class="block w-full px-4 py-3 text-left text-base text-surface-400 transition-colors hover:bg-primary-50">Viewer</button>
                        </div>
                    </div>
                    <p data-error-role class="hidden text-xs text-ispu-sangat-tidak-sehat"></p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button type="submit" class="h-14 rounded-[15px] bg-primary-300 text-lg font-bold tracking-wide text-surface-50 transition-colors hover:bg-primary-400">SIMPAN</button>
                    <button type="button" data-account-form-cancel class="h-14 rounded-[15px] border border-primary-300 bg-primary-50 text-lg font-bold tracking-wide text-primary-300 transition-colors hover:bg-primary-100">BATAL</button>
                </div>
            </form>
        </div>
    </div>

    <div data-account-delete-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-surface-100 p-6 shadow-xl">
            <h3 class="mb-2 text-xl font-bold text-surface-400">Hapus Akun</h3>
            <p class="mb-6 text-surface-300">
                Apakah Anda yakin ingin menghapus akun
                <span data-account-delete-name class="font-semibold text-surface-400"></span>?
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" data-account-delete-cancel class="rounded-lg border border-surface-200 px-4 py-2 font-medium text-surface-300 transition-colors hover:bg-surface-200">Batal</button>
                <button type="button" data-account-delete-confirm class="rounded-lg bg-ispu-sangat-tidak-sehat px-4 py-2 font-medium text-surface-50 transition-colors hover:brightness-95">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection
