<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

// Routes redirection sur dashboard après inscription
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', function () {
    return view('homepage');
});


// Routes concernant l'affichage des joueurs NBA en liste (index) et individuels (show)
Route::prefix('nba')
    ->middleware('auth')
    ->name('nba.')
    ->group(function () {
        Route::resource('/', 'PlayerController');
    });


// Routes concernant l'affichage des ligues en portail (index), en liste (public) et individuelles (show)
Route::resource('leagues', 'LeagueController')->middleware('auth');
Route::get('public', 'LeagueController@publicLeagues')->name('leagues.public')->middleware('auth');
Route::post('joinPrivateLeague', 'LeagueController@joinPrivateLeague')->name('leagues.joinPrivateLeague')->middleware('auth');
Route::get('joinPublicLeague/{id}', 'LeagueController@joinPublicLeague')->name('leagues.joinPublicLeague')->middleware('auth');


// Routes concernant l'affichage de la draft
Route::prefix('draft')
    ->middleware('auth')
    ->middleware('draft')
    ->name('draft.')
    ->group(function () {
        Route::post('confirmDraft/{forwards}{guards}{centers}', 'DraftController@confirmDraft')->name('confirm');
        Route::post('auction/{id}', 'DraftController@auction')->name('auction');
        Route::post('updateAuction/{id}', 'DraftController@updateAuction')->name('auction.updateValue');
        Route::delete('deleteAuction/{id}', 'DraftController@deleteAuction')->name('delete.auction');
        Route::resource('/', 'DraftController');
    });

// Routes concernant l'affichage du dashboard
Route::resource('dashboard', 'DashboardController')->Middleware('auth');


// Routes concernant les équipes
Route::resource('teams', 'TeamController')->middleware('auth');

// Routes concernant l'affichage du match
Route::prefix('match')
    ->middleware('auth')
    ->name('match.')
    ->group(function () {
        Route::resource('/', 'MatchController');
        Route::Get('deletePlayer/{id}', 'MatchController@deletePlayer')->name('delete.player');
    });

//Route pour la page tuto
Route::get('/tuto/', function () {
    return view('tuto');
});

//Route pour la page mentions légales
Route::get('/mentions_legales', function () {
    return view('mentions_legales');
});


// Route concernant le footer
Route::get('/contact', 'ContactController@index')->name('contact');


