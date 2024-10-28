<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabelController;


Route::get('/', function () {
    return view('home');
});

//Route::get('/labels/search', [LabelController::class, 'searchLabel']);


////////////////////////////////DEFAULT ROUTES//////////////////////////////////////////////

Route::get('home', function () {
    return view('home');
})->name('home');

Route::get('aboutUs', function () {
    return view('aboutUs');
})->name('aboutUs');

Route::get('contactUs', function () {
    return view('contactUs');
})->name('contactUs');

Route::get('loginTemp', function () {
    return view('loginTemp');
})->name('loginTemp');

////////////////////////////////ADMIN ROUTES//////////////////////////////////////////////

Route::get('admin/homeAdmin', function () {
    return view('admin/homeAdmin');
})->name('admin/homeAdmin');

Route::get('admin/notifications', function () {
    return view('admin/notifications');
})->name('admin/notifications');

Route::get('admin/aboutUs', function () {
    return view('admin/aboutUs');
})->name('admin/aboutUs');

Route::get('admin/contactUs', function () {
    return view('admin/contactUs');
})->name('admin/contactUs');

Route::get('admin/help', function () {
    return view('admin/help');
})->name('admin/help');

Route::get('admin/searchLabel', function () {
    return view('admin/searchLabel');
})->name('admin/searchLabel');

Route::get('admin/createLabel', function () {
    return view('admin/createLabel');
})->name('admin/createLabel');

Route::get('admin/editLabel', function () {
    return view('admin/editLabel');
})->name('admin/editLabel');

Route::get('admin/invalidLabel', function () {
    return view('admin/invalidLabel');
})->name('admin/invalidLabel');

Route::get('admin/pickupRequest', function () {
    return view('admin/pickupRequest');
})->name('admin/pickupRequest');

Route::get('admin/invalidPickup', function () {
    return view('admin/invalidPickup');
})->name('admin/invalidPickup');

Route::get('admin/pickupHistorial', function () {
    return view('admin/pickupHistorial');
})->name('admin/pickupHistorial');

////////////////////////////////PROFESSOR ROUTES//////////////////////////////////////////////

Route::get('professor/homeProfessor', function () {
    return view('professor/homeProfessor');
})->name('professor/homeProfessor');

Route::get('professor/notifications', function () {
    return view('professor/notifications');
})->name('professor/notifications');

Route::get('professor/aboutUs', function () {
    return view('professor/aboutUs');
})->name('professor/aboutUs');

Route::get('professor/contactUs', function () {
    return view('professor/contactUs');
})->name('professor/contactUs');

Route::get('professor/help', function () {
    return view('professor/help');
})->name('professor/help');

Route::get('professor/searchLabel', function () {
    return view('professor/searchLabel');
})->name('professor/searchLabel');

Route::get('professor/createLabel', function () {
    return view('professor/createLabel');
})->name('professor/createLabel');

Route::get('professor/editLabel', function () {
    return view('professor/editLabel');
})->name('professor/editLabel');

Route::get('professor/invalidLabel', function () {
    return view('professor/invalidLabel');
})->name('professor/invalidLabel');

Route::get('professor/pickupRequest', function () {
    return view('professor/pickupRequest');
})->name('professor/pickupRequest');

Route::get('professor/invalidPickup', function () {
    return view('professor/invalidPickup');
})->name('professor/invalidPickup');


////////////////////////////////STAFF ROUTES//////////////////////////////////////////////

Route::get('staff/homeStaff', function () {
    return view('staff/homeStaff');
})->name('staff/homeStaff');

Route::get('staff/notifications', function () {
    return view('staff/notifications');
})->name('staff/notifications');

Route::get('staff/aboutUs', function () {
    return view('staff/aboutUs');
})->name('staff/aboutUs');

Route::get('staff/contactUs', function () {
    return view('staff/contactUs');
})->name('staff/contactUs');

Route::get('staff/help', function () {
    return view('staff/help');
})->name('staff/help');

Route::get('staff/searchLabel', function () {
    return view('staff/searchLabel');
})->name('staff/searchLabel');

Route::get('staff/createLabel', function () {
    return view('staff/createLabel');
})->name('staff/createLabel');

Route::get('staff/editLabel', function () {
    return view('staff/editLabel');
})->name('staff/editLabel');

Route::get('staff/invalidLabel', function () {
    return view('staff/invalidLabel');
})->name('staff/invalidLabel');

Route::get('staff/pickupRequest', function () {
    return view('staff/pickupRequest');
})->name('staff/pickupRequest');

Route::get('staff/invalidPickup', function () {
    return view('staff/invalidPickup');
})->name('staff/invalidPickup');




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
