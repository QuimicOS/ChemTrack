<?php

use App\Http\Controllers\ProfileController;
use App\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\pickupRequestController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\chemicalController;
use App\Http\Controllers\ManageQuizController;
use App\Http\Controllers\QuizController;



Route::get('/', function () {
    return view('home');
});


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////--------------------------------- Login Validations ------------------------------------------------/////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



Route::get('auth/saml2/arrival', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('auth.saml.login');
    }



    // IF a user tries to enter the system BEFORE a Professor or Admin gaves authorization.
    if($user->user_status === NUll ||$user->certification_status === NULL ||$user->role === NULL ){ //Ruta de acceso denegado
        return redirect()->route('accessDenied');
    }

    // Check  User Status
    if($user->user_status === 'Denied'){
        return redirect()->route('home'); // Ruta de Acceso denegado
    }


    // Check Certification Status and User Status
    if ($user->certification_status === false) {
        return redirect()->route('notice'); // Redirect to notice
    }

    // Redirect based on role if Certification Status is TRUE and User Status is Accepted
    if ($user->certification_status === true && $user->user_status === 'Accepted') {
        switch ($user->role) {
            case 'Administrator':
                return redirect()->route('admin/homeAdmin');
            case 'Professor':
                return redirect()->route('professor/homeProfessor');
            case 'Staff':
                return redirect()->route('staff/homeStaff');
            default:
                return redirect()->route('home'); // Fallback to main homepage
        }
    }
}); 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////








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

Route::get('accessDenied', function () {
    return view('accessDenied');
})->name('accessDenied');
/////////////////////////////////////////////////////////////////////////////////////////////////






////////////////////////////////ADMIN ROUTES//////////////////////////////////////////////
// Route::middleware(['auth', 'admin'])->group(function(){

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

    // Route::get('admin/manageQuiz', function () {
    //     return view('admin/manageQuiz');
    // })->name('admin/manageQuiz');




    //Statistics for Admin dashboard
    Route::get('/labels/last7days', [LabelController::class, 'countLabelsLast7Days']);
    Route::get('/labels/weight', [LabelController::class, 'calculateTotalWeight']); 
    Route::get('/labels/volume', [LabelController::class, 'calculateTotalVolume']); 
    Route::get('/users/new-members', [UserController::class, 'countNewMembersLast30Days']);
    Route::get('/pickup-requests/pending', [PickupRequestController::class, 'countPendingPickupRequests']);
    Route::get('/chemicalCreatedCount', [ChemicalController::class, 'chemicalsMadeThisMonth']); 





    //Search Label by ID
    Route::get('/label/{id}', [LabelController::class, 'searchLabelById']); 

    //For Create Label has ADMIN


    //For EDIT LABEL
    Route::get('/label/{id}', [LabelController::class, 'searchLabelById']); 






    ////For Role Managemnt
    Route::delete('/users/{id}', [UserController::class, 'deleteUserById']);

    Route::post('/newUsers', [UserController::class, 'createUser']); //Create a new user has Admin only GOODIE

    Route::get('/users/search/{email}', [UserController::class, 'searchUserByEmail']); //GOODIE

    Route::get('/users/certified', [UserController::class, 'getCertifiedUsers']); // // Admin get a list of users where certification status is TRUE

    Route::get('/users/requested', [UserController::class, 'getRequestedUsers']); // user_status requested

    Route::put('userInvalid/{id}',[UserController::class, 'invalidatesUser']); // status to denied

    Route::put('userStatus/{id}',[UserController::class, 'authenticateUser']); // update the user status to Accepted, only an admin can do it

    Route::get('/users/{id}', [UserController::class, 'getUserDetailsByID']);

    Route::put('/users/{id}', [UserController::class, 'roleManagementEditUser']);// update the user room number and role only



    //Summary
    Route::get('/unwanted-material-summary', [LabelController::class, 'unwantedMaterialSummary']);

    //Memorandum
    Route::get('/memorandum', [LabelController::class, 'memorandum']);

    //For INVALIDATE LABEL
    Route::put('/invalid/{id}', action: [LabelController::class, 'invalidateLabel']); 
    
    //For Laboratories
    Route::get('/labs', [LaboratoryController::class, 'getAllLabs']);  // GET all labs ONLY ADMIN
    
    Route::get('/labs/room', [LaboratoryController::class, 'searchByRoomNumber']);

    Route::get('/labs/{lab_id}', [LaboratoryController::class, 'getLabDetails']);  // GET a lab by ID ONLY ADMIN

    Route::post('/labs', [LaboratoryController::class, 'addLab']); // CREATE LAB ONLY ADMIN

    Route::put('/editLabs/{lab_id}', [LaboratoryController::class, 'editlab']); // UPDATE LAB ONLY ADMIN

    Route::put('/invalidateLabs/{lab_id}', [LaboratoryController::class, 'invalidateLab']); //ONLY ADMIN, reality is that it will not be deleted but the lab status changed to INVALID, LAB HAS 3 STATUS (ASSIGNED, UNASSIGNED, INVALID)

    Route::put('/lab/{id}/supervisor', [LaboratoryController::class, 'assignLabSupervisor']); // ONLY ADMIN can assign a supervisor to a lab


    ////   Quiz Management Routes
    Route::get('admin/manageQuiz', [ManageQuizController::class, 'show'])->name('admin.manageQuiz.show');
    Route::post('admin/manageQuiz/save', [ManageQuizController::class, 'save'])->name('admin.manageQuiz.save');




