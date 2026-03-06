<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AllocatipnController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_ids' => 'required|array|min:1',
            'device_ids.*' => 'required|exists:devices,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->device_ids as $device_id) {

                    $alreadyExists = DB::table('device_user')
                        ->where('device_id', $device_id)
                        ->where('user_id', $request->user_id)
                        ->exists();

                    if ($alreadyExists) {
                        throw new \Exception("Duplicate allocations are not allowed for device ID $device_id.");
                    }

                    $count = DB::table('device_user')
                        ->where('device_id', $device_id)
                        ->count();

                    if ($count >= 3) {
                        throw new \Exception("Device with ID $device_id has already been allocated to 3 users.");
                    }

                    DB::table('device_user')->insert([
                        'device_id' => $device_id,
                        'user_id' => $request->user_id,
                        'assigned_at' => now(),
                    ]);
                }
            });

            return response()->json([
                'message' => 'Devices allocated successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function deallocate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required|exists:devices,id',
        ]);

        $user = User::findOrFail($request->user_id);

        $isAllocated = $user->devices()
            ->where('devices.id', $request->device_id)
            ->exists();

        if (!$isAllocated) {
            return response()->json([
                'message' => 'This device is not allocated to the specified user.'
            ], 404);
        }

        $user->devices()->detach($request->device_id);

        return response()->json([
            'message' => 'Device deallocated successfully.'
        ], 200);
    }

}
