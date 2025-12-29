<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DishOptionGroup;
use App\Models\OrderItemOption;

class DishOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'additional_price',
    ];

    public function group()
    {
        return $this->belongsTo(DishOptionGroup::class, 'group_id');
    }

    public function orderItemOptions()
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
