<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\FavoriteProperty;
use App\Traits\ApiResponse;
use App\Models\Lead;
use App\Models\Properties;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// use App\Models\Tour;

class LeadsController extends Controller
{
    use ApiResponse;
    public function profileEdit(Request $request)
    {
        $user_id =  $request->input('user_id');
        try {
            $lead = Lead::findOrFail($user_id);

            return response()->json(['message' => 'Lead fetched successfully', 'lead' => $lead], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Lead not found'], 404);
        }
    }
    public function updateProfile(Request $request, $id)
    {
        try {
            $lead = Lead::findOrFail($id);

            $lead->update([
                'email' => $request->input('email'),
                'name' => $request->input('fullname'),
                'phone' => $request->input('mobilenumber'),
                'role' => $request->input('role'),
            ]);

            return response()->json(['message' => 'Profile updated successfully', 'lead' => $lead]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating profile: ' . $e->getMessage()], 500);
        }
    }
    public function getsavesearch($userId)
    {
        try {
            $savedSearches = SavedSearch::where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
    
            return response()->json(['message' => 'Saved searches retrieved successfully', 'saved_searches' => $savedSearches]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving saved searches: ' . $e->getMessage()], 500);
        }
    }
    

    public function searchdelete(Request $request)
    {
        $searchId = $request->json('search_id');
      
        $search = SavedSearch::find($searchId);
        
        if (!$search) {
            return response()->json(['error' => 'Search not found'], 404);
        }
        
        $search->delete();
        
        return response()->json(['message' => 'Search deleted successfully'], 200);
    }

    public function addToFavorites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'property_id' => 'required|string',
            'favorite' => 'required|integer|in:0,1', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
    
        try {
            $userId = $request->input('user_id');
            $propertyId = $request->input('property_id');
            $favoriteValue = $request->input('favorite');
    
            $existingFavorite = FavoriteProperty::where('user_id', $userId)
                ->where('property_id', $propertyId)
                ->first();
    
            if ($existingFavorite) {
                if ($favoriteValue == 1) {
                    return response()->json(['message' => 'Property is already in favorites'], 200);
                } else {
                    $existingFavorite->delete();
                    return response()->json(['message' => 'Property removed from favorites successfully'], 200);
                }
            }
    
            if ($favoriteValue == 0) {
                return response()->json(['message' => 'Property is not in favorites'], 200);
            }
    
            $favorite = new FavoriteProperty();
            $favorite->user_id = $userId;
            $favorite->property_id = $propertyId;
            $favorite->save();
            return response()->json(['message' => 'Property added to favorites successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add/remove property to/from favorites'], 500);
        }
    }
    
    
  public function getUserFavoriteProperties(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 400);
    }

    try {
        // Get favorite property IDs for the user
        $userFavoriteProperties = FavoriteProperty::where('user_id', $request->user_id)->pluck('property_id');

        // Query favorite properties with conditional PropertyType
        $favoritePropertiesData = Properties::select(
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
            ->whereIn('ListingId', $userFavoriteProperties)
            ->paginate(4);

        return response()->json(['favorite_properties' => $favoritePropertiesData], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch favorite properties'], 500);
    }
}

    
    public function getfavoritelisting(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 400);
    }

    try {
        $userFavoriteProperties = FavoriteProperty::where('user_id', $request->user_id)->pluck('property_id');

        $favoritePropertiesIds = Properties::whereIn('ListingId', $userFavoriteProperties)->pluck('ListingId');

        return response()->json(['favorite_properties' => $favoritePropertiesIds], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch favorite properties'], 500);
    }
}
}