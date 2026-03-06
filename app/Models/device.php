<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class device extends Model
{
    protected $fillable=['name','type','unique_num','os','purchase_date','warranty_expiry_date','created_by'];
    public function users(){
        return $this->belongsToMany(User::class)->withPivot('assigned_at');
    }
    public function tickets(){
        return $this->hasMany(Ticket::class);
    }
}
