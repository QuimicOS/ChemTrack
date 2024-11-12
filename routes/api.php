<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LaboratoryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





//Login function
Route::post('api/login', [AuthController::class, 'login']);

//Logout
Route::post('api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



////////////////////////////////////STATISTICS//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Route::get('/labels/last7days', [LabelController::class, 'countLabelsLast7Days']);
























//////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// Admin outes only///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////

Route::middleware(['auth:sanctum','admin'])->group(function () {


////////////////////////////////////////For users///////////////////////////////////////////

    //statitstics

    //FALTA retrieve new users created on the last 30 days


    ////Routes

    Route::post('/newUsers', [UserController::class, 'store']); //Create a new user has Admin only

    Route::get('/userEmail', [UserController::class, 'searchByEmail']); // get a user by email

    Route::put('userStatus/{id}',[UserController::class, 'authenticateUser']); // update the user status to Accepted, only an admin can do it

    Route::put('userInvalid/{id}',[UserController::class, 'invalidatesUser']); //// update the user status to Denied, only an admin can do it

    Route::get('/users/search', [UserController::class, 'searchCertifiedUsers']); // Admin get a list of users where certification status is TRUE

    // Route::put('editUserRoleManagement/{id}',[UserController::class, 'roleManagementEditUser']); // update the user room number and role only


    // //changes user role
    Route::put('usersRoleProfessor/{id}',[UserController::class, 'changeUserRoleProfessor']); //GOO
    Route::put('usersRoleAdmin/{id}',[UserController::class, 'changeUserRoleAdmin']); //GOO
    Route::put('usersRoleStaff/{id}',[UserController::class, 'changeUserRoleStaff']); //GOO




///////////////////////////////////For Labels/////////////////////////////////////

    Route::get('/getAdminLabels/{id}', action: [LabelController::class, 'show']);   // GET a label by ID

    Route::post('/newAdminLabel', [LabelController::class, 'createLabel']);   // POST create a new label
    

    Route::put('/updateAdminLabel/{id}', [LabelController::class, 'update']); // PUT update a label

    Route::put('/invalidLabel/{id}', [LabelController::class, 'invalidateLabel']);//not delete , but yes invalidate the label as admin




    /////statitstics


    // Route::get('/unwanted-material-summary', [LabelController::class, 'unwantedMaterialSummary']);// only admin can access this statistic, is giving errors because the querys


    Route::get('/labels/memorandum', [LabelController::class, 'memorandum']); // ONLY ADMIN , select label id , chemical name join with chemical table, and container capacity


    Route::get('labels/near-six-months',[LabelController::class, 'getLabelsNearSixMonths']);// get the accumulation start date and get the labes that are close of the 6 months (so 5 months or 5 1/2 months)


    ////change status of labels 
    Route::put('/activeLabel/{id}', [LabelController::class, 'updateLabelStatusActive']); 
    Route::put('/pendingLabel/{id}', [LabelController::class, 'updateLabelStatusPending']); 
    Route::put('/completedLabel/{id}', [LabelController::class, 'updateLabelStatusCompleted']); 










    //////////////////////////////////For Laboratories//////////////////////////////////////////
    Route::get('/labs', [LaboratoryController::class, 'getAllLabs']);  // GET all labs ONLY ADMIN

    Route::get('/labs/{lab_id}', [LaboratoryController::class, 'getLabDetails']);  // GET a lab by ID ONLY ADMIN

    Route::post('/labs', [LaboratoryController::class, 'addLab']); // CREATE LAB ONLY ADMIN
    
    Route::put('/editLabs/{lab_id}', [LaboratoryController::class, 'editlab']); // UPDATE LAB ONLY ADMIN
    
    Route::put('/invalidateLabs/{lab_id}', [LaboratoryController::class, 'invalidateLab']); //ONLY ADMIN, reality is that it will not be deleted but the lab status changed to INVALID, LAB HAS 3 STATUS (ASSIGNED, UNASSIGNED, INVALID)

    Route::put('/lab/{id}/supervisor', [LaboratoryController::class, 'assignLabSupervisor']); // ONLY ADMIN can assign a supervisor to a lab
    
    Route::get('/labs/room', [LaboratoryController::class, 'searchByRoomNumber']);  // GET a lab by Room Number

    

});















//////////////////////////////////////Professor only///////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

Route::middleware(['auth:sanctum','professor'])->group(function () {

    ////////////////////////////////User//////////////////////////////////////////////////////////

    Route::post('/professors/users', [UserController::class, 'createStaffUser']); //create a user with status = requested and role = staff
    

    Route::put('usersRoleStaffAsProf/{id}',[UserController::class, 'changeUserRoleStaff']); // for a already crated user, the professor can assigned a Staff role to it


    ///////////////////////////////Labels//////////////////////////////////////////
    Route::post('/newProfLabel', [LabelController::class, 'createLabel']); //professor can create a label

    Route::put('/pendingLabel/{id}', [LabelController::class, 'updateLabelStatusPending']); // professor can set label to pending


    Route::put('/updateProfLabels/{id}', [LabelController::class, 'update']); //professor can edit the label info

    Route::put('/invalidLabelAsProf/{id}', [LabelController::class, 'invalidateLabel']); //professor can invalidate a label but not delete it


    /////////////////////////////////////Laboratories////////////////////////////////////////////////////
    



});



/////////////////////////////////////////// Staff ONly////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

Route::middleware(['auth:sanctum','staff'])->group(function () {
    //Get label by the user id
    Route::post('/newStaffLabel', [LabelController::class, 'createLabel']); //Staff can create a label
    Route::put('/editLabelStaff/{id}', [LabelController::class, 'update']); // staff can edit a label created by that person only
    Route::put('/invalidLabelStaff/{id}', [LabelController::class, 'invalidateLabel']); // staff can invalidate a label created only  by that person

});