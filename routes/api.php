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

Route::post('auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::post('auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);


Route::group([
    'middleware' => 'auth:sanctum'
], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Aufträge
    Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/orders2', [\App\Http\Controllers\Api\OrderController::class, 'index2']);
    Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    //Route::post('/orders/addorder', [\App\Http\Controllers\Api\OrderController::class, 'addOrder']);

    // Checkout
    Route::get('/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'index']);
    Route::post('/checkout/addorder', [\App\Http\Controllers\Api\CheckoutController::class, 'addOrder']);
    Route::delete('/checkout/deleteorder', [\App\Http\Controllers\Api\CheckoutController::class, 'deleteOrder']);

});

// Benutzer
Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
Route::post('/users', [\App\Http\Controllers\Api\UserController::class, 'saveUser']);
Route::delete('/users', [\App\Http\Controllers\Api\UserController::class, 'Delete']);

// Aufträge
//Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
//Route::get('/orders2', [\App\Http\Controllers\Api\OrderController::class, 'index2']);
//Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
//Route::post('/orders/addorder', [\App\Http\Controllers\Api\OrderController::class, 'addOrder']);

// Auftragsfilter
Route::get('/addBausteineToOrderFilter', [\App\Http\Controllers\Api\OrderFilterController::class, 'addBausteineToOrderFilter']);

// Checkout
//Route::get('/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'index']);
//Route::post('/checkout/addorder', [\App\Http\Controllers\Api\CheckoutController::class, 'addOrder']);
//Route::delete('/checkout/deleteorder', [\App\Http\Controllers\Api\CheckoutController::class, 'deleteOrder']);

// Buchung
Route::post('/bookings/bookorders', [\App\Http\Controllers\Api\BookingController::class, 'bookOrders']);

// ----- Dispo Online Transfer ----- */

Route::group([
    'prefix' => 'V1'
], function () {

    // Login
    Route::post('getAccessToken', [\App\Http\Controllers\Api\AuthController::class, 'getAccessToken']);

    Route::group([
        'middleware' => 'auth:sanctum'
    ], function () {

        /* ----- User ----- */

        // Benutzer löschen
        Route::delete('/users', [\App\Http\Controllers\Api\UserController::class, 'Delete']);

        // Alle Benutzer anzeigen
        Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'Get']);

        // Benutzer speichern
        Route::post('/users', [\App\Http\Controllers\Api\UserController::class, 'Save']);

        /* ----- END User ----- */


        /* ----- Userprojects ----- */

        // Benutzerprojekte löschen
        Route::delete('userprojects', [\App\Http\Controllers\Api\UserProjectController::class, 'Delete']);

        // Alle Benutzerprojekte anzeigen
        Route::get('userprojects', [\App\Http\Controllers\Api\UserProjectController::class, 'Get']);

        // Benutzerprojekte speichern
        Route::post('userprojects', [\App\Http\Controllers\Api\UserProjectController::class, 'Save']);

        /* ----- END Userprojects ----- */


        /* ----- Projects ----- */

        // Projekte löschen
        Route::delete('projects', [\App\Http\Controllers\Api\ProjectController::class, 'Delete']);

        // Alle Projekte anzeigen
        Route::get('projects', [\App\Http\Controllers\Api\ProjectController::class, 'Get']);

        // Projekte speichern
        Route::post('projects', [\App\Http\Controllers\Api\ProjectController::class, 'Save']);

        /* ----- END Projects ----- */


        /* ----- Shiftdays ----- */

        // Schichttage löschen
        Route::delete('shiftdays', [\App\Http\Controllers\Api\ShiftDayController::class, 'Delete']);

        // Alle Schichttage anzeigen
        Route::get('shiftdays', [\App\Http\Controllers\Api\ShiftDayController::class, 'Get']);

        // Schichttage speichern
        Route::post('shiftdays', [\App\Http\Controllers\Api\ShiftDayController::class, 'Save']);

        /* ----- END Shiftdays ----- */


        /* ----- Order ----- */

        // Aufträge löschen
        Route::delete('orders', [\App\Http\Controllers\Api\OrderController::class, 'Delete']);

        // Alle Aufträge anzeigen
        Route::get('orders', [\App\Http\Controllers\Api\OrderController::class, 'Get']);

        // Auftrag speichern
        Route::post('orders', [\App\Http\Controllers\Api\OrderController::class, 'Save']);

        /* ----- END Order ----- */


        /* ----- Orderfilter ----- */

        // Aufträge löschen
        Route::delete('orderfilter', [\App\Http\Controllers\Api\OrderFilterController::class, 'Delete']);

        // Alle Aufträge anzeigen
        Route::get('orderfilter', [\App\Http\Controllers\Api\OrderFilterController::class, 'Get']);

        // Auftrag speichern
        Route::post('orderfilter', [\App\Http\Controllers\Api\OrderFilterController::class, 'Save']);

        /* ----- END Orderfilter ----- */


        /* ----- Honorar ----- */

        // Honorar löschen
        Route::delete('commissions', [\App\Http\Controllers\Api\CommissionController::class, 'Delete']);

        // Alle Honorar anzeigen
        Route::get('commissions', [\App\Http\Controllers\Api\CommissionController::class, 'Get']);

        // Honorar speichern
        Route::post('commissions', [\App\Http\Controllers\Api\CommissionController::class, 'Save']);

        /* ----- END Honorar ----- */


        /* ----- Buchungen ----- */

        // Buchungen löschen
        Route::delete('bookings', [\App\Http\Controllers\Api\BookingController::class, 'Delete']);

        // Buchungen anzeigen
        Route::get('bookings', [\App\Http\Controllers\Api\BookingController::class, 'Get']);

        Route::get('bookingdetails', [\App\Http\Controllers\Api\BookingController::class, 'ShowBookingDetails']);

        /* ----- END Buchungen ----- */


        /* ----- Auftragskorb ----- */

        // Auftragskorb löschen
        Route::delete('ordercarts', [\App\Http\Controllers\Api\OrderCartController::class, 'Delete']);

        // Auftragskorb anzeigen
        Route::get('ordercarts', [\App\Http\Controllers\Api\OrderCartController::class, 'Get']);

        /* ----- END Auftragskorb ----- */


        /* ----- Checkout ----- */

        // Checkout speichern
        Route::get('checkout', 'Api\CheckoutController@Save');

        /* ----- END Checkout ----- */

    });
});

// ----- END Dispo Online Transfer ----- */

