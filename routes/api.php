<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecommendedPeopleController;
use App\Http\Controllers\Api\LikeDislikeController;

Route::prefix('v1')->group(function () {
    Route::get('/people/recommended', RecommendedPeopleController::class);
    Route::get('/people/{user_id}/liked-list', [LikeDislikeController::class, 'likedList']);

    Route::middleware('device')->group(function () {
        Route::post('/people/{user_id}/like', [LikeDislikeController::class, 'like']);
        Route::post('/people/{user_id}/dislike', [LikeDislikeController::class, 'dislike']);
    });
});

