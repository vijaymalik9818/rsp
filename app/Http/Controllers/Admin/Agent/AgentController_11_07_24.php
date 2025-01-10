<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\ContactUs;
use App\Models\JoinRep;
use App\Models\Properties;
use App\Models\CityProperty;
use App\Models\PropertyReview;
use App\Models\Tour;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Mail\SendPasswordEmail;
use Mailgun\Mailgun;

// use OpenApi\Annotations\Property;

class AgentController extends Controller
{

    public function store(Request $request)
    {
        Log::info('Come in agent controller class store function');
        try {
            $randomPassword = Str::random(8);
            $role = $request->input('agent_role');
            $id = $request->input('id-field');

            $fullName = $request->input('agent_first') . ' ' . $request->input('agent_last');
            $choices = $request->input('choices-single-default');
            $languages = is_array($choices) ? implode(',', $choices) : $choices;

            if ($id) {
                $user = User::findOrFail($id);
                $slug = $user->slug_url;
            } else {
                $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fullName), '-'));
                $slug = $baseSlug;
                $existingUsersCount = User::where('slug_url', $baseSlug)->count();
                if ($existingUsersCount > 0) {
                    $i = 1;
                    do {
                        $slug = $baseSlug . '-' . $i;
                        $i++;
                    } while (User::where('slug_url', $slug)->exists());
                }
            }
            if ($role == 2) {

                $id = $request->input('id-field-staff');

                $validator = Validator::make($request->all(), [
                    'agent_email' => 'nullable|email|max:255',
                    'agent_position' => 'required',

                    'agent_description' => 'required|string',
                    'agent_phone' => 'nullable',

                    'facebook' => 'nullable|string',
                    'linkedin' => 'nullable|string',
                    'twitter' => 'nullable|string',

                    'instagram' => 'nullable|string',

                ]);

                if ($validator->fails()) {
                    dd('here');
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                Log::info('Validator success in store function');
                $phoneNumber = intval(preg_replace('/[^0-9]/', '', $request->input('agent_phone')));

                $profile_image = "";
                $profile_image2 = "";


                if ($request->hasFile('agent_profile')) {
                    $imageName1 = time() . '_1.' . $request->agent_profile->extension();
                    $request->agent_profile->move(public_path('/storage/images/'), $imageName1);
                    $profile_image = $imageName1;
                } elseif (!empty($request->input('old_image'))) {
                    $profile_image = $request->input('old_image');
                    $profile_image = str_replace('storage/images/', '', $profile_image);
                }
                if ($request->hasFile('agent_profile2')) {
                    $imageName2 = time() . '_2.' . $request->agent_profile2->extension();
                    $request->agent_profile2->move(public_path('/storage/images/'), $imageName2);
                    $profile_image2 = $imageName2;
                } elseif (!empty($request->input('old_image2'))) {
                    $profile_image2 = $request->input('old_image2');
                    $profile_image2 = str_replace('storage/images/', '', $profile_image2);
                }

                $userAttributes = [
                    'name' => $fullName,
                    'slug_url' => $slug,
                    'email' => $request->input('agent_email'),
                    'phone' => $phoneNumber,
                    'position' => $request->input('agent_position'),

                    'description' => $request->input('agent_description'),
                    'facebook' => $request->input('facebook'),
                    'linkedin' => $request->input('linkedin'),
                    'twitter' => $request->input('twitter'),

                    'instagram' => $request->input('instagram'),
                    'role' => $request->input('agent_role'),
                    'profile_picture' => 'storage/images/' . $profile_image,
                    'other_profile_picture' => 'storage/images/' . $profile_image2,
                    'password' =>  'realtor@123',
                ];
                // dd($userAttributes); 
                if ($id) {
                    User::findOrFail($id)->update($userAttributes);
                } else {
                    Log::info('come in inserted data ' . json_encode($userAttributes));
                    $user = User::create($userAttributes);
                }
                return redirect()->back()->with('success', 'Agent ' . ($id ? 'updated' : 'added') . ' successfully!');
            } else {


                $validator = Validator::make($request->all(), [
                    'agent_email' => 'required|email|max:255',
                    'agent_position' => 'required',
                    'agent_address' => 'required',
                    'agent_description' => 'required|string',
                    'agent_phone' => 'required',
                    'agent_office' => 'nullable',
                    // 'agent_fax' => 'nullable',
                    'status' => 'required|string',
                    'agent_mls' => 'nullable|string',
                    'agent_logo' => 'nullable|image|max:1024',
                    'facebook' => 'nullable|string',
                    'linkedin' => 'nullable|string',
                    'twitter' => 'nullable|string',
                    'youtube' => 'nullable|string',
                    'instagram' => 'nullable|string',
                    'language' => 'nullable|string',
                    'agent_website' => 'nullable|string',
                    'specialisation' => 'nullable|string',
                    'designation' => 'nullable|string',
                    'agent_password' => 'nullable'
                ]);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $useremail = $request->input('agent_email');
                Log::info('Validator success in store function');
                $phoneNumber = intval(preg_replace('/[^0-9]/', '', $request->input('agent_phone')));
                // $faxnumber = intval(preg_replace('/[^0-9]/', '', $request->input('agent_fax')));
                $officenumber = intval(preg_replace('/[^0-9]/', '', $request->input('agent_office')));
                $profile_image = "";
                $logo_image = "";

                if ($request->hasFile('agent_profile')) {
                    $imageName = time() . '.' . $request->agent_profile->extension();
                    $request->agent_profile->move(public_path('/storage/images/'), $imageName);
                    $profile_image = $imageName;
                } elseif (!empty($request->input('old_image'))) {
                    $profile_image = $request->input('old_image');
                    $profile_image = str_replace('storage/images/', '', $profile_image);
                }

                if ($request->hasFile('agent_logo')) {
                    $logoName = time() . '.' . $request->agent_logo->extension();
                    $request->agent_logo->move(public_path('/storage/images/'), $logoName);
                    $logo_image = $logoName;
                } elseif (!empty($request->input('old_logo'))) {
                    $logo_image = $request->input('old_logo');
                    $logo_image = str_replace('storage/images/', '', $logo_image);
                }


                $userAttributes = [
                    'name' => $fullName,
                    'slug_url' => $slug,
                    'email' => $request->input('agent_email'),
                    'phone' => $phoneNumber,
                    'position' => $request->input('agent_position'),
                    'office_no' => $officenumber,
                    'status' => $request->input('status'),
                    // 'fax_no' => $faxnumber,
                    'mls_id' => $request->input('agent_mls'),
                    'address' => $request->input('agent_address'),
                    'description' => $request->input('agent_description'),
                    'facebook' => $request->input('facebook'),
                    'linkedin' => $request->input('linkedin'),
                    'twitter' => $request->input('twitter'),
                    'youtube' => $request->input('youtube'),
                    'instagram' => $request->input('instagram'),
                    'website' => $request->input('agent_website'),
                    'profile_picture' => 'storage/images/' . $profile_image,
                    'agent_logo' => 'storage/images/' . $logo_image,
                    'language' => $languages,
                    // 'password' => Hash::make($randomPassword),
                    'specialisation' => $request->input('specialisation'),
                    'designation' => $request->input('designation'),
                    // 'password'  => $request->input('agent_password')
                ];
                // dd($fullName);
                // dd($userAttributes);
                if ($id) {
                    $userAttributes['password'] = Hash::make($request->input('agent_password'));
                    User::findOrFail($id)->update($userAttributes);
                } else {
                    $userAttributes['password'] = Hash::make($randomPassword);
                    $user= Auth::user();
                    if ($user->role === 1) {
                        $latestAdmin = User::where('role', 1)->latest()->first();
             
                      $adminname = $latestAdmin->name;
                      $adminemail = $latestAdmin->email;
             
                     
                    } 
                    Log::info('come in inserted data ' . json_encode($userAttributes));
                    $user = User::create($userAttributes);
                    try {
                        $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));

                        $email = [
                            'from' => env('MAIL_FROM_ADDRESS'),
                            'to' => $request->input('agent_email'),
                            'subject' => 'Welcome to Real Estate Professionals Inc.!',
                            'html' => view('emails.sendpass', [
                                'randomPassword' => $randomPassword,
                                'email' => $request->input('agent_email'),
                                'name' => $fullName,
                                'adminname' => $adminname,
                                'adminemail' => $adminemail
                            ])->render()
                        ];
                
                        $mgClient->messages()->send(env('MAILGUN_DOMAIN'), $email);
                
                    } catch (\Exception $e) {
                        Log::error("Failed to send email: " . $e->getMessage());
                    }
              
                }
                return redirect()->back()->with('success', 'Agent ' . ($id ? 'updated' : 'added') . ' successfully!');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
            FacadesLog::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing the request.');
        }
    }

    public function getUsersemail(Request $request)
    {
        $email = $request->input('email');
        $userExists = User::withTrashed()->where('email', $email)->count();
        if ($userExists > 0) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    public function showAgents(Request $request)
    {
        $user = Auth::user();
        if ($user->role != 1) {
            $encryptedId = base64_encode($user->id);
            return redirect('/agent-details/' . $encryptedId);
        }


        $perPage = 8;
        $page = $request->input('page', 1);
        $skip = ($page - 1) * $perPage;
        $agents = User::whereNull('role')
            ->orderBy('created_at', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->paginate($perPage);

        $positions = config('positions.positions');
        $languages = config('languages.languages');
        $total_agent = User::whereNull('role')->count();
        return view('apps-crm-leads', compact('agents', 'positions', 'languages', 'total_agent'));
    }
    public function showStaff(Request $request)
    {
        $perPage = 8;
        $page = $request->input('page', 1);
        $skip = ($page - 1) * $perPage;
        $user = Auth::user();
        if ($user->role != 1) {
            $encryptedId = base64_encode($user->id);
            return redirect('/agent-details/' . $encryptedId);
        }
        $agents = User::where('role', 2)
            ->orderBy('created_at', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->paginate($perPage);

        $positions = config('positions.positions');
        $total_agent = User::where('role', 2)->count();
        return view('staff', compact('agents', 'total_agent', 'positions'));
    }

    public function getagents(Request $request)
    {
        $perPage = 8;
        $page = $request->input('page', 1);
        $skip = ($page - 1) * $perPage;

        $agents = User::where('role', 2)
            ->orderBy('created_at', 'desc')
            ->skip($skip)
            ->take($perPage)
            ->get();

        $idCounter = $skip + 1;

        $agentsWithIds = $agents->map(function ($agent) use (&$idCounter) {
            $agent['ids'] = $idCounter++;
            return $agent;
        });

        $totalAgents = User::where('role', 2)->count();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $agentsWithIds,
            $totalAgents,
            $perPage,
            $page
        );

        $paginator->setPath($request->url());

        return response()->json(['agent' => $paginator]);
    }





    public function editagents($id)
    {
        try {
            $agent = User::findOrFail($id);
            return response()->json(['agent' => $agent]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Agent not found'], 404);
        }
    }
    public function updateAgent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'agent_name' => 'required|max:255',
                'agent_email' => 'required|email|unique:users,email',
            ]);
            $agent = User::findOrFail($validatedData['id']);
            $agent->update([
                'name' => $validatedData['agent_name'],
                'email' => $validatedData['agent_email'],
            ]);
            if ($request->hasFile('agent_profile')) {
                $profileImage = $request->file('agent_profile');
                $profileImagePath = $profileImage->store('agent_images', 'public');
                $agent->profile_picture = Storage::url($profileImagePath);
            }
            if ($request->hasFile('agent_logo')) {
                $logoImage = $request->file('agent_logo');
                $logoImagePath = $logoImage->store('agent_images', 'public');
                $agent->agent_logo = Storage::url($logoImagePath);
            }
            $agent->save();
            return response()->json(['success' => true, 'agent' => $agent]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function deleteagents($id)
    {
        try {
            $agent = User::findOrFail($id);
            $agent->status = 0;
            $agent->save();
            $agent->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function agentview($id)
    {
        $user = Auth::user();
        $agentId = base64_decode($id);

        if ($user->role === 1 || $user->id == $agentId) {
            $user_id = base64_decode($id);
            $agent = User::findOrFail($user_id);
            $reviews = Review::where('review_to', $agent->id)
                ->select('reviews.*')
                ->get();
            $averageRating = $reviews->avg('rating');

            $agentPicture = $agent->profile_picture;
            $reviewFeedbacks = $reviews->pluck('review_feedback')->toArray();
            $positions = config('positions.positions');
            $languages = config('languages.languages');
            view()->share('agent', $agent);
            return view('agent-details', [
                'agent' => $agent,
                'positions' => $positions,
                'languages' => $languages,
                'agentPicture' => $agentPicture,
                'reviews' => $reviews,
                'averageRating' => $averageRating,
                'reviewFeedbacks' => $reviewFeedbacks,
            ]);
        } else {
            return redirect('/unauthorized');
        }
    }

    public function showProperties(Request $request, $id)
    {
        //properties
        try {
            $agentId = base64_decode($id);
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrors($validator->errors(), 'Validation Error');
            }

            $perPage = $request->input('per_page', 4);

            try {
                $users = User::findOrFail($agentId);
                $slugUrl = $users->slug_url;
                $user = User::where('slug_url', $slugUrl)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not found.'], 404);
            }

             $query = DB::table('properties_all_data')->Where('ListAgentFullName', $user->name)->orWhere('ListAgentKeyNumeric', $user->mls_id)->select('id', 'ListPrice', 'PropertySubType', 'PropertyType', 'City', 'ListingId', 'MlsStatus', 'diamond', 'UnparsedAddress', 'OtherColumns');

            $count = $query->count();

            $properties = $request->boolean('paginate')
                ? $query->paginate($perPage)->appends($request->query())
                : $query->simplePaginate($perPage)->appends($request->query());

            return response()->json(['property' => $properties, 'total_count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }


    public function deletedAgents()
    {
        try {
            $perPage = 8;
            $deletedAgents = User::onlyTrashed()->orderByDesc('deleted_at')->paginate($perPage);

            $ids = [];

            $startingId = ($deletedAgents->currentPage() - 1) * $perPage + 1;

            for ($i = 0; $i < $perPage; $i++) {
                $ids[] = $startingId++;
            }
            foreach ($deletedAgents as $key => $agent) {
                $agent->ids = $ids[$key];
            }

            return response()->json(['success' => true, 'deletedAgents' => $deletedAgents]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function getautosuggestion(Request $request)
    {

        $searchTerm = $request->input('term');
        $status = $request->input('status');

        if ($status != 2) {
            $usersQuery = User::whereNull('role')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%")
                        ->orWhere('phone', 'like', "%$searchTerm%");
                });

            if ($status == '') {
                $usersQuery->orderBy('created_at', 'desc');
            } else {
                $usersQuery->where('status', $status)->orderBy('created_at', 'desc');
            }
        } else {
            $usersQuery = User::onlyTrashed()
                ->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%")
                        ->orWhere('phone', 'like', "%$searchTerm%");
                });
            $usersQuery->orderBy('created_at', 'desc');
        }
        $users = $usersQuery->get(['name', 'email', 'phone']);
        return response()->json($users);
    }
    public function getautosuggestionstaff(Request $request)
    {

        $searchTerm = $request->input('term');

        $usersQuery = User::where('role', 2)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%$searchTerm%")
                    ->orWhere('email', 'like', "%$searchTerm%")
                    ->orWhere('phone', 'like', "%$searchTerm%");
            });

        $usersQuery->orderBy('created_at', 'desc');

        $users = $usersQuery->get(['name', 'email', 'phone']);
        return response()->json($users);
    }

    public function getpropertysuggestion(Request $request, $id)
    {
        try {
            $agentId = base64_decode($id);
            $searchTerm = $request->input('term');

            $validator = Validator::make($request->all(), [
                'term' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrors($validator->errors(), 'Validation Error');
            }

            try {
                $user = User::findOrFail($agentId);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $query = DB::table('properties_all_data')
                ->where('ListAgentKeyNumeric', $user->agent_key)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('ListingId', 'like', "%$searchTerm%")
                        ->orWhere('UnparsedAddress', 'like', "%$searchTerm%")
                        ->orWhere('City', 'like', "%$searchTerm%");
                })
                ->select('City', 'ListingId', 'UnparsedAddress', 'OtherColumns');

            $count = $query->count();

            $properties = $query->get();

            return response()->json(['properties' => $properties, 'total_count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }


    public function getUsersByStatus(Request $request)
    {
        $perPage = 8;
        $page = $request->input('page', 1);
        $status = $request->input('status');
    
        if ($status != 2) {
            $usersQuery = User::where(function ($query) use ($status) {
                if ($status !== null && $status !== '') {
                    $query->where('status', $status);
                }
            });
    
            if ($status === '' || $status === null) {
                $usersQuery->orderBy('id', 'desc');
            } else {
                $usersQuery->orderBy('updated_at', 'desc');
            }
    
            $users = $usersQuery
                ->whereNull('role')
                ->paginate($perPage);
    
            $ids = [];
            $startingId = ($page - 1) * $perPage + 1;
    
            for ($i = 0; $i < $perPage; $i++) {
                $ids[] = $startingId++;
            }
    
            foreach ($users as $key => $user) {
                $user->ids = $ids[$key];
            }
    
            return response()->json($users);
        } else {
            return $this->deletedAgents();
        }
    }
    



    public function showticketform()
    {
        $user = Auth::user();
        if ($user->role != 1) {
            return view('ticketmenu');
        } else {
            return redirect('/unauthorized');
        }
    }



    public function getAutoQuery(Request $request)
    {
        $perPage = 8;
        $query = $request->input('name') ?? $request->input('email') ?? $request->input('phone') ?? $request->input('query');
        $status = $request->input('status');

        $users = User::query()->whereNull('role');

        if ($status != 2) {
            $users->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%");
            });
        } else {
            $users->onlyTrashed()->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%");
            });
        }

        if ($status !== null && $status !== '') {
            $users->where('status', $status);
        }

        $users = $users->paginate($perPage);

        $ids = [];

        $startingId = ($users->currentPage() - 1) * $perPage + 1;

        for ($i = 0; $i < $perPage; $i++) {
            $ids[] = $startingId++;
        }

        foreach ($users as $key => $user) {
            $user->ids = $ids[$key];
        }

        return response()->json($users);
    }

    public function getAutoQuerystaff(Request $request)
    {
        $perPage = 8;
        $query = $request->input('name') ?? $request->input('email') ?? $request->input('phone') ?? $request->input('query');
        $page = $request->input('page', 1);

        $users = User::where('role', 2);

        $users->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->orWhere('phone', 'like', "%$query%");
        });

        $paginatedAgents = $users->paginate($perPage, ['*'], 'page', $page);

        $idCounter = ($page - 1) * $perPage + 1;

        $paginatedAgents->getCollection()->transform(function ($user) use (&$idCounter) {
            $user->ids = $idCounter++;
            return $user;
        });

        return response()->json($paginatedAgents);
    }


    public function getpropertyquery(Request $request, $id)
    {
        try {
            $agentId = base64_decode($id);
            try {
                $user = User::findOrFail($agentId);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not found.'], 404);
            }
            $query = $request->input('listingid') ?? $request->input('address') ?? $request->input('city') ?? $request->input('query');

            $properties = DB::table('properties_all_data')
                ->where('ListAgentKeyNumeric', $user->agent_key)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('ListingId', 'like', "%$query%")
                        ->orWhere('UnparsedAddress', 'like', "%$query%")
                        ->orWhere('City', 'like', "%$query%");
                })
                ->select('id', 'ListPrice', 'PropertySubType', 'PropertyType', 'City', 'ListingId', 'MlsStatus', 'diamond', 'UnparsedAddress', 'OtherColumns');
            $properties = $properties->paginate(8);

            return response()->json($properties);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }



    public function edit($encodedId)
    {
        $id = base64_decode($encodedId);

        $agent = User::findOrFail($id);

        $positions = config('positions.positions');
        $languages = config('languages.languages');

        return view('agent-edit', [
            'agent' => $agent,
            'positions' => $positions,
            'languages' => $languages,
        ]);
    }

    public function getAgentsFiltered(Request $request)
    {
        $perPage = 8;
        $status = $request->input('status');
        $language = $request->input('language');
        $query = $request->input('query');
if($status != 2){
        $queryBuilder = User::selectRaw('id, name, profile_picture, email, language, phone, address, mls_id, position, status')
            ->whereNull('role');

        if ($language) {
            $queryBuilder->where(function ($query) use ($language) {
                foreach ($language as $lang) {
                    $query->orWhere('language', 'LIKE', '%' . $lang . '%');
                }
            });
        }

        if ($query) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('address', 'like', '%' . $query . '%');
            });
        }
         if ($status !== null && $status !== '') {
                $queryBuilder->where('status', $status);
               
            }
        if ($status != 2) {
            if ($status === '' || $status === null) {
                $queryBuilder->orderBy('id', 'desc');
            } else {
                $queryBuilder->orderBy('updated_at', 'desc');
            }
            // if ($status !== null && $status !== '') {
            //     $queryBuilder->where('status', $status);
            //     // ->orderBy('created_at', 'desc');
            // }
        } else {
            $queryBuilder->onlyTrashed();
        }

        $agents = $queryBuilder->paginate($perPage);

        $idCounter = ($agents->currentPage() - 1) * $perPage + 1;

        $agents->getCollection()->transform(function ($agent) use (&$idCounter) {
            $agent->ids = $idCounter++;
            return $agent;
        });

        return response()->json($agents);
    }
    else {
        return $this->deletedAgents();
    }
    }

    public function ratingview($id)
    {
        $agentId = base64_decode($id);

        $agent = User::findOrFail($agentId);

        $reviews = Review::where('review_to', $agent->id)
        ->select('reviews.*')
        ->get();
        // dd($reviews);
        return view('ratingview', [
            'agent' => $agent,
            'reviews' => $reviews
        ]);
    }

    public function reviewconfirm($id, Request $request)
    {
        $agentId = base64_decode($id);
        $review = Review::findOrFail($agentId);
        $status = $request->input('status');

        if ($status === 'approve') {
            $review->status = 1;
        } elseif ($status === 'decline') {
            $review->status = 2;
        }

        $review->save();

        return response()->json(['success' => true]);
    }

    public function submitTicket(Request $request)
    {
        //ticket
        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'query' => 'required|string',
            'attachment' => 'required|file|max:10240',
        ]);
        $attachment = "";


        if ($request->hasFile('attachment')) {
            $imageName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('/storage/ticket/'), $imageName);
            $attachment = $imageName;
        }
        $agentId = Auth::id();
        $encryptedId = base64_encode($agentId);
        $ticket = new Ticket();
        $ticket->agent_id = $agentId;
        $ticket->subject = $validatedData['subject'];
        $ticket->query = $validatedData['query'];
        $ticket->attachment = 'storage/ticket/' . $attachment;
        $ticket->save();
        return redirect('/agent-details/' . $encryptedId);
    }


    public function contactusview(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 1) {
            $contactus = ContactUs::all();
            return view('contactus', [
                'contactus' => $contactus,
            ]);
        } else {
            return redirect('/unauthorized');
        }
    }
    public function getContactUsData(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);
        $query = ContactUs::query();
        $query->orderBy('created_at', 'desc');
        $contactus = $query->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($contactus as $contact) {
            $contact->sno = $sno++;
        }
    
        return response()->json($contactus);
    }
    

    public function getAutoSuggestionContactus(Request $request)
    {
        $searchTerm = $request->input('term');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
    
        $usersQuery = ContactUs::where(function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', "%$searchTerm%");
        })
        ->orWhere('email', 'like', "%$searchTerm%")
        ->orWhere('phone', 'like', "%$searchTerm%")
        ->orderBy('created_at', 'desc');
    
        $users = $usersQuery->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;

        $modifiedUsers = $users->map(function ($user) use (&$sno) {
            $user['sno'] = $sno++;
            $user['name'] = $user['first_name'];
            unset($user['first_name'], $user['last_name']);
            return $user;
        });
    
        return response()->json($modifiedUsers);
    }
    


  public function getAutoQueryContactUs(Request $request)
{
    $name = $request->input('name');
    $email = $request->input('email');
    $phone = $request->input('phone');
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $query = ContactUs::query();
    if (!is_null($name)) {
        $query->where('first_name', $name);
    }
    if (!is_null($email)) {
        if ($query->getQuery()->wheres) {
            $query->orWhere('email', $email);
        } else {
            $query->where('email', $email);
        }
    }
    if (!is_null($phone)) {
        if ($query->getQuery()->wheres) {
            $query->orWhere('phone', $phone);
        } else {
            $query->where('phone', $phone);
        }
    }

    $query->orderBy('created_at', 'desc');
    $singContactInfo = $query->paginate($perPage);
    $sno = ($page - 1) * $perPage + 1;
    $modifiedContactInfo = $singContactInfo->map(function ($contactInfo) use (&$sno) {
        $contactInfo['sno'] = $sno++;
        $contactInfo['phone'] = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '($1) $2-$3', $contactInfo['phone']);
        return $contactInfo;
    });

    return response()->json($modifiedContactInfo);
}
public function getAutosearchQueryContactUs(Request $request)
{
    $query = ContactUs::query();

    $searchTerm = $request->input('term');
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    if (!is_null($searchTerm)) {
        $query->where(function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', "%$searchTerm%")
                ->orWhere('email', 'like', "%$searchTerm%")
                ->orWhere('phone', 'like', "%$searchTerm%");
        });
    }

    $query->orderBy('created_at', 'desc');

    $contactInfo = $query->paginate($perPage);

    $contactInfo->getCollection()->transform(function ($contact) {
        $contact['phone'] = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '($1) $2-$3', $contact['phone']);
        return $contact;
    });

    $sno = ($page - 1) * $perPage + 1;
    $contactInfo->getCollection()->each(function ($item) use (&$sno) {
        $item->sno = $sno++;
    });

    return response()->json($contactInfo);
}



    public function getContactUsDateData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1); 
        $contactDates = [];

        if ($startDate && $endDate) {
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
            $contactDatesQuery = ContactUs::select('id','first_name', 'last_name', 'email', 'phone', 'comment','created_at')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('created_at', 'desc');
            $contactDates = $contactDatesQuery->paginate($perPage);
            $contactDates->appends(['start_date' => $startDate, 'end_date' => $endDate]);
            $sno = ($page - 1) * $perPage + 1;
            foreach ($contactDates as $contactDate) {
                $contactDate->sno = $sno++;
            }

        }

        return response()->json($contactDates);
    }





    public function joinrepview(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 1) {
            $joinrep = JoinRep::all();
            return view('joinrep', [
                'joinrep' => $joinrep,
            ]);
        } else {
            return redirect('/unauthorized');
        }
    }


    public function getJoinRepData(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);
        $query = JoinRep::query();
        $query->orderBy('created_at', 'desc');
        $joinreps = $query->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($joinreps as $joinrep) {
            $joinrep->sno = $sno++;
        }
    
        return response()->json($joinreps);
    }
    

    public function getAutoSuggestionJoinrep(Request $request)
    {
        $searchTerm = $request->input('term');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1); 
    
        $usersQuery = JoinRep::where(function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', "%$searchTerm%");
        })
        ->orWhere('email', 'like', "%$searchTerm%")
        ->orWhere('phone', 'like', "%$searchTerm%")
        ->orderBy('created_at', 'desc');
    
        $users = $usersQuery->paginate($perPage);
    
        $sno = ($page - 1) * $perPage + 1;
    
        $modifiedUsers = $users->map(function ($user) use (&$sno) {
            return [
                'sno' => $sno++, 
                'name' => $user->first_name, 
                'email' => $user->email,
                'phone' => $user->phone
            ];
        });
    
        return response()->json($modifiedUsers);
    }
    
    public function getAutoQueryJoinrep(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $perPage = $request->input('per_page', 10); 
        $page = $request->input('page', 1); 
    
        $query = JoinRep::query();
    
        
        if (!is_null($name)) {
            $query->where('first_name', $name);
        }
        if (!is_null($email)) {
            $query->orWhere('email', $email);
        }
        if (!is_null($phone)) {
            $query->orWhere('phone', $phone);
        }
    
        $singJoinrepInfo = $query->paginate($perPage);
    
        $sno = ($page - 1) * $perPage + 1;
        $modifiedJoinrepInfo = $singJoinrepInfo->map(function ($info) use (&$sno) {
            $info['sno'] = $sno++;
            return $info;
        });
    
        return response()->json($modifiedJoinrepInfo);
    }
    
    public function getentersearchjoinrep(Request $request)
{
    $term = $request->input('term');

    // Query to retrieve search results based on the term
    $results = JoinRep::where('first_name', 'like', "%$term%")
        ->orWhere('email', 'like', "%$term%")
        ->orWhere('phone', 'like', "%$term%")
        ->get();

    return response()->json($results);
}

