<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    /**
     * Konfigurasi tampilan per role (layout, warna, label badge).
     * Digunakan oleh view tunggal profile.show.
     */
    private array $roleConfig = [
        'admin' => [
            'layout'       => 'admin',
            'roleLabel'    => 'Administrator',
            'roleColor'    => '#6d28d9',
            'roleAvatarBg' => '#f5f3ff',
            'roleBadgeBg'  => '#ede9fe',
            'roleBadgeText'=> '#4c1d95',
        ],
        'gatekeeper' => [
            'layout'       => 'gatekeeper',
            'roleLabel'    => 'Gatekeeper',
            'roleColor'    => '#1d4ed8',
            'roleAvatarBg' => '#eff6ff',
            'roleBadgeBg'  => '#dbeafe',
            'roleBadgeText'=> '#1e3a8a',
        ],
        'penyiar' => [
            'layout'       => 'penyiar',
            'roleLabel'    => 'Penyiar',
            'roleColor'    => '#059669',
            'roleAvatarBg' => '#ecfdf5',
            'roleBadgeBg'  => '#d1fae5',
            'roleBadgeText'=> '#065f46',
        ],
        'pj_program' => [
            'layout'       => 'pjprogram',
            'roleLabel'    => 'PJ Program',
            'roleColor'    => '#d97706',
            'roleAvatarBg' => '#fffbeb',
            'roleBadgeBg'  => '#fef3c7',
            'roleBadgeText'=> '#92400e',
        ],
    ];

    public function edit(Request $request): View
    {
        $user   = $request->user();
        $config = $this->roleConfig[$user->role] ?? [
            'layout'       => 'admin',
            'roleLabel'    => ucfirst($user->role),
            'roleColor'    => '#6d28d9',
            'roleAvatarBg' => '#f5f3ff',
            'roleBadgeBg'  => '#ede9fe',
            'roleBadgeText'=> '#4c1d95',
        ];

        return view('profile.show', array_merge(['user' => $user], $config));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
