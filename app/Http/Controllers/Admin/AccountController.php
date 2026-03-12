<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    private const ROLE_MAP_TO_DB = [
        'Super Admin' => 'super_admin',
        'Admin' => 'admin',
        'Operator' => 'operator',
        'Viewer' => 'viewer',
    ];

    private const ROLE_MAP_FROM_DB = [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'operator' => 'Operator',
        'viewer' => 'Viewer',
    ];

    public function index(): JsonResponse
    {
        $accounts = User::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (User $user) => $this->transform($user))
            ->values();

        return response()->json(['data' => $accounts]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureSuperAdmin($request);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(self::ROLE_MAP_TO_DB))],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user = User::query()->create([
            'name' => $payload['name'],
            'username' => $this->generateUsername($payload['name'], $payload['email']),
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'role' => self::ROLE_MAP_TO_DB[$payload['role']],
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Akun berhasil ditambahkan.',
            'data' => $this->transform($user),
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->ensureSuperAdmin($request);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(self::ROLE_MAP_TO_DB))],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ]);

        $nextRole = self::ROLE_MAP_TO_DB[$payload['role']];
        if ($user->role === 'super_admin' && $nextRole !== 'super_admin' && $this->superAdminCount() <= 1) {
            return response()->json(['message' => 'Minimal harus ada satu Super Admin aktif.'], 422);
        }

        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->role = $nextRole;
        $user->username = $user->username ?: $this->generateUsername($payload['name'], $payload['email']);
        if (! empty($payload['password'])) {
            $user->password = Hash::make($payload['password']);
        }
        $user->save();

        return response()->json([
            'message' => 'Akun berhasil diperbarui.',
            'data' => $this->transform($user),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->ensureSuperAdmin($request);

        if ((int) $request->user()->id === (int) $user->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        if ($user->role === 'super_admin' && $this->superAdminCount() <= 1) {
            return response()->json(['message' => 'Minimal harus ada satu Super Admin aktif.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $this->ensureSuperAdmin($request);

        $payload = $request->validate([
            'new_password' => ['nullable', 'string', 'min:8', 'max:100'],
        ]);

        $newPassword = $payload['new_password'] ?? $this->generateTempPassword();
        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json([
            'message' => 'Kata sandi berhasil direset.',
            'temp_password' => $payload['new_password'] ? null : $newPassword,
        ]);
    }

    private function ensureSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'super_admin', 403, 'Hanya Super Admin yang dapat melakukan aksi ini.');
    }

    private function superAdminCount(): int
    {
        return User::query()->where('role', 'super_admin')->count();
    }

    private function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => self::ROLE_MAP_FROM_DB[$user->role] ?? 'Admin',
            'lastLogin' => $user->last_login_at?->format('d-m-Y') ?? '-',
            'isActive' => (bool) $user->is_active,
        ];
    }

    private function generateUsername(string $name, string $email): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9]+/i', '.', trim($name)));
        $base = trim($base, '.');
        $base = $base ?: strstr($email, '@', true);

        $candidate = $base;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $suffix++;
            $candidate = "{$base}.{$suffix}";
        }

        return $candidate;
    }

    private function generateTempPassword(): string
    {
        return 'Dlh#' . substr(bin2hex(random_bytes(6)), 0, 8);
    }
}

