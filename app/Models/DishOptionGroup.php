<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dish;
use App\Models\DishOption;

class DishOptionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'dish_id',
        'name',
        'min_options',
        'max_options',
        'is_required',
    ];

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function options()
    {
        return $this->hasMany(DishOption::class, 'group_id');
    }
}