// });















////////////////////////////////PROFESSOR ROUTES//////////////////////////////////////////////
// Route::middleware(['auth', 'professor'])->group(function(){

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


    Route::post('/professors/users', [UserController::class, 'createStaffUser']); //create a user with status = requested and role = staff


// });














////////////////////////////////STAFF ROUTES//////////////////////////////////////////////

Route::middleware(['auth', 'staff'])->group(function(){

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



});
















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

Route::put('/editLabel/{id}', [LabelController::class, 'updateLabel'])->name('editLabel');



////////////////////////////////////////////////////////////////////////////////////////////////



// -------------------------------------Notification Routes--------------------------------------------------
Route::put('/notificationRead', action: [NotificationController::class, 'markAsRead']);
Route::get('/notificationAdminActives', action: [NotificationController::class, 'adminGetUnreadNotifications']);
Route::get('/notificationAdminRead', action: [NotificationController::class, 'adminGetReadNotifications']);
Route::get('/notificationAdminOverdues', action: [NotificationController::class, 'adminGetOverdueNotifications']);
Route::get('/notificationGetToDo', action: [NotificationController::class, 'getToDo']);
Route::get('/create5Months', action: [LabelController::class, 'getValidLabels']);

Route::get('/todoList', action: [NotificationController::class, 'todoList']);
Route::post('/checkPickupRequest', [LabelController::class, 'checkPickupRequest']);
Route::get('/notificationUnreadCount', action: [NotificationController::class, 'unreadNotificationsCount']);
Route::get('/notifications/types', [NotificationController::class, 'getNotificationTypes']);

// ------------------------------------------------------------------------------------------------------





// -------------------------------------Chemical Routes--------------------------------------------------
Route::post('/chemicalCreate', [ChemicalController::class, 'addChemical']); 
Route::put('/chemicalInvalidate', [ChemicalController::class, 'deleteChemical']); 
Route::put('/chemicalModify', [ChemicalController::class, 'editChemical']); 
Route::get('/chemicalCasNumber', [ChemicalController::class, 'getCasNumber']); 
Route::get('/chemicalSearch', [ChemicalController::class, 'searchChemicalName']);
// ------------------------------------------------------------------------------------------------------


//////////////////////////////////For Laboratories//////////////////////////////////////////
   
Route::get('/labs', [LaboratoryController::class, 'getAllLabs']);  // GET all labs ONLY ADMIN
Route::get('/labs/room', [LaboratoryController::class, 'searchByRoomNumber']);

Route::get('/labs/{lab_id}', [LaboratoryController::class, 'getLabDetails']);  // GET a lab by ID ONLY ADMIN

Route::post('/labs', [LaboratoryController::class, 'addLab']); // CREATE LAB ONLY ADMIN

Route::put('/editLabs/{lab_id}', [LaboratoryController::class, 'editlab']); // UPDATE LAB ONLY ADMIN

Route::put('/invalidateLabs/{lab_id}', [LaboratoryController::class, 'invalidateLab']); //ONLY ADMIN, reality is that it will not be deleted but the lab status changed to INVALID, LAB HAS 3 STATUS (ASSIGNED, UNASSIGNED, INVALID)

Route::put('/lab/{id}/supervisor', [LaboratoryController::class, 'assignLabSupervisor']); // ONLY ADMIN can assign a supervisor to a lab

















// //changes user role
Route::put('usersRoleProfessor/{id}',[UserController::class, 'changeUserRoleProfessor']); //GOO
Route::put('usersRoleAdmin/{id}',[UserController::class, 'changeUserRoleAdmin']); //GOO
Route::put('usersRoleStaff/{id}',[UserController::class, 'changeUserRoleStaff']); //GOO





/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
















//////////////////////////////////////////////////////////////////////////////////
////////////////////////////// Quiz //////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

Route::get('/notice', function () {
    return view('notice');
})->name('notice');

Route::get('/training', function () {
    return view('training');
})->name('training');

Route::get('/quiz', function () {
    return view('quiz');
})->name('quiz');


//// Quiz Routes for Users
Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

Route::post('/update-certification-status', [UserController::class, 'updateCertificationStatus'])->name('update.certificate');


// //////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////














require __DIR__.'/auth.php';
require __DIR__.'/saml2.php';