public function getsearchautoquerypropertyreview(Request $request){
    $term = $request->input('term');

    $query = PropertyReview::query();
    $query->select('property_reviews.*', 'properties_all_data.UnparsedAddress as address','properties_all_data.slug_url','properties_all_data.ListingId');
    $query->leftJoin('properties_all_data', 'properties_all_data.ListingId', '=', 'property_reviews.listing_id');

    if (!is_null($term)) {
        $query->where(function ($query) use ($term) {
            $query->where('email', 'like', "%$term%")
                ->orWhere('review_from', 'like', "%$term%");
        });
    }

    $query->orderBy('created_at', 'desc');

    if ($request->has('per_page') && $request->has('page')) {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $propertyReviewInfo = $query->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($propertyReviewInfo as $propertyReview) {
            $propertyReview->sno = $sno++;
        }

        return response()->json($propertyReviewInfo);
    } else {
        // If pagination parameters are not provided, return all results without pagination
        $propertyReviewInfo = $query->get();
        foreach ($propertyReviewInfo as $propertyReview) {
            $propertyReview->sno = null; // Or any appropriate value for sno when not paginated
        }

        return response()->json($propertyReviewInfo);
    }
}





    public function getJoinRepDateData(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);
  $joinrepDates = [];
    if ($startDate && $endDate) {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $joinrepDatesQuery = JoinRep::select('id', 'first_name', 'last_name', 'email', 'phone', 'joinee', 'experience', 'practice_areas', 'reference', 'about', 'is_contact', 'perceive','created_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc');

        $joinrepDates = $joinrepDatesQuery->paginate($perPage);
        $joinrepDates->appends(['start_date' => $startDate, 'end_date' => $endDate]);
       
        $sno = ($page - 1) * $perPage + 1;
        foreach ($joinrepDates as $joinrepDate) {
            $joinrepDate->sno = $sno++;
        }

    }
    return response()->json($joinrepDates);

    // return response()->json(['error' => 'Start date and end date are required'], 400);
}
     

    
public function allcityView()
{
    
    if ($user = Auth::check()){
        $user = Auth::user();
        if ($user->role === 1) {
            $cities = Properties::paginate(10);
            
            return view('city', [
                'cities' => $cities,
            ]);
        } else {
            return redirect('/unauthorized');
        }
    }else{
        return redirect('/');
    }
}


    
// public function getcityData(Request $request)
// {
//     $perPage = 10;
//     $page = $request->input('page', 1);
//     $column = $request->input('column','id');
//     $order_by = $request->input('order_by','asc');
//     $cities = CityProperty::select('id', 'city_name as city', 'status', DB::raw('(SELECT COUNT(*) FROM properties_all_data WHERE properties_all_data.City = city_properties.city_name) as properties_count'))
//     ->orderBy($column,$order_by)    
//     ->paginate($perPage);

