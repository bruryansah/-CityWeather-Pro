<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\WeatherController;

Route::get('/', [WeatherController::class, 'index'])->name('home');
Route::get('/search-weather', [WeatherController::class, 'search'])->name('search.weather');

Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');

Route::post('/ai-recommendation', [WeatherController::class, 'aiRecommendation'])->name('ai.recommendation');
