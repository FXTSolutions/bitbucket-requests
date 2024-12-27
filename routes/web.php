<?php

use App\Http\Controllers\PullRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pull-requests', [PullRequestController::class, 'index'])->name('pull-requests.index');
Route::post('/pull-requests/show', [PullRequestController::class, 'show'])->name('pull-requests.show');
Route::post('/pull-requests', [PullRequestController::class, 'updateObservations']);
