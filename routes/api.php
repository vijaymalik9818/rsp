<?php

use App\Http\Controllers\Apis\AdminPanelController;
use App\Http\Controllers\Apis\AgentController;
use App\Http\Controllers\Apis\LeadsController;
use App\Http\Controllers\Auth\LeadAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apis\PropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('properties', [PropertyController::class, 'index']);
Route::get('professionals', [PropertyController::class, 'professionals']);

Route::group(['prefix' => 'agents'], static function () {
    Route::controller(AgentController::class)->group(function () {
        Route::get('/list', 'index');
        Route::get('/language-and-position', 'getDistinctLanguageAndPositions');
        Route::get('/detail/{mls_id}', 'detail');
        Route::post('/store-review', 'storeReview');
        Route::get('/get-reviews/{id}', 'getReviews');
        Route::get('/get-avg-rating/{id}', 'getAverageRating');
        Route::get('/get-name-mls', 'autoSuggestMlsAgentName');
        Route::post('/contact-us-form', 'storeContactUs');
        Route::post('/join-rep-form', 'storeJoinRep');
        Route::post('/listing-appointment-form', 'storeListingAppointment');
        Route::post('/listing-autosuggestion', 'listingAutosuggestionBar');
        Route::get('/listing-agent-properties/{id}', 'getAgentProperties');
        Route::get('/listing-properties', 'getProperties');
        Route::get('/state-city-subdivision', 'getStateCitiesWithSubdivision');
       
        Route::get('/get-properties-index', 'getPropertiesIndex');
        Route::get('/get-properties-featured', 'getFeaturedProperties');
        Route::get('/get-properties-diamond', 'getDiamondProperties');
        Route::get('/get-properties-type', 'getPropertiesType');
        Route::get('/property-details/{slug_url}', 'PropertyDetail');
        Route::post('/get-advance-data', 'getadvanceData');
        Route::get('/property-type', 'propertyType');
        Route::post('/listing-autosuggestion-map', 'autoList');
        Route::get('/get-staff', 'getStaff');
        Route::get('/get-community', 'getCommunity');
        Route::get('/get-locations', 'getLocations');
        Route::get('/get-cities-subdivision', 'getCitiesWithSubdivisions');
        Route::get('/get-top-count', 'gettopPropertyCount');

        
        
        
        
        
        //call the api
        Route::post('/save-data', 'submitForm');
        Route::post('/similar-listings', 'getSimilarListings');
        Route::post('/property-review', 'propertyreview');
        Route::post('/agent-slug', 'agentslug');
        Route::get('/getproperty-reviews', 'getpropertyreviews');
        Route::get('/save-search', 'getsearchresult');
        Route::post('/forgot-password', 'forgotpassword');
        Route::post('/reset-password', 'resetpassword');
        
    });
    Route::controller(LeadsController::class)->group(function () {
        Route::post('/leads-info', 'profileEdit');
        Route::post('/update-profile/{id}', 'updateProfile');
        Route::get('/getsavesearch/{id}', 'getsavesearch');
        Route::post('add-to-fav', 'addToFavorites');
        Route::get('get-fav', 'getUserFavoriteProperties');
        Route::post('searchdelete', 'searchdelete');
        Route::get('getlistings', 'getfavoritelisting');
    });
});


Route::group(['prefix' => 'lead'], static function () {
    Route::controller(LeadAuthController::class)->group(function () {
        
        //Leads
        
        Route::post('login', 'login');
   
        Route::post('register', 'register');
        Route::post('update-password/{id}', 'updatePassword');
        Route::post('sign-up', 'signup');
        Route::post('socialsign-up', 'socialsignup');
        Route::post('check-email', 'checkemail');
        Route::get('getlogininfo/{id}', 'getlogininfo');

        
    });
});


Route::group(['prefix' => 'admin'], static function () {
    Route::controller(AdminPanelController::class)->group(function () {
        //admin panel
        Route::post('/updatecity', [AdminPanelController::class, 'updateCity']);
        Route::post('/updatecitystatus',[AdminPanelController::class,'updatecitystatus']);
    });
});
      
Route::middleware('lead:api')->group(function () {
    Route::get('getuser',[LeadAuthController::class, 'getdetails']);
    Route::post('logout', [LeadAuthController::class, 'logout']);
    Route::get('/get-property-details/{slug_url}',[AgentController::class,  'getPropertyDetail']);
});