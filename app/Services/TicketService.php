<?php

namespace App\Services;
use App\Models\Ticket;

class TicketService
{
    public function generate($data)
    {

    $last=Ticket::latest()->first();
    $number=$last ? intval(substr($last ->ticket_number,4)) + 1 : 1;
    return 'TCKT'.str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
   