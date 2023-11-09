<?php

use App\Models\Item;
use App\Models\Sale;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Auth\UserController;

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
        'items' => Item::with('tally')->get()->sum(function($q) {
            return $q->tally->number;
        }),
        'current_inventory' => Item::with('tally')->get()->sum(function($q) {
                                        return $q->cost * $q->tally->number;
                                    }),
        'dailyReport' => Sale::whereDate('created_at',now())->exists()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('batch/sell', function () {
        return Inertia::render('BatchSell');
    })->name('batch.sell');
    Route::post('sell/batch', [SaleController::class, 'batchSell'])->name('sell.batch');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}/delete', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::post('/item/count/edit', [ItemController::class, 'setCount'])->name('items.setCount');
    Route::get('items/export', [ItemController::class, 'export'])->name('items.export');
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');

    Route::get('sales', [SaleController::class, 'index'])->name('items.sales');
    Route::post('item/{item}/sale', [SaleController::class, 'store'])->name('sales.store');
    Route::post('check', [SaleController::class, 'check'])->name('check');
    Route::post('sell', [SaleController::class, 'sell'])->name('sell');
    Route::get('sales/export/{period?}/{date?}', [SaleController::class, 'export'])->name('sales.export');
    Route::post('sales/view/', [SaleController::class, 'view'])->name('sales.view');

    Route::put('user/{user}/update', [UserController::class, 'update'])->name('user.update');
    Route::put('{user}/password/change', [UserController::class, 'changePassword'])->name('password.change');

    Route::get('settings', function () {
        return inertia('Settings');
    })->name('settings');

    Route::get('settings/storage/link', function () {
        Artisan::call('storage:link');

        return redirect()->back();
    })->name('storage.link');
});

require __DIR__.'/auth.php';