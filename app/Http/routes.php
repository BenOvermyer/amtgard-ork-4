<?php

Route::get( '/', [ 'as' => 'home', 'uses' => 'HomeController@index' ] );

Route::group( [ 'prefix' => 'event' ], function () {
    Route::get( '/{id}', [ 'as' => 'event.show', 'uses' => 'EventController@show' ] );
} );

Route::group( [ 'prefix' => 'kingdom' ], function () {
    Route::get( '/{id}', [ 'as' => 'kingdom.show', 'uses' => 'KingdomController@show' ] );
} );

Route::group( [ 'prefix' => 'park' ], function () {
    Route::get( '/{id}', [ 'as' => 'park.show', 'uses' => 'ParkController@show' ] );
} );

Route::group( [ 'prefix' => 'player' ], function () {
    Route::get( '/{id}', [ 'as' => 'player.show', 'uses' => 'PlayerController@show' ] );
    Route::get( '/search/{query}', [ 'as' => 'player.search', 'uses' => 'PlayerController@search' ] );
} );

Route::group( [ 'prefix' => 'unit' ], function () {
    Route::get( '/{id}', [ 'as' => 'unit.show', 'uses' => 'UnitController@show' ] );
} );

Route::group( [ 'prefix' => 'reports' ], function () {
    Route::get( '/newbies-for-month/{year}/{month}', [ 'as' => 'reports.newbiesForMonth', 'uses' => 'ReportController@newbiesForMonth' ] );
} );

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group( [ 'middleware' => [ 'web' ] ], function () {

} );
