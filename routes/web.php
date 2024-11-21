<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

Route::get('/', function () {
    return redirect('http://192.168.0.233:8000/collaborator/');
});
