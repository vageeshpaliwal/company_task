<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use App\Models\device;

class DeviceController extends Controller
{

    public function index(Request $request)
    {
      
        $query = Device::query();

        if($request->search){
            $query->where('name','like',"%$request->search%")
                  ->orWhere('type','like',"%$request->search%")
                  ->orWhere('os','like',"%$request->search%");
        }
        
        if($request->os){
            $query->where('os',$request->os);
        }

        if($request->warranty_status){
            if($request->warranty_status === 'active'){
                $query->where('warranty_expiry_date','>=',now());
            } else if($request->warranty_status === 'expired'){
                $query->where('warranty_expiry_date','<',now());
            }
        }   
        $sort_by = $request->sort_by ?? 'name';
        $sort_order= $request->sort_order ?? 'asc';
        $devices = $query->orderBy($sort_by, $sort_order)->paginate($request->per_page ?? 10);



        return response()->json([
            'message' => 'Devices retrieved successfully',
            'total' => $devices->total(),
            'total_pages' => $devices->lastPage(),
             'current_page' => $devices->currentPage(),
             'per_page' => $devices->perPage(),
            'devices' => $devices->items(),
        ], 200);
    }
    public function store(StoreDeviceRequest $request)
    {
        $validated = $request->validated();

        $device = device::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'unique_num' => $validated['unique_num'],
            'os' => $validated['os'],
            'purchase_date' => $validated['purchase_date'],
            'warranty_expiry_date' => $validated['warranty_expiry_date'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Device created successfully',
            'device' => $device,
        ], 201);
    }
}
