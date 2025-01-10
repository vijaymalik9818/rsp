<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Properties;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Properties::select('id', 'slug_url', 'updated_at','ListingId')->get();
        return response()->json($properties);
    }
    public function professionals(Request $request)
    {
        // $professionals = User::select('id', 'slug_url','updated_at')->get();
        $professionals = User::select('id', 'slug_url','updated_at')->whereNull('role')->where('status',1)->get();
        return response()->json($professionals);
    }
}
