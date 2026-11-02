<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Fanclub\CastContentChildController;
use App\Http\Controllers\Api\Fanclub\CastContentController;
use App\Http\Controllers\Api\Fanclub\ContentController;
use App\Http\Controllers\Api\Fanclub\FanclubController;
use App\Http\Controllers\Api\Fanclub\MembershipController;
// SquareWebhookController は routes/web.php の /payment/webhook/square で定義

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:member');

Route::prefix('v1/fanclubs')->group(function () {
    Route::get('/', [FanclubController::class, 'index']);
    Route::get('{fanclub}', [FanclubController::class, 'show']);
    Route::get('{fanclub}/contents', [ContentController::class, 'index']);

    Route::middleware('auth:member')->group(function () {
        Route::post('{fanclub}/subscribe', [MembershipController::class, 'subscribe']);
        Route::patch('{fanclub}/membership', [MembershipController::class, 'updateStatus']);
    });
});
// Square Webhook は routes/web.php の POST /payment/webhook/square に移行済み

Route::prefix('v1/item/fanclub-contents')
    ->middleware('auth:item')
    ->group(function () {
        Route::get('/', [CastContentController::class, 'index']);
        Route::post('/', [CastContentController::class, 'store']);
        Route::put('{fanclub_content}', [CastContentController::class, 'update']);
        Route::delete('{fanclub_content}', [CastContentController::class, 'destroy']);
        Route::get('{fanclub_content}/children', [CastContentChildController::class, 'index']);
        Route::post('{fanclub_content}/children', [CastContentChildController::class, 'store']);
        Route::put('{fanclub_content}/children/{child}', [CastContentChildController::class, 'update']);
        Route::delete('{fanclub_content}/children/{child}', [CastContentChildController::class, 'destroy']);
    });