<?php

use App\Http\Controllers\ProfileController;
use App\Models\Nft;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/payment/{id}', function (int $id) {
    $user = User::find($id);
    return Inertia::render('Payment', [
        'user' => $user,
        'id' => $id,
    ]);
});

Route::get('/success', function () {
    return Inertia::render('Success', [
    ]);
})->name('success');


Route::post('/payment/{id}', function (int $id) {
    $user = User::find($id);
    NFT::create([
        'discord_id' => request()->discord_id,
        'nft_id' => request()->nft_id,
        'user_id' => request()->user_id,
    ]);
    return Inertia::render('Success', [
    ]);
})->name('payment');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
