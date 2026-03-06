<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\device;
use App\Models\ticket;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);
        $device= device::find($request->device_id);

        if($device->warranty_expiry_date < now()){
            return response()->json([
                'message' => 'warranty expired.',
            ], 400);
        }
        $ticket= Ticket::create([
            'ticket_number' => (new TicketService())->generate([]),
            'device_id' => $request->device_id,
            'description'=> $request->description
            
        ]);

        return response()->json([
            'message' => 'Ticket created successfully.',
            'ticket' => $ticket,
        ], 201);

    }

    public function updateStatus(Request $request,$id)
    {
        $ticket = ticket::findOrFail($id);
        $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found.',
            ], 404);
        }
        $ticket->update([
            'status' => $request->status,
        ]);

            

        return response()->json(
            [
                    'message' => 'Ticket status updated successfully.',
                    'ticket' => $ticket,
            ], 200);
            
    }
    public function index(Request $request)
    {
        $tickets = ticket::with('device');
        if($request ->search){
            $tickets = $tickets->where('ticket_number', 'like', '%' . $request->search . '%');
        }
        if($request->status){
            $tickets = $tickets->where('status', $request->status);
        }
        if($request->from_date && $request->to_date){
            $tickets = $tickets->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }
        $tickets= $tickets->paginate($request->per_page ?? 10);
        return response()->json([
            'tickets' => $tickets,
        ], 200);
    }
}