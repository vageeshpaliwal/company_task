<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\device;

class ticket extends Model
{
    protected $fillable=['ticket_number','device_id','description','status'];
    public function device(){
        return $this->belongsTo(device::class);
    }
}
