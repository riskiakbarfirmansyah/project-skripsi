<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Role yang boleh dikelola oleh admin.
     * Admin tidak bisa mengelola sesama admin.
     */
    private const ALLOWED_ROLES = ['gatekeeper', 'penyiar', 'pj_program'];

    /**
     * Dashboard admin — menampilkan semua user berdasarkan role.
     *
     * Route: GET /admin/dashboard
     * Middleware: isAdmin
     */
    public function index(): View
    {
        $users = User::whereIn('role', self::ALLOWED_ROLES)
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total_user'       => User::whereIn('role', self::ALLOWED_ROLES)->count(),
            'total_gatekeeper' => User::where('role', 'gatekeeper')->count(),
            'total_penyiar'    => User::where('role', 'penyiar')->count(),
            'total_pj_program' => User::where('role', 'pj_program')->count(),
        ];

        $usersByRole = [];

        return view('admin.dashboard', compact('users', 'stats', 'usersByRole'));
    }

    /**
     * Tampilkan detail satu user.
     *
     * Route: GET /admin/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        $user = User::whereIn('role', self::ALLOWED_ROLES)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => format_tanggal_id($user->created_at) . ', ' . $user->created_at->format('H:i'),
                'updated_at' => format_tanggal_id($user->updated_at) . ', ' . $user->updated_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Kembalikan nilai boolean fields berdasarkan role yang dipilih.
     */
    private function roleFlagAttributes(string $role): array
    {
        return [
            'is_gatekeeper' => $role === 'gatekeeper' ? 1 : 0,
            'is_penyiar'    => $role === 'penyiar'    ? 1 : 0,
            'is_pjprogram'  => $role === 'pj_program' ? 1 : 0,
        ];
    }

    /**
     * Buat akun user baru (gatekeeper / penyiar / pj_program).
     *
     * Route: POST /admin/users
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|in:' . implode(',', self::ALLOWED_ROLES),
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role tidak valid.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ] + $this->roleFlagAttributes($validated['role']));

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil dibuat.",
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 201);
    }

    /**
     * Perbarui data akun user (nama, email, role).
     * Password hanya diperbarui jika field password diisi.
     *
     * Route: PUT /admin/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::whereIn('role', self::ALLOWED_ROLES)->findOrFail($id);

        $rules = [
            'name'  => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:' . implode(',', self::ALLOWED_ROLES),
        ];

        $messages = [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
        ];

        // Password opsional saat update — hanya divalidasi jika diisi
        if ($request->filled('password')) {
            $rules['password']              = ['required', 'confirmed', Password::min(8)];
            $messages['password.confirmed'] = 'Konfirmasi password tidak cocok.';
        }

        $validated = $request->validate($rules, $messages);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];
        $user->fill($this->roleFlagAttributes($validated['role']));

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil diperbarui.",
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Hapus akun user.
     * Admin tidak bisa menghapus akunnya sendiri.
     *
     * Route: DELETE /admin/users/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = User::whereIn('role', self::ALLOWED_ROLES)->findOrFail($id);

        if ((string) $request->user()->id === (string) $id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.',
            ], 403);
        }

        $name = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Akun {$name} berhasil dihapus.",
        ]);
    }

    /**
     * Cari user berdasarkan nama, email, atau role (AJAX).
     *
     * Route: GET /admin/users/search
     */
    public function search(Request $request): JsonResponse
    {
        $query = User::whereIn('role', self::ALLOWED_ROLES);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15);

        return response()->json([
            'success'      => true,
            'total'        => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'data'         => collect($users->items())->map(fn($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'role'  => $u->role,
            ]),
        ]);
    }

    /**
     * Reset password user ke password baru yang ditentukan admin.
     *
     * Route: PUT /admin/users/{id}/reset-password
     */
    public function resetPassword(Request $request, string $id): JsonResponse
    {
        $user = User::whereIn('role', self::ALLOWED_ROLES)->findOrFail($id);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Password akun {$user->name} berhasil direset.",
        ]);
    }

    /**
     * Tampilkan daftar permintaan perubahan password.
     *
     * Route: GET /admin/password-requests
     */
    public function passwordRequests(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [],
        ]);
    }

    /**
     * Setujui permintaan perubahan password.
     *
     * Route: POST /admin/users/{id}/verify-password
     */
    public function verifyPasswordChange(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Permintaan password disetujui.',
        ]);
    }

    /**
     * Tolak permintaan perubahan password.
     *
     * Route: POST /admin/users/{id}/reject-password
     */
    public function rejectPasswordChange(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Permintaan password ditolak.',
        ]);
    }
}