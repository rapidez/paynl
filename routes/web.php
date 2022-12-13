<?php

use Illuminate\Support\Facades\Route;
use Rapidez\Paynl\Http\Controllers\FinishTransactionController;

Route::middleware('web')->group(function () {
    Route::get('paynl/finish', FinishTransactionController::class)->name('paynl.success');
});
