<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\pickupRequestController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\chemicalController;




Route::get('/', function () {
    return view('home');
});

/// LOGIN ENDPOINT
Route::get('/saml/login', function () {
    // Redirect to the actual Sign-On URL provided by  IdP
    return redirect('http://chemtrack.test/saml2/de46364b-e680-400d-97f6-e7416066552b/login'); // Replace with the actual URL
})->name('saml.login');


// web.php





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

Route::get('admin/manageChemical', function () {
    return view('admin/manageChemical');
})->name('admin/manageChemical');

Route::get('admin/roleManagement', function () {
    return view('admin/roleManagement');
})->name('admin/roleManagement');

Route::get('admin/unwantedMaterialSummary', function () {
    return view('admin/unwantedMaterialSummary');
})->name('admin/unwantedMaterialSummary');

Route::get('admin/unwantedMaterialMemorandum', function () {
    return view('admin/unwantedMaterialMemorandum');
})->name('admin/unwantedMaterialMemorandum');

Route::get('admin/manageLaboratories', function () {
    return view('admin/manageLaboratories');
})->name('admin/manageLaboratories');

Route::get('admin/manageQuiz', function () {
    return view('admin/manageQuiz');
})->name('admin/manageQuiz');


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

Route::get('professor/roleRequest', function () {
    return view('professor/roleRequest');
})->name('professor/roleRequest');

Route::get('professor/addChemical', function () {
    return view('professor/addChemical');
})->name('professor/addChemical');

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

Route::get('staff/addChemical', function () {
    return view('staff/addChemical');
})->name('staff/addChemical');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/laboratories', [LaboratoryController::class, 'getAllLabs']);
Route::get('/laboratories/{room_number}', [LaboratoryController::class, 'getLabByRoomNumber']);

// Chemical Routes
//Route::get('/chemicals', [ChemicalController::class, 'getAllChemicals']);
Route::get('/chemicals/{chemical_name}', [ChemicalController::class, 'getCASNumberByChemicalName']);
Route::get('/chemicals', [ChemicalController::class, 'index']);


// Content Routes (For adding chemicals with percentages to a label)
//Route::post('/contents', [ContentController::class, 'store']);

// Label Routes
Route::post('/labels', [LabelController::class, 'store'])->name('labels.store');
Route::post('/contents', [ContentController::class, 'store'])->name('contents.store');







Route::get('/labels/{id}', [LabelController::class, 'show']);

Route::post('/createPickupRequest', [PickupRequestController::class, 'createPickupRequest']);

Route::get('/getPickupRequests', [PickupRequestController::class, 'getAllPickupRequests']);

Route::put('/pickupInvalidate', [PickupRequestController::class, 'invalidatePickupRequest']);

Route::get('/pickupSearch', [PickupRequestController::class, 'searchPickupRequests']);

Route::put('/pickupComplete', [PickupRequestController::class, 'completePickupRequest']);

// Route::middleware(['web', 'auth'])->group(function () {
//     Route::put('/editLabel/{id}', [LabelController::class, 'updateLabel'])->name('editLabel');
// });

Route::put('/editLabel/{id}', [LabelController::class, 'updateLabel'])->name('editLabel');



//Statistics for Admin dashboard


Route::get('/labels/last7days', [LabelController::class, 'countLabelsLast7Days']);
Route::get('/labels/weight', [LabelController::class, 'calculateTotalWeight']); 
Route::get('/labels/volume', [LabelController::class, 'calculateTotalVolume']); 
////////////////////////////////////////////////////////////////////////////////////////////





///////////////////////////////////////////////////////////////////////////////////////////

Route::get('/users/new-members', [UserController::class, 'countNewMembersLast30Days']);
// Route::get('/users', [UserController::class, 'getUserDetails']);
////////////////////////////////////////////////////////////////////////////////////////////////











//For EDIT LABEL
Route::get('/label/{id}', [LabelController::class, 'searchLabelById']); 

//Route::post('/editLabel/{id}', [LabelController::class, 'updateLabel'])->withoutMiddleware('auth');






//search label
Route::get('/label/{id}', [LabelController::class, 'searchLabelById']); 











//Summary
Route::get('/unwanted-material-summary', [LabelController::class, 'unwantedMaterialSummary']);








//For INVALIDATE LABEL

Route::put('/invalid/{id}', action: [LabelController::class, 'invalidateLabel']); 


require __DIR__.'/auth.php';
