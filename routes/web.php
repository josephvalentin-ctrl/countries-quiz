<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;

Route::get('/', [QuizController::class, 'index'])
    ->name('index');

Route::get('/post-answer', [QuizController::class, 'postAnswer'])
    ->name('post.answer');
