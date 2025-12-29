<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\DishOption;

class OrderItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'dish_option_id',
        'quantity',
        'price',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function dishOption()
    {
        return $this->belongsTo(DishOption::class);
    }
}
