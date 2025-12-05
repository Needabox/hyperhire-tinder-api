<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecommendedPeopleController;

Route::get('/people/recommended', RecommendedPeopleController::class);

