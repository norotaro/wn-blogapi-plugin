<?php

Route::namespace('Norotaro\BlogApi\Api\V1')
    ->prefix('api/norotaro/blogapi')
    ->group(function () {
        Route::prefix('posts')->group(function () {
            Route::get('/', 'Posts@index');
            Route::get('/{slug}', 'Posts@show');
        });
        Route::get('/categories', 'Categories@index');
    });
