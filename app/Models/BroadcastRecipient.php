<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastRecipient extends Model
{
    protected $fillable = [
        'cook_broadcast_id',
        'user_id',
        'phone',
        'name',
        'status',
    ];

    public function broadcast()
    {
        return $this->belongsTo(CookBroadcast::class, 'cook_broadcast_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
