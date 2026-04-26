<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookBroadcast extends Model
{
    protected $fillable = [
        'cook_id',
        'message',
        'target_audience',
        'status',
        'sent_count',
    ];

    public function cook()
    {
        return $this->belongsTo(Cook::class);
    }

    public function recipients()
    {
        return $this->hasMany(BroadcastRecipient::class);
    }
}
