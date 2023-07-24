<?php

use App\Models\Item;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ItemController;

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
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'items' => Item::all()->count()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
Route::delete('/items/{item}/delete', [ItemController::class, 'destroy'])->name('items.destroy');
Route::post('/item/count/edit', [ItemController::class, 'setCount'])->name('items.setCount');

require __DIR__.'/auth.php';
