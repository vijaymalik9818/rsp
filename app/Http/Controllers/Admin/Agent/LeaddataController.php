<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;


class LeaddataController extends Controller{


    public function showleads(Request $request)
    {
        $perPage = 8;  
        $page = $request->input('page', 1); 

        $leads = Lead::first()
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);
                 
        $total_leads = Lead::count();
     
        return view('leads', compact('leads','total_leads'));
    }
    public function getleads(Request $request)
    {
        
        $perPage = 8;
        $page = $request->input('page', 1);
        $skip = ($page - 1) * $perPage;

        $leads = Lead::first()
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);
        $idCounter = $skip + 1;

        $leadsWithIds = $leads->map(function ($lead) use (&$idCounter) {
            $lead['ids'] = $idCounter++;
            return $lead;
        });

        $totalleads = Lead::count();


        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $leadsWithIds,
            $totalleads,
            $perPage,
            $page
        );

        $paginator->setPath($request->url());

        return response()->json(['leads' => $paginator]);
    }

    public function getautosuggestionleads(Request $request){
        $searchTerm = $request->input('term');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1); 
    
        $usersQuery = Lead::where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', "%$searchTerm%");
        })
        ->orWhere('email', 'like', "%$searchTerm%")
        ->orWhere('phone', 'like', "%$searchTerm%")
        ->orderBy('created_at', 'desc');
    
        $users = $usersQuery->paginate($perPage);
    
        $sno = ($page - 1) * $perPage + 1;
    
        $modifiedUsers = $users->map(function ($user) use (&$sno) {
            return [
                'sno' => $sno++, 
                'name' => $user->name, 
                'email' => $user->email,
                'phone' => $user->phone
            ];
        });
    
        return response()->json($modifiedUsers);
    }
    public function getautoqueryleads(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $perPage = $request->input('per_page', 8); 
        $page = $request->input('page', 1); 
    
   
        $query = Lead::query();

        if (!is_null($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if (!is_null($email)) {
            $query->orWhere('email', 'like', '%' . $email . '%');
        }
        if (!is_null($phone)) {
            $query->orWhere('phone', 'like', '%' . $phone . '%');
        }
    
        $leads = $query->orderBy('created_at', 'desc')
                       ->paginate($perPage);
    
        $ids = [];

        $startingId = ($leads->currentPage() - 1) * $perPage + 1;

        for ($i = 0; $i < $perPage; $i++) {
            $ids[] = $startingId++;
        }

        foreach ($leads as $key => $user) {
            $user->ids = $ids[$key];
        }

      
    
        return response()->json(['leads' => $leads]);
    }
    
}
?>