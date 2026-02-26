<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Models\Device;

class UserController extends Controller
{
    public function index(Request $request)
    {
       $query = User::with('devices');
       if($request->search){
        $query->where('name','like',"%$request->search%")
              ->orWhere('email','like',"%$request->search%");
       }
       $sort_by = $request->sort_by ?? 'name';
       $sort_order= $request->sort_order ?? 'asc';
       $users = $query->orderBy($sort_by, $sort_order)->paginate($request->per_page ?? 10);


       return response()->json([
            'message' => 'Users retrieved successfully',
            'total' => $users->total(),
            'total_pages' => $users->lastPage(),
             'current_page' => $users->currentPage(),
             'per_page' => $users->perPage(),
             'users' => $users->items()
        ], 200);
    }
}
