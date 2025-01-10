<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Agent\AgentController;
use App\Http\Controllers\Admin\Agent\ReviewController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LeadAuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RETSController;
use App\Http\Controllers\Admin\Agent\LeaddataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//test

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::match(['get', 'post'],'/signin', [AuthController::class, 'signin'])->name('signin');
Route::delete('/delete-agent/{id}', [AgentController::class, 'deleteagents'])->name('delete-agents');
Route::post('/store-agent', [AgentController::class, 'store'])->name('store-agent');
Route::post('/store-staff', [AgentController::class, 'storestaff'])->name('store-staff');
Route::get('/unauthorized', [AuthController::class, 'faileduser'])->name('faileduser');

Route::get('/agents', [AgentController::class, 'showAgents'])->name('show-agents')->middleware('auth');
Route::get('/staff', [AgentController::class, 'showStaff'])->name('show-staff')->middleware('auth');
Route::get('/agents/{id}', [AgentController::class, 'editagents'])->name('edit-agents')->middleware('auth');
Route::get('/update', [AgentController::class, 'updateAgent'])->name('update-agents');
Route::get('/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot');
Route::post('/forgot/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::get('/reset-passwords/{token}', [LeadAuthController::class, 'showResetfrontForm'])->name('pass.resets');
Route::post('/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::post('/resetpass', [LeadAuthController::class, 'resets'])->name('password.updates');
Route::match(['get', 'post'],'/agent-details/{encodedString}', [AgentController::class, 'agentview'])->name('agent.details')->middleware('auth');
Route::get('/getautosuggestion', [AgentController::class, 'getautosuggestion'])->name('getautosuggestion');
Route::get('/getautosuggestionstaff', [AgentController::class, 'getautosuggestionstaff'])->name('getautosuggestionstaff');
Route::get('/getUsersByStatus', [AgentController::class, 'getUsersByStatus'])->name('getUsersByStatus');
Route::get('/getagents', [AgentController::class, 'getagents'])->name('getagents');
Route::post('/check-email', [AgentController::class, 'getUsersemail'])->name('getUsersemail');
Route::get('/deleted-agents', [AgentController::class, 'deletedAgents'])->name('deletedAgents');
Route::get('agent-details/edit/{id}', [AgentController::class, 'edit'])->name('agent.edit')->middleware('auth');
Route::get('/get-agents-filtered', [AgentController::class, 'getAgentsFiltered'])->name('getAgentsFiltered');
Route::get('/agent-details/reviews/{id}', [AgentController::class, 'ratingview'])->name('ratingview');
Route::post('/review/confirm/{id}', [AgentController::class, 'reviewconfirm'])->name('reviewconfirm');

//contactus route
Route::get('/contactus', [AgentController::class, 'contactusView'])->name('contactusView')->middleware('auth');
Route::get('/getAutoSuggestionContactus', [AgentController::class, 'getAutoSuggestionContactus'])->name('getAutoSuggestionContactus');
Route::get('/getautoquerycontactus', [AgentController::class, 'getAutoQueryContactUs'])->name('getAutoQueryContactUs');
Route::get('/getContactUsDateData',[AgentController::class, 'getContactUsDateData'])->name('getContactUsDateData');
Route::get('/getcontactusdata', [AgentController::class, 'getContactUsData'])->name('getcontactusdata');
Route::get('/getAutosearchQueryContactUs', [AgentController::class, 'getAutosearchQueryContactUs'])->name('getAutosearchQueryContactUs');

//joinrep route
Route::get('/joinrep', [AgentController::class, 'joinrepview'])->name('joinrepview')->middleware('auth');
Route::get('/getjoinrepdata', [AgentController::class, 'getJoinRepData'])->name('getjoinrepdata');
Route::get('/getAutoSuggestionJoinrep', [AgentController::class, 'getAutoSuggestionJoinrep'])->name('getAutoSuggestionJoinrep');
Route::get('/getAutoqueryJoinrep', [AgentController::class, 'getAutoQueryJoinrep'])->name('getAutoQueryJoinrep');
Route::get('/getJoinRepDateData',[AgentController::class, 'getJoinRepDateData'])->name('getJoinRepDateData');
Route::get('/getentersearchjoinrep', [AgentController::class, 'getentersearchjoinrep'])->name('getentersearchjoinrep');

//city route
Route::get('/allcity', [AgentController::class, 'allcityView'])->name('allcity');
Route::get('/getcityData', [AgentController::class, 'getcityData'])->name('getcityData');
Route::get('/getAutoSuggestionCity', [AgentController::class, 'getAutoSuggestionCity'])->name('getAutoSuggestionCity');
Route::get('/getAutoqueryCity', [AgentController::class, 'getAutoQueryCity'])->name('getAutoQueryCity');
Route::get('/getAutoQuerysearchCity', [AgentController::class, 'getAutoQuerysearchCity'])->name('getAutoQuerysearchCity');
Route::get('/city/{id}', [AgentController::class, 'editcity'])->name('editcity');
// Route::post('/updatecity', [AgentController::class, 'updateCity'])->name('updatecity');
Route::get('/prefill-data/{id}', [AgentController::class, 'getPrefillData'])->name('prefill-data');
// Route::post('/updatecitystatus',[AgentController::class,'updatecitystatus'])->name('updatecitystatus');
Route::get('/getfeaturedproperty', [AgentController::class, 'getfeaturedproperty'])->name('getfeaturedproperty');

//propertyreviews route
Route::get('/propertyreviews', [AgentController::class, 'showPropertyReviews'])->name('propertyreviews');
Route::get('/getPropertyReviewsdata', [AgentController::class, 'getPropertyReviewsData'])->name('getPropertyReviewsdata');
Route::get('/getAutoSuggestionPropertyReview', [AgentController::class, 'getAutoSuggestionPropertyReview'])->name('getAutoSuggestionPropertyReview');
Route::get('/getAutoQueryPropertyReview', [AgentController::class, 'getAutoQueryPropertyReview'])->name('getAutoQueryPropertyReview');
Route::get('/getsearchautoquerypropertyreview', [AgentController::class, 'getsearchautoquerypropertyreview'])->name('getsearchautoquerypropertyreview');


//tour route
Route::get('/tour', [AgentController::class, 'tourView'])->name('tour');
Route::get('/gettourdata', [AgentController::class, 'gettourData'])->name('gettourdata');
Route::get('/getAutoSuggestionTour', [AgentController::class, 'getAutoSuggestionTour'])->name('getAutoSuggestionTour');
Route::get('/getAutoQuerytour', [AgentController::class, 'getAutoQueryTour'])->name('getAutoQuerytour');
Route::get('/getsearchautoquerytour', [AgentController::class, 'getsearchautoquerytour'])->name('getsearchautoquerytour');


Route::get('/getTourDateData',[AgentController::class, 'getTourDateData'])->name('getTourDateData');


//this is backend route for mls properties
Route::get('/getpropertysuggestion/{id}', [AgentController::class, 'getpropertysuggestion'])->name('getpropertysuggestion');
Route::get('/getpropertyquery/{id}', [AgentController::class, 'getpropertyquery'])->name('getpropertyquery');
Route::get('/properties/{id}', [AgentController::class, 'showproperties'])->name('properties');
Route::get('/active-properties', [RETSController::class, 'retrieveData']);
Route::get('/retrieveRoomData', [RETSController::class, 'retrieveRoomData']);


Route::get('/member-properties', [RETSController::class, 'retrieveMemberData']);
Route::get('/slug-properties', [RETSController::class, 'updateSlugUrlsForProperties']);
Route::get('/getautoquery', [AgentController::class, 'getAutoQuery'])->name('getautoquery');
Route::get('/getautoquerystaff', [AgentController::class, 'getAutoQuerystaff'])->name('getautoquerystaff');

//ticket routes
Route::get('/ticket', [AgentController::class, 'showticketform'])->name('ticket.page');
Route::post('/submit-ticket', [AgentController::class, 'submitTicket'])->name('submit.ticket');
Route::get('/edit-profile/{id}', [AgentController::class, 'profileEdit'])->name('profile.edit');
Route::post('/edit-personal-profile', [AgentController::class, 'personalProfileEdit'])->name('profile.personaledit');



//emailsending

Route::get('/emailsending', [AgentController::class, 'emailsending'])->name('emailsending');
Route::post('/restore-agent/{id}', [AgentController::class, 'restore'])->name('agents.restore');

Route::get('/leads', [LeaddataController::class, 'showleads'])->name('show-leads')->middleware('auth');
Route::get('/getleads', [LeaddataController::class, 'getleads'])->name('get-leads');
Route::get('/getautosuggestionleads', [LeaddataController::class, 'getautosuggestionleads'])->name('getautosuggestionleads');
Route::get('/getautoqueryleads', [LeaddataController::class, 'getautoqueryleads'])->name('getautoqueryleads');