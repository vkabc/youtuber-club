<?php

use App\Http\Controllers\ProfileController;
use App\Models\Nft;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
    return Inertia::render('Auth/Register', [

    ]);
});


Route::get('/edit', function () {
    return Inertia::render('EditDiscord', [
    ]);
});

Route::get('/qrcode/{id}', function (int $id) {


    $url = \route('payment', ['id' => $id]);
    $name = User::find($id)->name;
    $svg = (new Writer(
        new ImageRenderer(
            new RendererStyle(256),
            new SvgImageBackEnd()
        )
    ))->writeString($url);

    $svg = trim(substr($svg, strpos($svg, "\n") + 1));


    return Inertia::render('QrCode', [
        'id' => $id,
        'svg' => $svg,
        'url' => $url,
        'name' => $name,
    ]);
})->name('qrcode');

Route::post('/edit', function () {
    $nft = Nft::where('nft_id', request()->nft_id)->first();
    $nft->discord_id = request()->discord_id;
    $nft->save();
    return redirect()->route('success');
})->name('edit');

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
     $nfts = auth()->user()->nfts;
    return Inertia::render('Dashboard',[
        'id' => auth()->user()->id,
        'nfts' => $nfts,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
