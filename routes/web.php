<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformasiSiaranController;
use App\Http\Controllers\NarasumberByphoneController;
use App\Http\Controllers\NarasumberTalkshowController;
use App\Http\Controllers\NarasumberSelfRegistrationController;
use App\Http\Controllers\BroadcastSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $isLive = \App\Models\informasisiaran::where('status', 'active')->exists()
        || \App\Models\BroadcastSession::where('status', 'active')->exists();

    return view('welcome', compact('isLive'));
});

/*
|--------------------------------------------------------------------------
| NARASUMBER SELF-REGISTRATION (Public — no login required)
|--------------------------------------------------------------------------
*/
Route::post('/daftar-narasumber', [NarasumberSelfRegistrationController::class, 'store'])
    ->name('narasumber.self-register');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    } elseif ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'gatekeeper') {
        return redirect()->route('gatekeeper.dashboard');
    } elseif ($user->role === 'penyiar') {
        return redirect()->route('penyiar.dashboard');
    } elseif ($user->role === 'pj_program') {
        return redirect()->route('pj_program.dashboard');
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMIN
| Akses: Manajemen user (gatekeeper, penyiar, pj_program)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'isAdmin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
 
    // Dashboard admin
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard');
 
    // ── Manajemen User ───────────────────────────────────────────────
    Route::get('/users/search',  [AdminController::class, 'search'])->name('users.search');
    Route::post('/users',        [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{id}',    [AdminController::class, 'show'])->name('users.show');
    Route::put('/users/{id}',    [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
 
    // ── Manajemen Password ───────────────────────────────────────────
    Route::get('/password-requests',               [AdminController::class, 'passwordRequests'])->name('password.requests');
    Route::post('/users/{id}/verify-password',     [AdminController::class, 'verifyPasswordChange'])->name('users.verify-password');
    Route::post('/users/{id}/reject-password',     [AdminController::class, 'rejectPasswordChange'])->name('users.reject-password');
    Route::post('/users/{id}/reset-password',      [AdminController::class, 'resetPassword'])->name('users.reset-password');
});

/*
|--------------------------------------------------------------------------
| GATEKEEPER
| Akses: Informasi Siaran + Narasumber Byphone
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'isGatekeeper', 'prefix' => 'gatekeeper', 'as' => 'gatekeeper.'], function () {
 
    // Dashboard terpadu gatekeeper
    Route::get('/dashboard', [NarasumberByphoneController::class, 'dashboardGatekeeper'])
        ->name('dashboard');
 
    // ── Statistik (AJAX) ─────────────────────────────────────────────
    Route::get('/stats', [NarasumberByphoneController::class, 'getStats'])->name('stats');

    // ── Export Excel ─────────────────────────────────────────────────
    Route::get('/export', [NarasumberByphoneController::class, 'exportGatekeeper'])->name('export');
    Route::get('/export-byphone', [NarasumberByphoneController::class, 'exportByphone'])->name('export-byphone');
    Route::get('/export-siaran', [NarasumberByphoneController::class, 'exportSiaran'])->name('export-siaran');

    // ── Informasi Siaran ─────────────────────────────────────────────
    Route::get('/siaran/search',       [InformasiSiaranController::class, 'search'])->name('siaran.search');
    Route::post('/siaran',             [InformasiSiaranController::class, 'store'])->name('siaran.store');
    Route::get('/siaran/{id}',         [InformasiSiaranController::class, 'show'])->name('siaran.show');
    Route::put('/siaran/{id}',         [InformasiSiaranController::class, 'update'])->name('siaran.update');
    Route::delete('/siaran/{id}',      [InformasiSiaranController::class, 'destroy'])->name('siaran.destroy');
    Route::post('/siaran/{id}/start',  [InformasiSiaranController::class, 'startSiaran'])->name('siaran.start');
    Route::post('/siaran/{id}/end',    [InformasiSiaranController::class, 'endSiaran'])->name('siaran.end');
 
    // ── Narasumber Byphone ───────────────────────────────────────────
    Route::get('/narasumber-byphone/search',   [NarasumberByphoneController::class, 'search'])->name('byphone.search');
    Route::get('/narasumber-byphone/instansi', [NarasumberByphoneController::class, 'instansiList'])->name('byphone.instansi');
    Route::post('/narasumber-byphone',         [NarasumberByphoneController::class, 'store'])->name('byphone.store');
    Route::get('/narasumber-byphone/{id}',     [NarasumberByphoneController::class, 'show'])->name('byphone.show');
    Route::put('/narasumber-byphone/{id}',     [NarasumberByphoneController::class, 'update'])->name('byphone.update');
    Route::patch('/narasumber-byphone/{id}/schedule', [NarasumberByphoneController::class, 'updateSchedule'])->name('byphone.update-schedule');
    Route::delete('/narasumber-byphone/{id}',  [NarasumberByphoneController::class, 'destroy'])->name('byphone.destroy');
});
 
/*
|--------------------------------------------------------------------------
| PENYIAR
| Akses: Narasumber Byphone + Narasumber Talkshow
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'isPenyiar', 'prefix' => 'penyiar', 'as' => 'penyiar.'], function () {
 
    // Dashboard terpadu penyiar
    Route::get('/dashboard', [NarasumberByphoneController::class, 'dashboardPenyiar'])
        ->name('dashboard');
 
    // ── Narasumber Byphone ───────────────────────────────────────────
    Route::get('/narasumber-byphone/search',   [NarasumberByphoneController::class, 'search'])->name('byphone.search');
    Route::get('/narasumber-byphone/instansi', [NarasumberByphoneController::class, 'instansiList'])->name('byphone.instansi');
    Route::post('/narasumber-byphone',         [NarasumberByphoneController::class, 'store'])->name('byphone.store');
    Route::get('/narasumber-byphone/{id}',     [NarasumberByphoneController::class, 'show'])->name('byphone.show');
    Route::put('/narasumber-byphone/{id}',     [NarasumberByphoneController::class, 'update'])->name('byphone.update');
    Route::delete('/narasumber-byphone/{id}',  [NarasumberByphoneController::class, 'destroy'])->name('byphone.destroy');
 
    // ── Narasumber Talkshow ──────────────────────────────────────────
    Route::get('/narasumber-talkshow/search',   [NarasumberTalkshowController::class, 'search'])->name('talkshow.search');
    Route::get('/narasumber-talkshow/instansi', [NarasumberTalkshowController::class, 'instansiList'])->name('talkshow.instansi');
    Route::post('/narasumber-talkshow',         [NarasumberTalkshowController::class, 'store'])->name('talkshow.store');
    Route::get('/narasumber-talkshow/{id}',     [NarasumberTalkshowController::class, 'show'])->name('talkshow.show');
    Route::put('/narasumber-talkshow/{id}',     [NarasumberTalkshowController::class, 'update'])->name('talkshow.update');
    Route::delete('/narasumber-talkshow/{id}',  [NarasumberTalkshowController::class, 'destroy'])->name('talkshow.destroy');

    // ── Broadcast ────────────────────────────────────────────────────
    Route::post('/broadcast/start',   [BroadcastSessionController::class, 'startBroadcast'])->name('broadcast.start');
    Route::post('/broadcast/end',     [BroadcastSessionController::class, 'endBroadcast'])->name('broadcast.end');
    Route::get('/broadcast/current',  [BroadcastSessionController::class, 'getCurrentBroadcast'])->name('broadcast.current');
    Route::get('/broadcast/history',  [BroadcastSessionController::class, 'getBroadcastHistory'])->name('broadcast.history');
});
 
/*
|--------------------------------------------------------------------------
| PJ PROGRAM
| Akses: Narasumber Talkshow saja
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'isPjProgram', 'prefix' => 'pj-program', 'as' => 'pj_program.'], function () {
 
    // Dashboard pj_program
    Route::get('/dashboard', [NarasumberTalkshowController::class, 'dashboardPjProgram'])
        ->name('dashboard');

    // ── Export Excel ─────────────────────────────────────────────────
    Route::get('/export', [NarasumberTalkshowController::class, 'exportPjProgram'])->name('export');
    Route::get('/export-talkshow', [NarasumberTalkshowController::class, 'exportTalkshow'])->name('export-talkshow');

    // ── Narasumber Talkshow ──────────────────────────────────────────
    Route::get('/narasumber-talkshow/search',   [NarasumberTalkshowController::class, 'search'])->name('talkshow.search');
    Route::get('/narasumber-talkshow/instansi', [NarasumberTalkshowController::class, 'instansiList'])->name('talkshow.instansi');
    Route::post('/narasumber-talkshow',         [NarasumberTalkshowController::class, 'store'])->name('talkshow.store');
    Route::get('/narasumber-talkshow/{id}',     [NarasumberTalkshowController::class, 'show'])->name('talkshow.show');
    Route::put('/narasumber-talkshow/{id}',     [NarasumberTalkshowController::class, 'update'])->name('talkshow.update');
    Route::patch('/narasumber-talkshow/{id}/schedule', [NarasumberTalkshowController::class, 'updateSchedule'])->name('talkshow.update-schedule');
    Route::delete('/narasumber-talkshow/{id}',  [NarasumberTalkshowController::class, 'destroy'])->name('talkshow.destroy');
});

require __DIR__.'/auth.php';