//     return response()->json($cities);
// }

    public function getcityData(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);
        $column = $request->input('column','id');
        $order_by = $request->input('order_by','asc');
        $cities = CityProperty::select('id', 'city_name as city', 'status', DB::raw('(SELECT COUNT(*) FROM properties_all_data WHERE properties_all_data.City = city_properties.city_name) as properties_count'))
        ->orderBy($column,$order_by)    
        ->paginate($perPage);
    
        return response()->json($cities);
    }

    public function editcity($id)
    {
        $city = CityProperty::findOrFail($id);
        return view('editcity', ['city' => $city]);
        //  dd($city);
    }

    public function getAutoSuggestionCity(Request $request)
    {
        $searchTerm = $request->input('term');

        $cities = CityProperty::query();

        if (!empty($searchTerm)) {
            $cities->where('city_name', 'like', "%$searchTerm%");
        }

        $cities = $cities->pluck('city_name')->unique();

        return response()->json($cities);
    }


    public function getAutoQueryCity(Request $request)
    {
        $cityName = $request->input('city_name');
        $propertiesCount = $request->input('properties_count');

        $query = CityProperty::select('id', 'city_name as City', 'status')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('properties_all_data')
                    ->whereColumn('properties_all_data.City', 'city_properties.city_name');
            }, 'properties_count');

        if (!is_null($cityName)) {
            $query->where('city_name', 'like', "%$cityName%");
        }
        if (!is_null($propertiesCount)) {
            $query->having('properties_count', $propertiesCount);
        }

        $cityInfo = $query->get();

        return response()->json($cityInfo);
    }

    public function getAutoQuerysearchCity(Request $request)
    {
        $cityName = $request->input('city_name');
        $propertiesCount = $request->input('properties_count');
    
        $query = CityProperty::select('id', 'city_name as City', 'status')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('properties_all_data')
                    ->whereColumn('properties_all_data.City', 'city_properties.city_name');
            }, 'properties_count');
    
        if (!is_null($cityName)) {
            $query->where('city_name', 'like', "%$cityName%");
        }
    
        if (!is_null($propertiesCount)) {
            $query->having('properties_count', $propertiesCount);
        }
    
        $cityInfo = $query->get();
    
        return response()->json($cityInfo);
    }
    
    


    public function updateCity(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
                'city_image' => 'image|mimes:jpeg,png,jpg,|',
            ]);

            $city = CityProperty::findOrFail($validatedData['id']);

            if ($city) {
                if ($request->hasFile('city_image')) {
                    $image = $request->file('city_image');
                    $imageName = $image->hashName();
                    $image->storeAs('public/city_images', $imageName);
                    $city->image = url('storage/city_images/' . $imageName); 
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


    public function getPrefillData(Request $request, $id)
    {

        $city = CityProperty::findOrFail($id);
        return response()->json(['city' => $city]);
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
      
    public function getFeaturedProperty()
    {
        $status = 1;

        $featuredProperties = CityProperty::where('status', $status)->get();

        foreach ($featuredProperties as $property) {
            $city = $property->city_name;
            $propertyCount = Properties::where('city', $city)->count();
            $property['propertycount'] =  $propertyCount ? $propertyCount : 0;
        }

        if ($featuredProperties->count() > 0) { // Check if any featured properties are found
            $response = response()->json(['success' => true, 'properties' => $featuredProperties]);
            // Add CORS headers
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            return $response;
        } else {
            return response()->json(['success' => false, 'message' => 'No featured properties found.']);
        }
    }

    public function showPropertyReviews()
    {
       
        $propertyreviews = PropertyReview::all();
        // dd($propertyreviews);
        try {
            $user = Auth::user();
            
            if ($user->role === 1) {
                return view('propertyreviews', [
                    'propertyreviews' => $propertyreviews,
                ]);
            } else {
                return redirect('/');
            }
        } catch (\Exception $e) {
           
            return redirect('/');
        }
    }
    

  
    public function getPropertyReviewsData(Request $request)
{
    $perPage = 10;
    $page = $request->input('page', 1);
    $query = PropertyReview::query();
    $query->select('property_reviews.*', 'properties_all_data.UnparsedAddress as address','properties_all_data.slug_url','properties_all_data.ListingId');
    $query->leftJoin('properties_all_data', 'properties_all_data.ListingId', '=', 'property_reviews.listing_id');
    $query->orderBy('property_reviews.created_at', 'desc');
    $propertyReviews = $query->paginate($perPage);
    $sno = ($page - 1) * $perPage + 1;
    foreach ($propertyReviews as $propertyReview) {
        $propertyReview->sno = $sno++;
    }
    return response()->json($propertyReviews);
}

    
    public function getAutoSuggestionPropertyReview(Request $request)
    {
        $searchTerm = $request->input('term');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
    
        $propertyreviewsQuery = PropertyReview::query();
        $propertyreviewsQuery->select('property_reviews.*', 'properties_all_data.UnparsedAddress as address','properties_all_data.slug_url','properties_all_data.ListingId');
        $propertyreviewsQuery->leftJoin('properties_all_data', 'properties_all_data.ListingId', '=', 'property_reviews.listing_id');
        if (!empty($searchTerm)) {
            $propertyreviewsQuery->where('email', 'like', "%$searchTerm%")
                ->orWhere('review', 'like', "%$searchTerm%")
                ->orWhere('review_from', 'like', "%$searchTerm%")
                ->orderBy('created_at', 'desc');
        }
    
        $propertyreviews = $propertyreviewsQuery->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($propertyreviews as $propertyreview) {
            $propertyreview->sno = $sno++;
        }
    
        return response()->json($propertyreviews);
    }
    

    public function getAutoQueryPropertyReview(Request $request)
{
    $email = $request->input('term');
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);
    $query = PropertyReview::query();
    $query->select('property_reviews.*', 'properties_all_data.UnparsedAddress as address','properties_all_data.slug_url','properties_all_data.ListingId');
    $query->leftJoin('properties_all_data', 'properties_all_data.ListingId', '=', 'property_reviews.listing_id');
    
    if (!is_null($email)) {
        $query->where(function ($query) use ($email) {
            $query->where('email', $email)
                ->orWhere('review_from', $email);
        });
    }

    $query->orderBy('created_at', 'desc');
    $propertyReviewInfo = $query->paginate($perPage);
    $sno = ($page - 1) * $perPage + 1;
    foreach ($propertyReviewInfo as $propertyReview) {
        $propertyReview->sno = $sno++;
    }

    return response()->json($propertyReviewInfo);
}


public function tourView()
{
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 1) {
            $tour = Tour::all();
            return view('tour', [
                'tour' => $tour,
            ]);
        } else {
            return redirect('/unauthorized');
        }
    } else {
        return redirect('/'); 
    }
}

   

    public function gettourData(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);
        $query = Tour::query();
        $query->orderBy('created_at', 'desc');
        $tours = $query->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($tours as $tour) {
            $tour->sno = $sno++;
        }
        
        return response()->json($tours);
    }
    


        public function getAutoSuggestionTour(Request $request)
    {
        $searchTerm = $request->input('term');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        
        $usersQuery = Tour::where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%$searchTerm%");
            })
            ->orWhere('email', 'like', "%$searchTerm%")
            ->orWhere('phone', 'like', "%$searchTerm%")
            ->orderBy('created_at', 'desc');
        $users = $usersQuery->paginate($perPage);
        $sno = ($page - 1) * $perPage + 1;
        foreach ($users as $user) {
            $user->sno = $sno++;
        }
        return response()->json($users);
    }



    public function getAutoQueryTour(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $page = $request->input('page', 1);
        $perPage = 10;
        $query = Tour::query();
        if (!is_null($name)) {
            $query->where('name', $name);
        }
        if (!is_null($email)) {
            if ($query->getQuery()->wheres) {
                $query->orWhere('email', $email);
            } else {
                $query->where('email', $email);
            }
        }
        if (!is_null($phone)) {
            if ($query->getQuery()->wheres) {
                $query->orWhere('phone', $phone);
            } else {
                $query->where('phone', $phone);
            }
        }

        $query->orderBy('created_at', 'desc');

        $tourInfo = $query->get();
        $sno = ($page - 1) * $perPage + 1;

        foreach ($tourInfo as $tour) {
           $tour->sno = $sno++;
        }
        return response()->json($tourInfo);
    }
    public function getsearchautoquerytour(Request $request)
{
    $query = Tour::query();

    $searchTerm = $request->input('term');
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    if (!is_null($searchTerm)) {
        $query->where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', "%$searchTerm%")
                ->orWhere('email', 'like', "%$searchTerm%")
                ->orWhere('phone', 'like', "%$searchTerm%");
        });
    }

    $query->orderBy('created_at', 'desc');

    $tourInfo = $query->paginate($perPage);

    $sno = ($page - 1) * $perPage + 1;
    $tourInfo->getCollection()->transform(function ($tour) use (&$sno) {
        $tour->sno = $sno++;
        return $tour;
    });

    return response()->json($tourInfo);
}


    

    public function getTourDateData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1); 
        $tourDates = [];
    
        if ($startDate && $endDate) {
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
            $tourDatesQuery = Tour::select('id', 'name', 'email', 'phone', 'message', 'time', 'date', 'created_at')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('created_at', 'desc');
            $tourDates = $tourDatesQuery->paginate($perPage);
            $tourDates->appends(['start_date' => $startDate, 'end_date' => $endDate]);
            $sno = ($page - 1) * $perPage + 1;
            foreach ($tourDates as $tourDate) {
                $tourDate->sno = $sno++;
            }
        }
    
        return response()->json($tourDates);
    }
    

    public function profileEdit($id)
    {

        $encryptedId = base64_decode($id);
        $agent = User::findOrFail($encryptedId);
        return view('editprofile', compact('agent'));
    }
    public function personalProfileEdit(Request $request)
    {

        $id = $request->input('id-field');
        $validator = Validator::make($request->all(), [
            'agent_email' => 'required|email|max:255',
            'agent_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = Auth::user();

        $userFromDB = User::findOrFail($user->id);
        $password = $request->old_password;
        if ($password != null) {
            if (!Hash::check($password, $userFromDB->password)) {
                return redirect()->back()->withErrors(['old_password' => 'Incorrect old password.'])->withInput();
            }
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $profile_image = "";
        if ($request->hasFile('agent_profile')) {
            $imageName = time() . '.' . $request->agent_profile->extension();
            $request->agent_profile->move(public_path('/storage/images/'), $imageName);
            $profile_image = 'storage/images/' . $imageName;
        } elseif (!empty($request->input('old_image'))) {

            $profile_image = $request->input('old_image');
        }

        $userAttributes = [
            'name' => $request->input('agent_first') . ' ' . $request->input('agent_last'),
            'email' => $request->input('agent_email'),
            'profile_picture' => $profile_image,
        ];

        if ($request->filled('new_password')) {
            $userAttributes['password'] = Hash::make($request->input('new_password'));
        }

        User::findOrFail($id)->update($userAttributes);
        return redirect()->route('show-agents')->with('success', 'Profile updated successfully!');
    }
    
    public function restore($id)
    {
        $agent = User::onlyTrashed()->findOrFail($id);
        $agent->restore(); 
        if ($agent->status === 0) {
            $agent->status = 1; 
            $agent->save();
        }
 
        return response()->json(['message' => 'Agent restored successfully', 'agent' => $agent]);
    }

 
}