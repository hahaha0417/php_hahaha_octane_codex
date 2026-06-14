<?php

use App\Http\Controllers\backend\animal\hahaha_backend_animal_controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Log::channel('log_error')->info('Hello World');

    return view('welcome');
});

Route::get('/backend/animal', [hahaha_backend_animal_controller::class, 'Index'])->name('backend.animal');

require __DIR__.'/web/page.php';
require __DIR__.'/web/template.php';
require __DIR__.'/web/tool.php';
