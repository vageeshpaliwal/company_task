<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllocatipnController extends Controller
{
    public function store(Request $request)
    {
        DB::transaction((function() use ($request){
            foreach($request->device_ids as $device_id){
                $count= DB::table('device_user')->where('device_id',$device_id)->count();
                if($count >= 3){
                    throw new \Exception("Device with ID $device_id has already been allocated to 3 users.");
                }
              

                DB::table('device_user')->updateOrInsert([
                    'device_id' => $device_id,
                    'user_id' => $request->user_id,
                ],
                [
                    'allocated_at' => now(),
                ]
                );

              
            }
        }));
        return response()->json([
            'message' => 'Devices allocated successfully.',
        ], 200);
    }
        
}
