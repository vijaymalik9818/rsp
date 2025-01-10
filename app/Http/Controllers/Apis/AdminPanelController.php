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
use App\Mail\ThankYouEmail;
use App\Models\Leads;
use App\Models\PropertyReview;
use App\Models\SavedSearch;
use App\Models\Tour;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CityProperty;
// use App\Models\Tour;
use Aws\S3\S3Client;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
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

    // public function updateCity(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'id' => 'required',
    //             'city_image' => 'image|mimes:jpeg,png,jpg,|',
    //         ]);

    //         $city = CityProperty::findOrFail($validatedData['id']);

    //         if ($city) {
    //             if ($request->hasFile('city_image')) {
    //                 $image = $request->file('city_image');
    //                 $imageName = $image->hashName();
    //                 $image->storeAs('public/city_images', $imageName);
    //                 $city->image = url('storage/city_images/' . $imageName); 
    //             }
    //             $city->save();

    //             return response()->json(['success' => true, 'message' => 'City status updated successfully']);
    //         } else {
    //             return response()->json(['error' => 'City not found']);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }
    
    

    public function updateCity(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
                'city_image' => 'image|mimes:jpeg,png,jpg|max:2048', // Added max size validation
            ]);
    
            $city = CityProperty::findOrFail($validatedData['id']);
    
            if ($city) {
                if ($request->hasFile('city_image')) {
                    $image = $request->file('city_image');
                    $imageName = $image->hashName(); // Generate unique name
                    $imagePath = $image->getRealPath(); // Get the path to the image
    
                    $cityNameSanitized = str_replace(' ', '_', strtolower($city->city_name)); 
    
                    $s3 = new S3Client([
                        'version' => 'latest',
                        'region'  => env('AWS_DEFAULT_REGION'),
                        'credentials' => [
                            'key'    => env('AWS_ACCESS_KEY_ID'),
                            'secret' => env('AWS_SECRET_ACCESS_KEY'),
                        ],
                    ]);
    
                    $s3Key = "property-city-images/{$cityNameSanitized}/photo-{$cityNameSanitized}.jpeg";
    
                    $result = $s3->putObject([
                        'Bucket' => env('AWS_BUCKET'),
                        'Key'    => $s3Key,
                        'SourceFile' => $imagePath,
                        'ContentType' => $image->getClientMimeType(),
                    ]);
    
                    $city->image = $result['ObjectURL'];
                }
    
                $city->save();
    
                return response()->json(['success' => true, 'message' => 'City status updated successfully']);
            } else {
                return response()->json(['error' => 'City not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function updatecitystatus(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|exists:city_properties,id',
                'status' => 'required|boolean',
            ]);

            $city = CityProperty::findOrFail($validatedData['id']);

            if ($city) {
                $city->status = $validatedData['status'] ? 1 : 0;
                $city->save();
                return response()->json(['success' => true, 'message' => 'City status updated successfully']);
            } else {
                return response()->json(['error' => 'City not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


}
