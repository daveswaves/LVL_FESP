<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CreateOrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UndispatchedController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ArtisanCallsController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\InvController;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| If you receive the following message:
|--------------------------------------------------------------------------
|
| WARN  Failed to listen on 127.0.0.1:8000 (reason: Address already in use).
| INFO  Server running on [http://127.0.0.1:8001].
| 
| Run the following to find the PID running port 8000: $ lsof -i :8000
| 
| Eg.
| COMMAND   PID  USER   FD   TYPE DEVICE SIZE/OFF NODE NAME
| php8.1  18226 david   6u   IPv4 115269      0t0  TCP localhost:8000 (LISTEN)
| 
| The following will stop the server:
| $ sudo kill -9 18226
|
*/

// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
// #### START Laravel #### START Laravel #### START Laravel ####
// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
// cd /opt/lampp/htdocs/LVL_FESP && php artisan serve


// http://127.0.0.1:8000/createOrder
// http://127.0.0.1:8000/createOrder/?pf=am&id=026-5959042-4144314
Route::get('createOrder', [CreateOrderController::class, 'index'])->name('createOrder');


// http://127.0.0.1:8000/products
// http://127.0.0.1:8000/products/?page=2&limit=20
Route::match(['get', 'post'], 'products', [ProductsController::class, 'products'])->name('products');

// http://127.0.0.1:8000
Route::get('/', [UndispatchedController::class, 'index'])->name('undispatched');
// Route::get('/{id}', [UndispatchedController::class, 'index'])->name('undispatched');

Route::match(['get', 'post'], 'invoice', [InvController::class, 'invoice'])->name('invoice');
// Route::match(['get', 'post'], 'view_inv', [InvController::class, 'view'])->name('view_inv');
// Route::match(['get', 'post'], 'print_inv', [InvController::class, 'print'])->name('print_inv');

Route::post('ajax/modifyDb', [AjaxController::class, 'modifyDb'])->name('ajax.modifyDb');

// http://127.0.0.1:8000/exportCsv
Route::get('exportCsv', [CsvController::class, 'exportCsv'])->name('exportCsv');

// http://127.0.0.1:8000/importCsv
Route::post('importCsv', [CsvController::class, 'importCsv'])->name('importCsv');

// http://127.0.0.1:8000/artisan/run_migrate_fresh__seed
Route::get('artisan/run_migrate_fresh__seed', [ArtisanCallsController::class, 'runMigrateFreshSeed'])->name('runMigrateFreshSeed');





/*
1. Laravel PDF - Convert HTML to PDF : https://youtu.be/XPqOVRx4W5s
2. Laravel PDF - Convert HTML to PDF : https://youtu.be/r8YjRCfFexg
https://github.com/barryvdh/laravel-snappy
*/
// http://127.0.0.1:8000/snappy
Route::get('snappy', function () {
    $html = '<h1>Snappy PDF</h1>';
    
    // $pdf = App::make('snappy.pdf.wrapper');
    // $pdf->generateFromHtml($html, 'hello.pdf');
    // $pdf->inline();
    
    $pdf = PDF::loadHtml($html);
    return $pdf->stream(); // display in web browser
    // return $pdf->download('invoice.pdf'); // download PDF file
})->name('snappy');

// http://127.0.0.1:8000/phpinfo
Route::get('phpinfo', function () {
    phpinfo();
})->name('phpinfo');

// http://127.0.0.1:8000/testdb
Route::get('testdb', function () {
    return DB::table('sku_img')->get();
})->name('testdb');