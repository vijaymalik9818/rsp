<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyCollection;
use App\Http\Resources\UserCollection;
use App\Models\ContactUs;
use App\Models\JoinRep;
use App\Models\ListingAppointment;
use App\Models\Properties;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SendGrid;
use App\Mail\ThankYouEmail;
use App\Models\Leads;
use App\Models\PropertyReview;
use App\Models\SavedSearch;
use App\Models\Tour;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use App\Models\CityProperty;
use Illuminate\Support\Facades\Response;
use Mailgun\Mailgun;
use Log;

// use SendGrid;
// use App\Models\Tour;

class AgentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'paginate' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors(), 'Validation Error');
        }

        $perPage = $request->input('per_page', 12);
        $sort = 'asc';

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
        }
        $query = User::whereNull('role')->where('status', 1);

        if ($request->filled('search')) {
            $searchKey = $request->input('search');
            $query->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', '%' . $searchKey . '%')
                    ->orWhere('mls_id', 'like', '%' . $searchKey . '%');
            });
        }


        if ($request->filled('language')) {
            $languages = explode(',', $request->input('language')); // Split language string into an array
            $query->where(function ($query) use ($languages) {
                foreach ($languages as $language) {
                    $query->orWhere('language', 'like', '%' . trim($language) . '%'); // OrWhere for multiple languages
                }
            });
        }
        if ($request->filled('position')) {
            $position = $request->input('position');
            $query->where('specialisation', $position);
        }

        if ($request->filled('office_key')) {
            $office_key = $request->input('office_key');
            $query->where('office_key', $office_key);
        }




        $query->orderBy('name', $sort);

        $count = $query->count();

        $queryResult = $request->boolean('paginate')
            ? $query->paginate($perPage)->appends($request->query())
            : $query->simplePaginate($perPage)->appends($request->query());

        return (new UserCollection($queryResult))->additional([
            'total_count' => $count,
        ]);
    }


    public function getDistinctLanguageAndPositions()
    {
        $distinctLanguage = User::whereNotNull('language')->distinct()->orderBy('language', 'asc')->pluck('language');
        $distinctPositions = User::whereNotNull('specialisation')->distinct()->orderBy('specialisation', 'asc')->pluck('specialisation');

        // $distinctLanguage = config('languages.languages');

        return [
            'language' => $distinctLanguage,
            'positions' => $distinctPositions,
        ];
    }



    public function getPropertyDetail(Request $request, $slug_url)
    {  
        $user = $request->authenticated_user;
        $userId = $user->id;
        $isFavorite = null;

        
    // Fetch property with conditional PropertyType
    $property = DB::table('properties_all_data')
                        ->where('slug_url', $slug_url)
                        ->orWhere('ListingId', $slug_url)
                        ->first();
    
    // $property = DB::table('properties_all_data')
    //     ->selectRaw('*') 
    //     ->selectRaw("CASE 
    //                     WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
    //                     THEN 'Condo' 
    //                     ELSE `PropertyType` 
    //                  END AS PropertyType")
    //     ->where('slug_url', $slug_url)
    //     ->orWhere('ListingId', $slug_url)
    //     ->first();
    
        if ($property) {
            $images = DB::table('property_images')
                        ->where('listingid', $property->ListingKeyNumeric)
                        ->pluck('image_url')
                        ->toArray();
    
            if (empty($images)) {
                $images[] = $property->image_url;
            }
    
            $property->images = $images;
    
            $agentProfilePicture = DB::table('users')
                                        ->where('agent_key', $property->ListAgentKeyNumeric)
                                        ->value('profile_picture');
    
            $property->agent_profile_picture = $agentProfilePicture ? $agentProfilePicture : null;
          
            if($userId) {
                $isFavorite = DB::table('favorite_properties')
                                ->where('user_id', $userId)
                                ->where('property_id', $property->ListingId)
                                ->exists();
            
    
            $property->is_favorite = $isFavorite;
            }
             $mainPropertyPrice = $property->ListPrice;
            $minPrice = $mainPropertyPrice * 0.8; 
            $maxPrice = $mainPropertyPrice * 1.2; 

            // $similarListings = DB::table('properties_all_data')
            //             ->select(
            //                 'id',
            //                 'StreetNumber',
            //                 'StreetDirPrefix',
            //                 'UnparsedAddress',
            //                 'slug_url',
            //                 'StreetName',
            //                 'StreetSuffix',
            //                 'UnitNumber',
            //                 'ListPrice',
            //                 'diamond',
            //                 'featured',
            //                 'UnitNumber',
            //                 'StreetDirSuffix',
            //                 'BathroomsFull',
            //                 'BedroomsTotal',
            //                 'BuildingAreaTotalSF',
            //                 DB::raw("CASE 
            //                             WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
            //                             THEN 'Condo' 
            //                             ELSE `PropertyType` 
            //                          END AS PropertyType"),
            //                 'City',
            //                 'ListingId',
            //                 'image_url',
            //                 'LivingAreaSF',
            //                 'LotSizeSquareFeet',
            //                 'StateOrProvince',
            //                 'LeaseMeasure',
            //                 'LeaseAmountFrequency',
            //                 'LeaseAmount'
            //             )
            //             ->where('PropertyType', $property->PropertyType)
            //             ->where('City', $property->City)
            //             ->where('MlsStatus', $property->MlsStatus)
            //             ->where(function ($query) use ($property) {
            //                 $query->where('ListPrice', '<=', $property->ListPrice)
            //                     ->orWhere(function ($query) use ($property) {
            //                         $query->where('ListPrice', '>', $property->ListPrice)
            //                             ->where('ListPrice', '<=', $property->ListPrice * 1.2);
            //                     });
            //             })
            //             ->where('ListingId', '!=', $property->ListingId)
            //             ->orderBy('ListPrice', 'desc')
            //             ->limit(4)
            //             ->get();
            
            $similarListings = DB::table('properties_all_data')
                ->where('PropertyType', $property->PropertyType)
                ->where('City', $property->City)
                ->where('MlsStatus', $property->MlsStatus)
                ->where('StreetDirSuffix', $property->StreetDirSuffix)
                ->whereBetween('ListPrice', [$minPrice, $maxPrice])
                ->where('ListingId', '!=', $property->ListingId)
                ->orderBy('ListPrice', 'asc')
                ->limit(4)
                ->get();
    
       
        $agent = DB::table('users')
            ->where('name', $property->ListAgentFullName)
            ->first();
    
        $agentSlug = $agent ? [
            'slug_url' => $agent->slug_url,
            'phone' => $agent->phone,
            'office_no' => $agent->office_no
        ] : null;
    

        // if ($property->Latitude && $property->Longitude) {
        //     $apiKey = env("YELP_API");
        //     $radius = 10000;
        //     $categories = 'active,arts,auto,beautysvc,education,eventservices,financialservices,food,health,localservices,hotelstravel,localflavor,media,nightlife,pets,professional,services,government,religiousorg,restaurants,shopping,schools,parks';

        //     $client = new Client();
        //     $response = $client->get("https://api.yelp.com/v3/businesses/search?longitude={$property->Longitude}&latitude={$property->Latitude}&radius=$radius&categories=$categories", [
        //         'headers' => [
        //             'Authorization' => "Bearer $apiKey",
        //             'Accept' => 'application/json',
        //         ],
        //     ]);

        //     $locationData = json_decode($response->getBody()->getContents(), true);
        // } else {
        //     $locationData = ['message' => 'No location data available'];
        // }

            return response()->json([
                'success' => true,
                'data' => $property,
                'is_favorite' => $isFavorite,
                'similarListings' => $similarListings,
                'agent_slug' => $agentSlug,
                'location' => array()

            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }
    }
      public function PropertyDetail($slugurl)
    {
        $isFavorite = null;
        $property = DB::table('properties_all_data')
            ->where('slug_url', $slugurl)
            ->orWhere('ListingId', $slugurl)
            ->first();
        
        // $property = DB::table('properties_all_data')
        // ->selectRaw('*') // Select all columns
        // ->selectRaw("CASE 
        //                 WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
        //                 THEN 'Condo' 
        //                 ELSE `PropertyType` 
        //              END AS PropertyType")
        // ->where('slug_url', $slugurl)
        // ->orWhere('ListingId', $slugurl)
        // ->first();
        
       if ($property) {    
        $images = DB::table('property_images')
            ->where('listingid', $property->ListingKeyNumeric)
            ->pluck('image_url')
            ->toArray();
    
        if (empty($images)) {
            $images[] = $property->image_url;
        }
        $property->images = $images;
    
        $agentProfilePicture = DB::table('users')
            ->where('agent_key', $property->ListAgentKeyNumeric)
            ->value('profile_picture');
            $property->agent_profile_picture = $agentProfilePicture ? $agentProfilePicture : null;
            $property->is_favorite = $isFavorite;
    
            $mainPropertyPrice = $property->ListPrice;
            $minPrice = $mainPropertyPrice * 0.8; 
            $maxPrice = $mainPropertyPrice * 1.2; 
        //       $similarListings = DB::table('properties_all_data')
        //     ->where('PropertyType', $property->PropertyType)
        //     ->where('SubdivisionName', $property->SubdivisionName)
        //     ->where('MlsStatus', $property->MlsStatus)
        //     ->where('StreetDirSuffix', $property->StreetDirSuffix)
        //     ->whereBetween('ListPrice', [$minPrice, $maxPrice])
        //     ->where('ListingId', '!=', $property->ListingId)
        //     ->orderBy('ListPrice', 'asc')
        //     ->limit(4)
        //     ->select(
        //         'id',
        //         'StreetNumber',
        //         'StreetDirPrefix',
        //         'UnparsedAddress',
        //         'slug_url',
        //         'StreetName',
        //         'StreetSuffix',
        //         'UnitNumber',
        //         'ListPrice',
        //         'BathroomsFull',
        //         'BedroomsTotal',
        //         'BuildingAreaTotalSF',
        //         DB::raw("CASE 
        //                     WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
        //                     THEN 'Condo' 
        //                     ELSE `PropertyType` 
        //                  END AS PropertyType"),
        //         'City',
        //         'ListingId',
        //         'image_url', // Ensure this column exists
        //         'LivingAreaSF',
        //         'LotSizeSquareFeet',
        //         'StateOrProvince',
        //         'LeaseMeasure',
        //         'LeaseAmountFrequency',
        //         'LeaseAmount',
        //         'diamond',
        //         'featured',
        //         'StreetDirSuffix'
        //     )
        //     ->get();
        
        $similarListings = DB::table('properties_all_data')
                ->where('PropertyType', $property->PropertyType)
                ->where('City', $property->City)
                ->where('MlsStatus', $property->MlsStatus)
                ->where('StreetDirSuffix', $property->StreetDirSuffix)
                ->whereBetween('ListPrice', [$minPrice, $maxPrice])
                ->where('ListingId', '!=', $property->ListingId)
                ->orderBy('ListPrice', 'asc')
                ->limit(4)
                ->get();
    
       
        $agent = DB::table('users')
            ->where('name', $property->ListAgentFullName)
            ->first();
    
        $agentSlug = $agent ? [
            'slug_url' => $agent->slug_url,
            'phone' => $agent->phone,
            'office_no' => $agent->office_no
        ] : null;

        // if ($property->Latitude && $property->Longitude) {
        //     $apiKey = env("YELP_API");
        //     $radius = 10000;
        //     $categories = 'active,arts,auto,beautysvc,education,eventservices,financialservices,food,health,localservices,hotelstravel,localflavor,media,nightlife,pets,professional,services,government,religiousorg,restaurants,shopping,schools,parks';

        //     $client = new Client();
        //     $response = $client->get("https://api.yelp.com/v3/businesses/search?longitude={$property->Longitude}&latitude={$property->Latitude}&radius=$radius&categories=$categories", [
        //         'headers' => [
        //             'Authorization' => "Bearer $apiKey",
        //             'Accept' => 'application/json',
        //         ],
        //     ]);

        //     $locationData = json_decode($response->getBody()->getContents(), true);
        // } else {
        //     $locationData = ['message' => 'No location data available'];
        // }

    return response()->json([
            'data' => $property,
            'similarListings' => $similarListings,
            'agent_slug' => $agentSlug,
            'location' => array()
        ]);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Property not found',
        ], 404);
    }
    } 
    

    

    public function detail(Request $request, $id)
    {
        $user = User::where('slug_url', $id)->firstOrFail();

        $averageRating = Review::where('review_to', $user->id)
            ->where('status', 1)
            ->avg('rating');

        unset($user->avg_rating);
        $user->avg_rating = round($averageRating, 1);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }


    public function storeReview_3_oct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_from' => 'required',
            'review_to' => 'required|exists:users,slug_url',
            'title' => 'required',
            'review_feedback' => 'nullable|required',
            'rating' => 'nullable|numeric|min:0|max:5|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reviewToUser = User::where('slug_url', $request->input('review_to'))->firstOrFail();

        $requestData = $validator->validated();
        $requestData['review_from'] = $request->input('review_from');
        $requestData['review_to'] = $reviewToUser->id;

        $review = Review::create($requestData);

        $avgRating = Review::where('review_to', $reviewToUser->id)->avg('rating');

        $reviewToUser->avg_rating = $avgRating;
        $reviewToUser->save();

        $latestReview = Review::where('review_to', $reviewToUser->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestReview) {
            $latestReview->update(['avg_rating' => intval($avgRating)]);
        }

        return response()->json(['message' => 'Review stored successfully', 'review' => $review], 201);
    }
     public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_from' => 'required',
            'firsttitle' => 'nullable|string',
            'lasttitle'  => 'nullable|string',
            'review_to' => 'required|exists:users,slug_url',
            'title' => 'required',
            'review_feedback' => 'nullable|required',
            'rating' => 'nullable|numeric|min:0|max:5|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $realtorSlug = $request->input('review_to');
        $realtor = User::where('slug_url', $realtorSlug)->first();
        
        if (!$realtor) {
            return response()->json(['error' => 'Realtor not found.'], 404);
        }
    
        $realtorId = $realtor->id;
        $realtorname = $realtor->name;

        $reviewToUser = User::where('slug_url', $request->input('review_to'))->firstOrFail();
        
        $requestData = $validator->validated();
        // dd($requestData);
        $requestData['review_from'] = $request->input('review_from');
        $requestData['review_to'] = $reviewToUser->id;

        $review = Review::create($requestData);

        $avgRating = Review::where('review_to', $reviewToUser->id)->avg('rating');

        $reviewToUser->avg_rating = $avgRating;
        $reviewToUser->save();
        
        $mainemail = env('ADMIN_EMAIL');
        try {
         
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => $mainemail,
                'subject' => "New Review Submitted: Action Required",
                'html' => view('emails.review', [
                   
                    'realtorname' => $realtorname,
                    'reviewerfirstname' => $requestData['firsttitle'],
                    'reviewerlastname' => $requestData['lasttitle'],
                    'revieweremail' => $requestData['review_from'],
                    'rating' => $avgRating,
                    'comment' => $requestData['review_feedback'],
                    'realtorid' => $realtorId
                ])->render()
            ]);
             //dd($mgClient);
   
           
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
        }
        $latestReview = Review::where('review_to', $reviewToUser->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestReview) {
            $latestReview->update(['avg_rating' => intval($avgRating)]);
        }

        return response()->json(['message' => 'Review stored successfully', 'review' => $review], 201);
    }



    public function getReviews(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'paginate' => 'nullable|boolean',
            'sort_by' => 'nullable|string', // Add 'sort_by' validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('paginate', false);
        $sortBy = $request->filled('sort_by') ? $request->input('sort_by') : 'desc';

        try {
            $user = User::where('slug_url', $id)->firstOrFail();

            $reviewsQuery = Review::where('review_to', $user->id)->where('status', 1);

            // Apply sorting based on user input
            $reviewsQuery->orderBy('created_at', $sortBy);

            $count = $reviewsQuery->count();

            $reviews = $paginate
                ? $reviewsQuery->paginate($perPage)->appends($request->query())
                : $reviewsQuery->simplePaginate($perPage)->appends($request->query());

            if ($reviews->isEmpty()) {
                return response()->json(['message' => 'No reviews found for the user.'], 404);
            }

            return response()->json(['reviews' => $reviews, 'total_count' => $count], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'User not found.'], 404);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }


    public function getAverageRating($id)
    {
        try {
            $user = User::where('slug_url', $id)->firstOrFail();

            $averageRating = Review::where('review_to', $user->id)
                ->where('status', 1)
                ->avg('rating');

            if ($averageRating === null) {
                return response()->json(['message' => 'No reviews found for the user.'], 404);
            }

            $averageRating = intval($averageRating);

            return response()->json(['average_rating' => $averageRating], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'User not found.'], 404);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }


    public function submitForm(Request $request)
    {
 
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', 
            'time' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'selectedDate' => 'required|date',
            'message' => 'nullable|string',
            'agent_name' => 'nullable|string|max:255',
            'agent_email' => 'nullable|string',
            'property_address' => 'nullable|string'
        ]);
    
        $tour = new Tour();
        $tour->name = $validatedData['name']; 
        $tour->time = $request->input('time');
        $tour->phone = $request->input('phone');
        $tour->email = $validatedData['email'];
        $tour->date = $validatedData['selectedDate'];
        $tour->message = $request->input('message');
        $tour->save();
    
        $latestAdmin = User::where('role', 1)->latest()->first();
    
        if ($latestAdmin) {
            $adminname = $latestAdmin->name;
            $adminemail = $latestAdmin->email;
        }
        $realtorEmail = $request->input('agent_email');
        $mainemail = env('ADMIN_EMAIL');
        // $from = env('MAIL_FROM_ADDRESS');
        try {
         
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => [$realtorEmail, $mainemail],
                'subject' => "New Property Tour Request for " . $validatedData['property_address'],
                'html' => view('emails.tour', [
                    'name' => $validatedData['name'],
                    'lastname' => $request->input('last_name'),
                    'phone' => $request->input('phone'),
                    'email' => $validatedData['email'],
                    'time' => $validatedData['time'],
                    'date' => $validatedData['selectedDate'],
                    'role' => $request->input('phone'),
                    'message' => $request->input('message'),
                    'address' => $validatedData['property_address'],
                    'realtorname' => $validatedData['agent_name'],
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ]);
   
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => $validatedData['email'],
                'subject' => "Thank You for Requesting a Property Tour",
                'html' => view('emails.autorespond-tour', [
                    'address' => $validatedData['property_address'],
                    'name' => $validatedData['name'],
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
        }
    
        return response()->json(['message' => 'Form data stored successfully'], 200);
    }
    
    
    public function propertyreview(Request $request)
    {
        $listing_id = $request->input('listing_id');
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'message' => 'required|string|max:200',
        ]);

        try {
            $review = new PropertyReview();
            $review->review_from = $validatedData['name'];
            $review->email = $validatedData['email'];
            $review->rating = $validatedData['rating'];
            $review->review = $validatedData['message'];
            $review->listing_id = $listing_id;
            $review->save();

            return response()->json(['message' => 'Review submitted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getSimilarListings(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'property_type' => 'required|string',
                'city' => 'required|string',
                'status' => 'required|string',
                'exclude_property_id' => 'required|string',
                'listing_type' => 'nullable|string',
            ]);

            $listingType = $validatedData['listing_type'] ?? null;

            $similarListingsQuery = DB::table('properties_all_data')
                ->where('PropertyType', $validatedData['property_type'])
                ->where('City', $validatedData['city'])
                ->where('MlsStatus', $validatedData['status'])
                ->where('ListingId', '!=', $validatedData['exclude_property_id']);

            if ($listingType !== null) {
                $similarListingsQuery->where($listingType, '=', 1);
            }

            $similarListings = $similarListingsQuery->limit(4)->get();

            return response()->json(['data' => $similarListings]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // public function autoSuggestMlsAgentName(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'key' => 'required|string|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $key = $request->input('key');

    //     $suggestedAgents = User::where(function($query) use ($key) {
    //     $query->where('name', 'like', '%' . $key . '%')
    //               ->orWhere('mls_id', 'like', '%' . $key . '%');
    //     })
    //     ->take(5)
    //     ->pluck('name', 'mls_id')
    //     ->unique();



    //     if ($suggestedAgents->isEmpty()) {
    //         return response()->json(['message' => 'No MLS agents found for the provided key.'], 404);
    //     }

    //     return response()->json(['suggested_agents' => $suggestedAgents], 200);
    // }
    
    public function autoSuggestMlsAgentName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = $request->input('key');

        $suggestedAgents = User::where(function($query) use ($key) {
        $query->where('name', 'like', '%' . $key . '%')
                  ->orWhere('mls_id', 'like', '%' . $key . '%');
        })
        ->take(5)
        ->get(['name', 'mls_id', 'slug_url']) // Specify the columns to retrieve
->unique('mls_id'); // Unique by 'mls_id'

// Format the result to an array of key-value pairs
$result = $suggestedAgents->map(function($agent) {
    return [
        'mls_id' => $agent->mls_id,
        'name' => $agent->name,
        'slug_url' => $agent->slug_url,
    ];

});



        if ($suggestedAgents->isEmpty()) {
            return response()->json(['message' => 'No MLS agents found for the provided key.'], 404);
        }

        return response()->json(['suggested_agents' => $suggestedAgents], 200);
    }



       public function storeContactUs(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'email' => 'required|email',
                'comment' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $contactData = $request->except(['_token']);
            $phone = null;
    
            if ($request->has('phone')) {
                $phone = preg_replace('/[^0-9]/', '', $request->phone);
            }
    
            ContactUs::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $phone,
                'email' => $request->email,
                'role' => $request->role,
                'page_name' => $request->page_name,
                // 'time' => $request->time,
                'comment' => $request->comment,
                'contact_data' => json_encode($contactData),
            ]);
    
            $latestAdmin = User::where('role', 1)->latest()->first();
            $pagename = $request->page_name;
            $adminname = $latestAdmin->name;
            $adminemail = $latestAdmin->email;
            $mainEmail = env('ADMIN_EMAIL');
    
            // Initialize Mailgun client
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
    
            try {
                if ($pagename === 'details_page') {
                    $realtor_name = $request->realtorname;
                    $realtor_email = $request->realtoremail;
    
                    $params = [
                        'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                        'to' => $realtor_email,
                        'subject' => "New Contact Request from Real Estate Professionals Inc. Website",
                        'html' => view('emails.realtor-contactform', [
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'phone' => $phone,
                            'email' => $request->email,
                            'message' => $request->comment,
                            'role' => $request->role,
                            'adminname' => $adminname,
                            'adminemail' => $adminemail,
                            'realtor_name' => $realtor_name
                        ])->render()
                    ];
                    
              $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $params);
                    
                } else {
                    $realtorEmail = $request->prorealtoremail;
    
                    if ($request->property_type === 'features') {
                        $params = [
                            'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                            'to' => $realtorEmail,
                            'subject' => "New Contact Request from Real Estate Professionals Inc. Website",
                            'html' => view('emails.prorealtor-contact-us', [
                                'first_name' => $request->first_name,
                                'last_name' => $request->last_name,
                                'phone' => $request->phone,
                                'email' => $request->email,
                                'realtor_name' => $request->prorealtorname,
                                'message' => $request->comment,
                                'adminname' => $adminname,
                                'adminemail' => $adminemail
                            ])->render()
                        ];
    
                        $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $params);
                    }
    
                    $params = [
                        'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                        'to' => $mainEmail,
                        'subject' => "New Contact Request from Real Estate Professionals Inc. Website",
                        'html' => view('emails.contact-us', [
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            //'phone' => $request->phone,
                            'email' => $request->email,
                            'message' => $request->comment,
                            'adminname' => $adminname,
                            'adminemail' => $adminemail
                        ])->render()
                    ];
                    $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $params);
                }
            } catch (\Exception $e) {//echo $e->getMessage();die('vijay');
                Log::error("Failed to send email: " . $e->getMessage());
            }
    
            return response()->json(['message' => 'Contact details saved successfully'], 201);
        } catch (\Exception $e) {
            Log::error("Failed to save contact details: " . $e->getMessage());
            return response()->json(['message' => 'Failed to save contact details'], 500);
        }
    }

     public function storeJoinRep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'joinee' => 'required|boolean',
            'real_estate_board' => 'required|string',
            'experience' => 'nullable|string',
            'licensed_areas' => 'nullable|string',
            'practice_areas' => 'nullable|string',
            'reference' => 'nullable|string',
            'board_name' => 'nullable|string',
            'about' => 'nullable|string',
            'is_contact' => 'required',
            'perceive' => 'nullable|string',
        ]);
    // dd($validator);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

            if($request->real_estate_board == 'No'){
                $isBoard = 'No';
            }
            else{
                $isBoard = $request->board_name;
            }
        
        $join_rep_data = $request->except(['_token']);
    
        $joinRep = JoinRep::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'joinee' => $request->joinee,
            'board_name' => $isBoard,
            'experience' => $request->experience,
            'licensed_area' => $request->licensed_areas,
            'practice_areas' => $request->practice_areas,
            'reference' => $request->reference,
            'about' => $request->about,
            'is_contact' => $request->is_contact,
            'perceive' => $request->perceive,
            'join_rep_data' => json_encode($join_rep_data),
        ]);
    // dd($joinRep);
        $latestAdmin = User::where('role', 1)->latest()->first();
    
        $adminname = $latestAdmin->name;
        $adminemail = $latestAdmin->email;
        $mainEmail = env('ADMIN_EMAIL');
        try {
           
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
            $mailFromAddress = env('MAIL_FROM_ADDRESS');
            $adminEmail = [
                'from' => 'Team REP <' . $mailFromAddress . '>',
                'to' => [$mainEmail,'andrewfrentz@repinc.ca'],
                'subject' => "New Realtor Intake Form Submission - Action Required",
                'html' => view('emails.join-rep-mail', [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'board_name' => $isBoard,
                    'joinee' => $request->joinee ? 'Yes' : 'No',
                    'about'  => $request->about,
                    'contact' => $request->is_contact,
                    'reference' => $request->reference,
                    'email' => $request->email,
                    'licensed_area' => $request->licensed_areas,
                    'perceive' => $request->perceive,
                    'experience' => $request->experience,
                    'practice_areas' => $request->practice_areas,
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ];
  
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $adminEmail);
    
           
            $sellerEmail = [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => $request->email,
                'subject' => "Thank You for Submitting Your Realtor Intake Form",
                'html' => view('emails.autorespond-joinrep', [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ];
    
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $sellerEmail);
    
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
        }
    
        return response()->json(['message' => 'JoinRep details saved successfully', 'data' => $joinRep], 201);
    }

    public function getAgentProperties(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrors($validator->errors(), 'Validation Error');
            }

            $perPage = $request->input('per_page', 6);

            try {
                $user = User::where('slug_url', $id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not found.'], 404);
            }

             $queryAgent = DB::table('properties_all_data')
            ->select(
                'id',
                'StreetNumber',
                'StreetDirPrefix',
                'UnparsedAddress',
                'slug_url',
                'StreetName',
                'StreetSuffix',
                'UnitNumber',
                'ListPrice',
                'BathroomsFull',
                'BedroomsTotal',
                'BuildingAreaTotalSF',
                DB::raw("CASE 
                            WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
                            THEN 'Condo' 
                            ELSE `PropertyType` 
                         END AS PropertyType"),
                'City',
                'ListingId',
                'image_url',
                'LivingAreaSF',
                'LotSizeSquareFeet',
                'StateOrProvince',
                'LeaseMeasure',
                'LeaseAmountFrequency',
                'LeaseAmount'
            )
            ->where('ListAgentFullName', $user->name)
            ->orWhere('ListAgentKeyNumeric', $user->mls_id);

            $countAgent = $queryAgent->count();
            $listings = $countAgent > 0 ? $queryAgent : null;

             if (!$listings) {
            $queryOffice = DB::table('properties_all_data')
                ->select(
                    'id',
                    'StreetNumber',
                    'StreetDirPrefix',
                    'UnparsedAddress',
                    'slug_url',
                    'StreetName',
                    'StreetSuffix',
                    'UnitNumber',
                    'ListPrice',
                    'BathroomsFull',
                    'BedroomsTotal',
                    'BuildingAreaTotalSF',
                    DB::raw("CASE 
                                WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
                                THEN 'Condo' 
                                ELSE `PropertyType` 
                             END AS PropertyType"),
                    'City',
                    'ListingId',
                    'image_url',
                    'LivingAreaSF',
                    'LotSizeSquareFeet',
                    'StateOrProvince',
                    'LeaseMeasure',
                    'LeaseAmountFrequency',
                    'LeaseAmount'
                )
                ->whereIn('ListOfficeKeyNumeric', ['1297353', '1298083']);
            $countOffice = $queryOffice->count();
            $listings = $countOffice > 0 ? $queryOffice : null;
        }

            $listingsType = $listings ? ($countAgent > 0 ? 'Listings' : 'Office Listings') : '';

            $queryResult = $listings
                ? ($request->boolean('paginate')
                    ? $listings->paginate($perPage)->appends($request->query())
                    : $listings->simplePaginate($perPage)->appends($request->query()))
                : [];

            $totalCount = $listings ? ($countAgent > 0 ? $countAgent : $countOffice) : 0;

            return response()->json([
                'property' => $queryResult,
                'total_count' => $totalCount,
                'listings_type' => $listingsType,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }

    public function storeListingAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'property_address' => 'nullable|string|max:255',
            'community' => 'nullable|string|max:255',
            'approx_age_of_property' => 'nullable|string|max:255',
            'approx_size_of_property' => 'nullable|string|max:255',
            'style_of_property' => 'nullable|string|max:255',
            'no_of_bedrooms' => 'nullable|max:255',
            'no_of_bathrooms' => 'nullable|max:255',
            'basement_development' => 'nullable|string|max:255',
            'parking' => 'nullable|string|max:255',
            'interest' => 'nullable|string|max:255',
            'additional_information' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $listing_data = $request->except(['_token']);
    
        ListingAppointment::create($listing_data);
    
        $latestAdmin = User::where('role', 1)->latest()->first();
        $adminname = $latestAdmin->name;
        $adminemail = $latestAdmin->email;
        $mainEmail = env('ADMIN_EMAIL');
    
        try {
        
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
    
            $adminEmail = [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => [$mainEmail,"northoffice@repinc.ca"],
                'subject' => "New Home Evaluation Request from Real Estate Professionals Inc. Website",
                'html' => view('emails.listingAppointment', [
                    'listing_data' => $listing_data,
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ];
    
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $adminEmail);
    
        } catch (\Exception $e) {
            Log::error("Failed to send email to admin: " . $e->getMessage());
        }
    
     
        try {
   
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
    
            $userEmail = [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => $request->email,
                'subject' => "Thank You for Requesting a Home Evaluation from Real Estate Professionals Inc.",
                'html' => view('emails.autorespond-homeevaluation', [
                    'listing_data' => $listing_data,
                    'adminname' => $adminname,
                    'adminemail' => $adminemail
                ])->render()
            ];
    
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $userEmail);
    
        } catch (\Exception $e) {
            Log::error("Failed to send email to user: " . $e->getMessage());
        }
    
        return response()->json(['message' => 'Listing details saved successfully'], 201);
    }
    
    public function listingAutosuggestionBar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = $request->input('key');

        $suggestedAgents = DB::table('properties_all_data')
            ->where('City', 'like', '%' . $key . '%')
            ->where('is_active', '1')
            ->orWhere('ListingId', 'like', '%' . $key . '%')
            ->orWhere('StateOrProvince', 'like', '%' . $key . '%')
            ->take(5)
            ->distinct()
            ->get(['City', 'ListingId', 'StateOrProvince']);

        if ($suggestedAgents->isEmpty()) {
            return response()->json(['message' => 'No MLS agents found for the provided key.'], 404);
        }

        return response()->json(['suggested_agents' => $suggestedAgents], 200);
    }




    public function getProperties(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrors($validator->errors(), 'Validation Error');
            }

            $perPage = $request->input('per_page', 10);

            $query = DB::table('properties_all_data');

            $count = $query->count();

            $queryResult = $request->boolean('paginate')
                ? $query->paginate($perPage)->appends($request->query())
                : $query->simplePaginate($perPage)->appends($request->query());

            return response()->json(['property' => $queryResult, 'total_count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }



    public function getPropertiesIndex(Request $request)
    {
    try {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'paginate' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors(), 'Validation Error');
        }

        $perPage = $request->input('per_page', 10);

        $featuredProperties = DB::table('properties_all_data')
            ->select(
                'id',
                'StreetNumber',
                'StreetDirPrefix',
                'UnparsedAddress',
                'slug_url',
                'StreetName',
                'StreetSuffix',
                'UnitNumber',
                'ListPrice',
                'BathroomsFull',
                'BedroomsTotal',
                'BuildingAreaTotalSF',
                DB::raw("CASE 
                                WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
                                THEN 'Condo' 
                                ELSE `PropertyType` 
                             END AS PropertyType"),
                'City',
                'ListingId',
                'image_url',
                'LivingAreaSF',
                'LotSizeSquareFeet',
                'StateOrProvince',
                'LeaseMeasure',
                'LeaseAmountFrequency',
                'LeaseAmount'
            )
            ->where('featured', 1)
            ->orderBy('DOMDate', 'desc')
            ->take(8)
            ->get();

        $diamondProperties = DB::table('properties_all_data')
            ->select(
                'id',
                'StreetNumber',
                'StreetDirPrefix',
                'UnparsedAddress',
                'slug_url',
                'StreetName',
                'StreetSuffix',
                'UnitNumber',
                'ListPrice',
                'BathroomsFull',
                'BedroomsTotal',
                'BuildingAreaTotalSF',
                DB::raw("CASE 
                                WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
                                THEN 'Condo' 
                                ELSE `PropertyType` 
                             END AS PropertyType"),
                'City',
                'ListingId',
                'image_url',
                'LivingAreaSF',
                'LotSizeSquareFeet',
                'StateOrProvince',
                'LeaseMeasure',
                'LeaseAmountFrequency',
                'LeaseAmount'
            )
            ->where('diamond', 1)
            ->orderBy('DOMDate', 'desc')
            ->take(8)
            ->get();

        $featuredCount = count($featuredProperties);
        $diamondCount = count($diamondProperties);

        return response()->json([
            'featured_properties' => $featuredProperties,
            'featured_count' => $featuredCount,
            'diamond_properties' => $diamondProperties,
            'diamond_count' => $diamondCount,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while processing the request.'], 500);
    }
}






    public function getPropertiesType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'nullable|boolean',
                'type' => 'required|in:featured,diamond',
                'sort_by' => 'nullable|in:desc,price_low,price_high',
            ]);

            if ($validator->fails()) {
                return $this->validationErrors($validator->errors(), 'Validation Error');
            }

            $perPage = $request->input('per_page', 12);
            $type = $request->input('type');
            $sortBy = $request->input('sort_by', 'desc');

            $propertiesQuery = DB::table('properties_all_data')
                ->select('id', 'StreetNumber', 'StreetDirPrefix', 'UnparsedAddress', 'slug_url', 'StreetName', 'StreetSuffix', 'UnitNumber', 'ListPrice', 'BathroomsFull', 'BedroomsTotal', 'BuildingAreaTotalSF', 'PropertyType', 'City', 'ListingId', 'image_url', 'LivingAreaSF', 'LotSizeSquareFeet', 'StateOrProvince');

            // Casting ListPrice as numeric for correct sorting
            $propertiesQuery->selectRaw('CAST(ListPrice AS DECIMAL(10, 2)) AS ListPriceNumeric');

            if ($sortBy == 'desc') {
                $propertiesQuery->orderBy('DOMDate', 'desc');
            } elseif ($sortBy == 'price_low') {
                $propertiesQuery->orderBy('ListPrice', 'asc');
            } elseif ($sortBy == 'price_high') {
                $propertiesQuery->orderBy('ListPrice', 'desc');
            }

            if ($type === 'featured') {
                $propertiesQuery->where('featured', 1);
            } elseif ($type === 'diamond') {
                $propertiesQuery->where('diamond', 1);
            }

            // Sorting


            $properties = $propertiesQuery->paginate($perPage)->appends($request->query());

            return response()->json([$type . '_properties' => $properties], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }


public function getadvanceData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'paginate' => 'nullable|boolean',
            'min_list_price' => 'nullable|numeric|min:0',
            'max_list_price' => 'nullable|numeric|min:0',
            'min_year_built' => 'nullable|integer|min:0',
            'max_year_built' => 'nullable|integer|min:0',
            'min_living_area_sf' => 'nullable|numeric|min:0',
            'max_living_area_sf' => 'nullable|numeric|min:0',
            'search' => 'nullable|string',
            'listing_id' => 'nullable|integer',
            'property_type' => 'nullable|string',
            'community' => 'nullable|string',
            'pets_allowed' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors(), 'Validation Error');
        }

        $perPage = $request->input('per_page', 20);
        $paginate = $request->boolean('paginate');

        $mapDataQuery = DB::table('properties_all_data')
            ->select('ListingKeyNumeric', 'Longitude', 'Latitude', 'slug_url', 'StreetName', 'StreetSuffix', 'UnparsedAddress', 'ListPrice', 'image_url', 'City', 'StateOrProvince', 'StreetNumber', 'StreetDirPrefix', 'UnitNumber', 'ListingId');

        $listingDataQuery = DB::table('properties_all_data')
            ->select(
                'id',
                'StreetNumber',
                'StreetDirPrefix',
                'UnparsedAddress',
                'slug_url',
                'StreetName',
                'StreetSuffix',
                'UnitNumber',
                'ListPrice',
                'BathroomsFull',
                'BedroomsTotal',
                'BuildingAreaTotalSF',
                DB::raw("CASE 
                                WHEN `PropertySubtype` IN ('Apartment', 'Row/Townhouse') 
                                THEN 'Condo' 
                                ELSE `PropertyType` 
                             END AS PropertyType"),
                'City',
                'ListingId',
                'image_url',
                'LivingAreaSF',
                'StateOrProvince',
                'LotSizeSquareFeet',
                'featured',
                'diamond',
                'LeaseMeasure',
                'LeaseAmount',
                'LeaseAmountFrequency'

                // DB::raw("JSON_UNQUOTE(JSON_EXTRACT(OtherColumns, '$.LeaseMeasure')) as LeaseMeasure"),
                // DB::raw("JSON_UNQUOTE(JSON_EXTRACT(OtherColumns, '$.LeaseAmount')) as LeaseAmount"),
                // DB::raw("JSON_UNQUOTE(JSON_EXTRACT(OtherColumns, '$.LeaseAmountFrequency')) as LeaseAmountFrequency")
            );

        if ($request->filled('search')) {
            $this->applySearchFilter($request, $mapDataQuery, $listingDataQuery);
        }

        $this->applyRangeFilters($request, $mapDataQuery, $listingDataQuery);

        if ($request->filled('applies_filters')) {
            $appliedFilters = explode(',', $request->input('applies_filters'));
    
            foreach ($appliedFilters as $filter) {
                $filterName = urldecode($filter);
    
                if ($filterName === 'pool') {
                    $mapDataQuery->whereNotNull('Poolfeatures')->where('Poolfeatures', '!=', '');
                    $listingDataQuery->whereNotNull('Poolfeatures')->where('Poolfeatures', '!=', '');
                } elseif ($filterName === 'parking') {
                    $mapDataQuery->whereNotNull('ParkingFeatures')->where('ParkingFeatures', '!=', '');
                    $listingDataQuery->whereNotNull('ParkingFeatures')->where('ParkingFeatures', '!=', '');
                }  elseif ($filterName === 'clubhouse') {
                    $mapDataQuery->where('CommunityFeatures', 'like', '%Clubhouse%');
                    $listingDataQuery->where('CommunityFeatures', 'like', '%Clubhouse%');
                } elseif ($filterName === 'playground') {
                    $mapDataQuery->where('CommunityFeatures', 'like', '%Playground%');
                    $listingDataQuery->where('CommunityFeatures', 'like', '%Playground%');
                } elseif ($filterName === 'lake') {
                    $mapDataQuery->where('CommunityFeatures', 'like', '%Lake%');
                    $listingDataQuery->where('CommunityFeatures', 'like', '%Lake%');
                }
                elseif ($filterName === 'street_lights') {
                    $mapDataQuery->where('CommunityFeatures', 'like', '%Street Lights%');
                    $listingDataQuery->where('CommunityFeatures', 'like', '%Street Lights%');
                }

                elseif ($filterName === 'deck') {
                    $mapDataQuery->where('PatioAndPorchFeatures', 'like', '%Deck%');
                    $listingDataQuery->where('PatioAndPorchFeatures', 'like', '%Deck%');
                }
                elseif ($filterName === 'balcony') {
                    $mapDataQuery->where('PatioAndPorchFeatures', 'like', '%balcony%');
                    $listingDataQuery->where('PatioAndPorchFeatures', 'like', '%balcony%');
                }
                elseif ($filterName === 'patio') {
                    $mapDataQuery->where('PatioAndPorchFeatures', 'like', '%patio%');
                    $listingDataQuery->where('PatioAndPorchFeatures', 'like', '%patio%');
                }
                elseif ($filterName === 'front_porch') {
                    $mapDataQuery->where('PatioAndPorchFeatures', 'like', '%Front Porch%');
                    $listingDataQuery->where('PatioAndPorchFeatures', 'like', '%Front Porch%');
                }

                elseif ($filterName === 'gazebo') {
                    $mapDataQuery->where('AssociationAmenities', 'like', '%gazebo%');
                    $listingDataQuery->where('AssociationAmenities', 'like', '%gazebo%');
                }
                elseif ($filterName === 'air_conditioning') {
                    $mapDataQuery->where('Appliances', 'like', '%Air Conditioner%');
                    $listingDataQuery->where('Appliances', 'like', '%Air Conditioner%');
                }

                
                elseif ($filterName === 'laundry') {
                    $mapDataQuery->whereRaw("JSON_EXTRACT(OtherColumns, '$.LaundryFeatures') IS NOT NULL AND JSON_EXTRACT(OtherColumns, '$.LaundryFeatures') != ''");
                    $listingDataQuery->whereRaw("JSON_EXTRACT(OtherColumns, '$.LaundryFeatures') IS NOT NULL AND JSON_EXTRACT(OtherColumns, '$.LaundryFeatures') != ''");
                }
                
                
                elseif ($filterName === 'onegarage') {
                    $mapDataQuery->where('GarageSpaces', '>=', 1);
                    $listingDataQuery->where('GarageSpaces', '>=', 1);
                } elseif ($filterName === 'twogarage') {
                    $mapDataQuery->where('GarageSpaces', '>=', 2);
                    $listingDataQuery->where('GarageSpaces', '>=', 2);
                } elseif ($filterName === 'threegarage') {
                    $mapDataQuery->where('GarageSpaces', '>=', 3);
                    $listingDataQuery->where('GarageSpaces', '>=', 3);
                } elseif ($filterName === 'fireplace') {
                    $mapDataQuery->whereNotNull('FireplaceFeatures')->where('FireplaceFeatures', '!=', '');
                    $listingDataQuery->whereNotNull('FireplaceFeatures')->where('FireplaceFeatures', '!=', '');
                } elseif ($filterName === 'pets_allowed') {
                    $petsAllowed = $request->input('pets_allowed');
                    if ($petsAllowed === 'true') {
                        $mapDataQuery->whereNotNull('PetsAllowed')->where('PetsAllowed', '!=', '');
                        $listingDataQuery->whereNotNull('PetsAllowed')->where('PetsAllowed', '!=', '');
                    } else {
                        $mapDataQuery->where(function ($query) {
                            $query->whereNull('PetsAllowed')->orWhere('PetsAllowed', '!=', '');
                        });
                        $listingDataQuery->where(function ($query) {
                            $query->whereNull('PetsAllowed')->orWhere('PetsAllowed', '!=', '');
                        });
                    }
                } elseif ($filterName === 'basement') {
                    $basement = $request->input('basement');
                    if ($basement === 'true') {
                        $mapDataQuery->whereNotNull('Basement')->where('Basement', '!=', '');
                        $listingDataQuery->whereNotNull('Basement')->where('Basement', '!=', '');
                    } else {
                        $mapDataQuery->where(function ($query) {
                            $query->whereNull('Basement')->orWhere('Basement', '!=', '');
                        });
                        $listingDataQuery->where(function ($query) {
                            $query->whereNull('Basement')->orWhere('Basement', '!=', '');
                        });
                    }
                } 

                elseif ($filterName === 'furnished') {
                    $furnished = $request->input('furnished');
                    if ($furnished === 'true') {
                        $mapDataQuery->whereJsonContains('OtherColumns', ['Furnished' => true]);
                        $listingDataQuery->whereJsonContains('OtherColumns', ['Furnished' => true]);
                    } else {
                        $mapDataQuery->whereRaw("JSON_EXTRACT(OtherColumns, '$.Furnished') IS NULL");
                        $listingDataQuery->whereRaw("JSON_EXTRACT(OtherColumns, '$.Furnished') IS NULL");
                    }
                } elseif ($filterName === 'onestory') {
                    $mapDataQuery->where('StoriesTotal', 1);
                    $listingDataQuery->where('StoriesTotal', 1);
                } elseif ($filterName === 'twostories') {
                    $mapDataQuery->where('StoriesTotal', 2);
                    $listingDataQuery->where('StoriesTotal', 2);
                } elseif ($filterName === 'threestories') {
                    $mapDataQuery->where('StoriesTotal', 3);
                    $listingDataQuery->where('StoriesTotal', 3);
                }
            }
        }

        if ($request->filled('mls_status')) {
            $sale = $request->input('mls_status');
            $status = $sale == 'Sale' ? ['Active', 'Incomplete', 'Pending'] : ['Sold', 'Terminated', 'Withdrawn', 'Expired'];
            $mapDataQuery->whereIn('MlsStatus', $status);
            $listingDataQuery->whereIn('MlsStatus', $status);
        }

        $this->applySorting($request, $listingDataQuery);
        $rawListingDataQuery = $listingDataQuery->toSql();
        // dd($rawListingDataQuery);
        $totalCount = $listingDataQuery->count();
        $mapData = $mapDataQuery->take(200)->get();
        
        $listingData = $paginate
            ? $listingDataQuery->paginate($perPage)->appends($request->query())
            : $listingDataQuery->simplePaginate($perPage)->appends($request->query());

        if ($totalCount === 0) {
            return response()->json([
                'message' => 'No data found',
                'status' => 'success',
                'data' => [],
                'total_count' => $totalCount,
            ]);
        }
$rawListingDataQuery = $listingDataQuery->toSql();
        $label = '';

        // Bedroom filter
        if ($request->filled('min_bedrooms')) {
            $bedrooms = $request->input('min_bedrooms');
            $label .= $bedrooms . " beds";
        }

        // PropertyType filter
        if ($request->filled('property_type')) {
            $propertyType = $request->input('property_type');
            $label .= " " . $propertyType;
        }
        $label .= " Properties";


        // Cities filter
        if ($request->filled('search')) {
            $city = ucwords(str_replace('-', ' ', $request->input('search')));
            $label .= " in " . $city;
        }

        if ($request->filled('community')) {
            $community = ucwords(str_replace('-', ' ', $request->input('community')));
            $label .= " in " . $community;
        }

        // Combining counts and label
        $label = $totalCount . " " . $label;

        return response()->json([
            'message' => 'Data fetched successfully',
            'status' => 'success',
            'map_data' => $mapData,
            'raw'=>$rawListingDataQuery,
            'listing_data' => $listingData,
            'total_count' => $totalCount,
            'label' => $label,
        ]);
    }


    private function applySearchFilter(Request $request, $mapDataQuery, $listingDataQuery)
    {
        $searchKey = strtolower($request->input('search'));
        $communityMapping = config('communities');
    
            if (isset($communityMapping[$searchKey])) {
                $communities = $communityMapping[$searchKey];
                $mapDataQuery->whereIn('SubdivisionName', $communities);
                $listingDataQuery->whereIn('SubdivisionName', $communities);
            } else {
                if ($searchKey === 'diamond') {
                    $mapDataQuery->where('diamond', 1);
                    $listingDataQuery->where('diamond', 1);
                } elseif ($searchKey === 'featured') {
                    $mapDataQuery->where('featured', 1);
                    $listingDataQuery->where('featured', 1);
                } else {
                    $searchKeyFormatted = ucwords(str_replace('-', ' ', $request->input('search')));
                    $this->applyKeywordFilter($request, $searchKeyFormatted, $mapDataQuery, $listingDataQuery);
                }
            }
    }

    private function applyKeywordFilter(Request $request, $searchKey, $mapDataQuery, $listingDataQuery)
    {
        // $keywordMappings = [
        //     'diamond' => 'diamond',
        //     'featured' => 'featured',
        // ];
    
        // foreach ($keywordMappings as $key => $column) {
        //     if (strpos(strtolower($searchKey), $key) !== false) {
        //         $mapDataQuery->where($column, 1);
        //         $listingDataQuery->where($column, 1);
        //         return;
        //     }
        // }
    
        // Check if the page_name is 'City'
        if ($request->input('page_name') === 'City') {
            $mapDataQuery->where('City', '=', $searchKey);
            $listingDataQuery->where('City', '=', $searchKey);
        } else {
            $mapDataQuery->where(function ($query) use ($searchKey) {
                $query->where('City', $searchKey)
                    ->orWhere('ListingId', 'like', '%' . $searchKey . '%')
                    ->orWhere('SubdivisionName', 'like', '%' . $searchKey . '%')
                    ->orWhere('PostalCode', 'like', '%' . $searchKey . '%')
                    ->orWhere(DB::raw("CONCAT(TRIM(UnparsedAddress), ', ', TRIM(City), ', ', TRIM(StateOrProvince))"), $searchKey );
                    // ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(OtherColumns, '$.PostalCode'))"), 'like', '%' . $searchKey . '%');
            });
        
    
            $listingDataQuery->where(function ($query) use ($searchKey) {
                $query->where('City', $searchKey)
                    ->orWhere('ListingId',$searchKey )
                    ->orWhere('SubdivisionName',  $searchKey )
                    ->orWhere('PostalCode',$searchKey)
                    ->orWhere(DB::raw("CONCAT(TRIM(UnparsedAddress), ', ', TRIM(City), ', ', TRIM(StateOrProvince))"),  $searchKey);
                    // ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(OtherColumns, '$.PostalCode'))"), 'like', '%' . $searchKey . '%');
            });
        }
    }
    

   private function applyRangeFilters(Request $request, $mapDataQuery, $listingDataQuery)
{
    // List Price Filters
    if ($request->filled('min_list_price')) {
        $value = $request->input('min_list_price');
        $mapDataQuery->whereRaw('ListPrice >= ?', [$value]);
        $listingDataQuery->whereRaw('ListPrice >= ?', [$value]);
    }
    if ($request->filled('max_list_price')) {
        $value = $request->input('max_list_price');
        $mapDataQuery->whereRaw('ListPrice <= ?', [$value]);
        $listingDataQuery->whereRaw('ListPrice <= ?', [$value]);
    }

    // Year Built Filters
    if ($request->filled('min_year_built')) {
        $value = $request->input('min_year_built');
        $mapDataQuery->whereRaw('CAST(YearBuilt AS UNSIGNED) >= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(YearBuilt AS UNSIGNED) >= ?', [$value]);
    }
    if ($request->filled('max_year_built')) {
        $value = $request->input('max_year_built');
        $mapDataQuery->whereRaw('CAST(YearBuilt AS UNSIGNED) <= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(YearBuilt AS UNSIGNED) <= ?', [$value]);
    }

    // Square Feet Filters with Casting
    if ($request->filled('min_sqft')) {
        $value = $request->input('min_sqft');
        $mapDataQuery->whereRaw('CAST(LivingAreaSF AS UNSIGNED) >= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(LivingAreaSF AS UNSIGNED) >= ?', [$value]);
    }
    if ($request->filled('max_sqft')) {
        $value = $request->input('max_sqft');
        $mapDataQuery->whereRaw('CAST(LivingAreaSF AS UNSIGNED) <= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(LivingAreaSF AS UNSIGNED) <= ?', [$value]);
    }

    // Acres Filters with Casting
    if ($request->filled('min_acrs')) {
        $value = $request->input('min_acrs');
        $mapDataQuery->whereRaw('CAST(LotSizeAcres AS UNSIGNED) >= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(LotSizeAcres AS UNSIGNED) >= ?', [$value]);
    }
    if ($request->filled('max_acrs')) {
        $value = $request->input('max_acrs');
        $mapDataQuery->whereRaw('CAST(LotSizeAcres AS UNSIGNED) <= ?', [$value]);
        $listingDataQuery->whereRaw('CAST(LotSizeAcres AS UNSIGNED) <= ?', [$value]);
    }

    // Property Type
    if ($request->filled('property_type')) {
    $propertyType = $request->input('property_type');

    // Apply condition based on PropertyType
    if ($propertyType === 'Condo') {
        // If the property type is 'Condo', filter by multiple subtypes
        $mapDataQuery->whereIn('PropertySubtype', ['Apartment', 'Row/Townhouse']);
        $listingDataQuery->whereIn('PropertySubtype', ['Apartment', 'Row/Townhouse']);
    }else {
        $mapDataQuery->where('PropertyType', $propertyType);
        $listingDataQuery->where('PropertyType', $propertyType);
    }
}

    // Building Type
    if ($request->filled('building_type')) {
        $buildingType = $request->input('building_type');
        $mapDataQuery->where('BuildingType', $buildingType);
        $listingDataQuery->where('BuildingType', $buildingType);
    }

    // Listing ID
    if ($request->filled('listing_id')) {
        $listingId = $request->input('listing_id');
        $mapDataQuery->where('ListingId', $listingId);
        $listingDataQuery->where('ListingId', $listingId);
    }

    // Community
    if ($request->filled('community')) {
        $community = ucwords(str_replace('-', ' ', $request->input('community')));
        $mapDataQuery->where('SubdivisionName', $community);
        $listingDataQuery->where('SubdivisionName', $community);
    }

    // Pets Allowed
    if ($request->filled('pets_allowed')) {
        $petsAllowed = ucwords($request->input('pets_allowed'));
        $mapDataQuery->where('PetsAllowed', $petsAllowed);
        $listingDataQuery->where('PetsAllowed', $petsAllowed);
    }

    // Bedrooms
    if ($request->filled('min_bedrooms')) {
        $minBedrooms = $request->input('min_bedrooms');
        if (strpos($minBedrooms, '+') !== false) {
            $numericMinBedrooms = (int)str_replace('+', '', $minBedrooms);
            $mapDataQuery->where('BedroomsTotal', '>', $numericMinBedrooms);
            $listingDataQuery->where('BedroomsTotal', '>', $numericMinBedrooms);
        } else {
            $mapDataQuery->where('BedroomsTotal', '=', $minBedrooms);
            $listingDataQuery->where('BedroomsTotal', '=', $minBedrooms);
        }
    }

    // Bathrooms
    if ($request->filled('min_bathrooms')) {
        $minBathrooms = $request->input('min_bathrooms');
        if (strpos($minBathrooms, '+') !== false) {
            $numericMinBathrooms = (int)str_replace('+', '', $minBathrooms);
            $mapDataQuery->where('BathroomsFull', '>', $numericMinBathrooms);
            $listingDataQuery->where('BathroomsFull', '>', $numericMinBathrooms);
        } else {
            $mapDataQuery->where('BathroomsFull', '=', $minBathrooms);
            $listingDataQuery->where('BathroomsFull', '=', $minBathrooms);
        }
    }
    
    if ($request->filled('just_listed') && $request->just_listed == 'true') {
        $twoDaysAgo = now()->subDays(2)->toDateString();
        $mapDataQuery->whereRaw('DATE(DOMDate) >= ?', [$twoDaysAgo]);
        $listingDataQuery->whereRaw('DATE(DOMDate) >= ?', [$twoDaysAgo]);
    }
}

    // private function applyCustomFilters(Request $request, $mapDataQuery, $listingDataQuery)
    // {
    //     $filters = [
    //         'featured' => 'featured',
    //         'diamond' => 'diamond',
    //     ];

    //     foreach ($filters as $input => $column) {
    //         if ($request->boolean($input)) {
    //             $mapDataQuery->where($column, 1);
    //             $listingDataQuery->where($column, 1);
    //         }
    //     }
    // }

    private function applySorting(Request $request, $listingDataQuery)
    {
        if ($request->filled('sort_by')) {
            $sortOrder = $request->input('sort_by');
            switch ($sortOrder) {
                case 'asc_list':
                    $listingDataQuery->orderBy('ListPrice', 'asc');
                    break;
                case 'desc_list':
                    $listingDataQuery->orderBy('ListPrice', 'desc');
                    break;
                case 'asc_dom':
                    $listingDataQuery->orderBy('DOMDate', 'asc');
                    break;
                case 'desc_dom':
                    $listingDataQuery->orderBy('DOMDate', 'desc');
                    break;
                default:
                    break;
            }
        } else {
            // Apply default sorting logic
            $listingDataQuery->orderByRaw("CASE WHEN featured = 1 THEN 0 ELSE 1 END")
                             ->orderBy('DOMDate', 'desc');
        }
    }


    public function getLocations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $apiKey = env("YELP_API");
        // $radius = env("RADIUS");
        $radius = 10000;

        $categories = 'active,arts,auto,beautysvc,education,eventservices,financialservices,food,health,localservices,hotelstravel,localflavor,media,nightlife,pets,professional,services,government,religiousorg,restaurants,shopping,schools,parks';

        $client = new Client();
        $response = $client->get("https://api.yelp.com/v3/businesses/search?longitude=$longitude&latitude=$latitude&radius=$radius&categories=$categories", [
            'headers' => [
                'Authorization' => "Bearer $apiKey",
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ]);
    }


    public function getCitiesWithSubdivisions(Request $request)
    {
        $subdivisionsQuery = Properties::select('SubdivisionName');
        $propertiesQuery = Properties::select('PropertyType');
    
        if ($request->has('city')) {
            $city = strtolower($request->input('city')); // Convert city to lowercase
            
            if ($city === 'diamond') {
                $subdivisionsQuery->where('diamond', 1);
                $propertiesQuery->where('diamond', 1);
            } elseif ($city === 'featured') {
                $subdivisionsQuery->where('featured', 1);
                $propertiesQuery->where('featured', 1);
            } else {
                $subdivisionsQuery->where('City', $city);
                $propertiesQuery->where('City', $city);
            }
        }
    
        $subdivisionNames = $subdivisionsQuery
            ->whereNotNull('SubdivisionName')
            ->where('SubdivisionName', '<>', '')
            ->where('SubdivisionName', '<>', 'NONE')
            ->where('SubdivisionName', '<>', 'N/A')
            ->distinct('SubdivisionName')
            ->orderBy('SubdivisionName')
            ->pluck('SubdivisionName')
            ->toArray();
    
        $propertyTypes = $propertiesQuery
            ->whereNotNull('PropertyType')
            ->where('PropertyType', '<>', '')
            ->where('PropertyType', '<>', 'NONE')
            ->distinct('PropertyType')
            ->orderBy('PropertyType')
            ->pluck('PropertyType')
            ->toArray();
    
        // Filter out 'Rental' type
        $propertyTypes = array_filter($propertyTypes, function ($type) {
            return $type !== 'Rental';
        });
    
        // Define the desired order
        $desiredOrder = [
            'Residential',
            'Commercial',
            'Land',
            'Mobile',
            'Multi-Family',
            'Agri-Business',
        ];
    
        // Add a hardcoded 'Apartment' PropertyType
$propertyTypes[] = 'Condo';

// Sort the property types according to the desired order
usort($propertyTypes, function ($a, $b) use ($desiredOrder) {
    $posA = array_search($a, $desiredOrder);
    $posB = array_search($b, $desiredOrder);

    // If the element is not found in the desired order, place it at the end
    if ($posA === false)
        $posA = count($desiredOrder);
    if ($posB === false)
        $posB = count($desiredOrder);

    return $posA - $posB;
});

    
        // Create the city data array without BuildingType
        $cityData = [
            'SubdivisionName' => $subdivisionNames,
            'PropertyType' => $propertyTypes,
        ];
    
        return response()->json($cityData);
    }
    

    public function getStaff()
    {
        try {
            $staff = User::where('role', 2)
                ->take(9)
                ->get();

            if ($staff->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff found.',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff data retrieved successfully.',
                'data' => $staff
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff data.',
                'data' => null
            ], 500);
        }
    }

    public function getCommunity()
    {
        $distinctCommunity = DB::table('properties_all_data')->whereNotNull('SubdivisionName')->distinct()->pluck('SubdivisionName');
        return [
            'community' => $distinctCommunity,
        ];
    }


    public function propertyType()
    {
        $distinctProperty = DB::table('properties_all_data')->whereNotNull('PropertyType')->distinct()->pluck('PropertyType');
        return [
            'property_type' => $distinctProperty,
        ];
    }

    public function autoList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $key = $request->input('key');
    
        // Optimized query with DISTINCT applied correctly
        $results = DB::table('properties_all_data')
            ->where(function ($query) use ($key) {
                $query->where('ListingId', 'like', '%' . $key . '%')
                    ->orWhere('UnparsedAddress', 'like', '%' . $key . '%')
                    ->orWhere('City', 'like', '%' . $key . '%')
                    ->orWhere('StateOrProvince', 'like', '%' . $key . '%')
                    ->orWhere('PostalCode', 'like', '%' . $key . '%');
            })
            ->distinct()
            ->select(
                'ListingId',
                DB::raw('CONCAT(ListingId, " - ", UnparsedAddress) as mls_id_address'),
                DB::raw('CONCAT(
                    TRIM(COALESCE(UnparsedAddress, "")), 
                    ", ", 
                    TRIM(COALESCE(City, "")), 
                    ", ", 
                    TRIM(COALESCE(StateOrProvince, "")), 
                    ", ", 
                    TRIM(COALESCE(PostalCode, ""))
                ) as full_address'),
                'slug_url'
            )
            ->take(5)
            ->get();
    
        // Extract the results into separate collections
    // Extract the results into separate collections
    $listingIds = $results->pluck('ListingId')->filter()->unique()->take(5);
    $mls_ids_with_address = $results->pluck('mls_id_address')->filter()->unique()->take(5);
    $fullAddress = $results->pluck('full_address')->filter()->unique()->take(5);
    $slugUrls = $results->pluck('slug_url')->filter()->unique()->take(5);

    // Prepare the response array
    $suggestedAgents = [
        'listingIds' => $listingIds,
        'mls_ids' => $mls_ids_with_address,
        'fullAddress' => $fullAddress,
        'slug_urls' => $slugUrls,
    ];

    // Check if all arrays are empty
    if ($suggestedAgents['listingIds']->isEmpty() && $suggestedAgents['mls_ids']->isEmpty() && $suggestedAgents['fullAddress']->isEmpty() && $suggestedAgents['slug_urls']->isEmpty()) {
        return response()->json(['message' => 'No Results found for the provided key.'], 404);
    }

    return response()->json(['suggested_agents' => $suggestedAgents], 200);
}

    public function agentslug(Request $request)
    {
        $name = $request->input('agent_name');
        $user = User::where('name', $name)->first();
        if ($user) {
            return response()->json([
                'slug_url' => $user->slug_url,
                'phone' => $user->phone,
                'office_no' => $user->office_no
            ]);
        }
        return response()->json([], 200);
    }
    
    public function getpropertyreviews(Request $request)
    {
        $sortBy = $request->query('sortBy', '');
        $listing_id = $request->input('listing_id');

        try {
            $query = PropertyReview::where('listing_id', $listing_id);

            if ($sortBy === 'asc') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'desc') {
                $query->orderBy('created_at', 'desc');
            }

            $reviews = $query->paginate(10);

            return response()->json(['reviews' => $reviews], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getsearchresult(Request $request)
    {
        
        // Validation rules for each field
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'nullable|string',
            'duration' => 'nullable|string',
            'city' => 'nullable|string',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'beds' => 'nullable',
            'bath' => 'nullable',
            'community' => 'nullable|string',
            'property_type' => 'nullable|string',
            'min_sqft' => 'nullable|integer',
            'max_sqft' => 'nullable|integer',
            'min_acres' => 'nullable|numeric',
            'max_acres' => 'nullable|numeric',
            'min_yearbuilt' => 'nullable|integer',
            'max_yearbuilt' => 'nullable|integer',
            'furnishedCheckbox' => 'nullable',
            'petsCheckbox' => 'nullable',
            'fireplace' => 'nullable',
            'onegarage' => 'nullable',
            'twogarage' => 'nullable',
            'threegarage' => 'nullable',
            'onestory' => 'nullable',
            'twostories' => 'nullable',
            'threestories' => 'nullable',
            'deck' => 'nullable',
            'basement' => 'nullable',
            'airconditioning' => 'nullable',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $savedSearch = new SavedSearch();

        $savedSearch->fill($request->all());

        $savedSearch->allColumns = json_encode($request->except(['user_id']));
        $savedSearch->sent_at = now();

        $savedSearch->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Search saved successfully',
            'data' => $savedSearch,
        ]);
    }

    public function forgotpassword(Request $request)
    {
        try {
            // Check if user exists
            $user = DB::table('leads')->where('email', $request->email)->first();
    
            if (!$user) {
                return response()->json(['message' => "Sorry, email address doesn't exist"], 200);
            }
    
            // Generate token and set expiration
            $token = Str::random(64);
            $expiration = now()->addMinutes(5);
    
            // Update user token details
            DB::table('leads')
                ->where('email', $request->email)
                ->update([
                    'token' => $token,
                    'token_expires_at' => $expiration,
                    'change_pass_status' => 0,
                    'created_at' => now()
                ]);
    
            $latestAdmin = User::where('role', 1)->latest()->first();
            $adminname = $latestAdmin->name;
            $adminemail = $latestAdmin->email;
    
            $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
    
            $email = [
                'from' => 'Team REP <' . env('MAIL_FROM_ADDRESS') . '>',
                'to' => $request->email,
                'subject' => 'Reset Password',
                'html' => view('resetpasswordfront', [
                    'token' => $token,
                    'adminemail' => $adminemail,
                    'adminname' => $adminname,
                    'name' => $user->name
                ])->render()
            ];
    
            $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $email);
    
            return response()->json(['message' => "Email sent successfully"], 200);
    
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email: " . $e->getMessage());
            return response()->json(['message' => "Failed to send email: " . $e->getMessage()], 500);
        }
    }
    
    public function gettopPropertyCount()
    {
        /*$cityInfo = CityProperty::select('city_name as City')
            ->selectSub(function ($query) {
                $query->from('properties_all_data')
                    ->whereColumn('properties_all_data.City', 'city_properties.city_name');
            }, 'properties_count')
            ->orderByDesc('properties_count')
            ->limit(10)
	    ->get();*/
    $cityInfo = CityProperty::select('city_name as City')
    ->leftJoin('properties_all_data', 'properties_all_data.City', '=', 'city_properties.city_name')
    ->selectRaw('count(properties_all_data.City) as properties_count')
    ->groupBy('city_properties.city_name')
    ->orderByDesc('properties_count')
    ->limit(10)
    ->get();
        return response()->json($cityInfo);
    }
    
    public function getStateCitiesWithSubdivision(Request $request)
    {
        // Select the required columns including StateOrProvince
        $data = Properties::select('City', 'SubdivisionName', 'StateOrProvince')
            ->whereNotNull('City')
            ->where('City', '<>', '')
            ->whereNotNull('SubdivisionName')
            ->where('SubdivisionName', '<>', '')
            ->distinct()
            ->get()
            ->groupBy(['StateOrProvince', 'City'])
            ->map(function ($cities) {
                return $cities->map(function ($items) {
                    $subdivisions = $items->pluck('SubdivisionName')
                        ->reject(function ($value) {
                            return in_array($value, ['NONE', 'N/A']);
                        })
                        ->unique()
                        ->values();
                    
                    // If all subdivisions are excluded, return ['NONE']
                    return $subdivisions->isEmpty() ? collect(['NONE']) : $subdivisions;
                });
            });
    
        // Format the data as required
        $formattedData = $data->mapWithKeys(function ($cities, $state) {
            return [$state => $cities];
        });
    
        // Convert the data to JSON
        $jsonData = $formattedData->toJson(JSON_PRETTY_PRINT);
    
        // Define the filename
        $fileName = 'cities_with_subdivisions.json';
    
        // Define the headers
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
    
        // Return the JSON response as a file download
        return Response::make($jsonData, 200, $headers);
    }

    
}