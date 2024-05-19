<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/user',function (Request $request){
        return $request->user();
    });
    Route::resource('/product', \App\Http\Controllers\ProductController::class);
    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
});

Route::prefix('admin')->middleware(['auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'index']);
    Route::post('/feedback/{feedback_id}/response', [\App\Http\Controllers\FeedbackController::class, 'postResponse']);
    Route::put('/toggle-feedback/{feedback_id}', [\App\Http\Controllers\FeedbackController::class, 'toggleFeedback']);
});

require __DIR__.'/auth.php';
