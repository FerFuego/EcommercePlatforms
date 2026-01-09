<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = \App\Models\Order::find($orderId);

    if (!$order) {
        return false;
    }

    // Cliente
    if ($user->id === $order->customer_id) {
        return true;
    }

    // Cocinero
    if ($user->cook && $user->cook->id === $order->cook_id) {
        return true;
    }

    // Repartidor
    if ($order->deliveryAssignment && $order->deliveryAssignment->delivery_user_id === $user->id) {
        return true;
    }

    return false;
});

Broadcast::channel('cook.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId && $user->cook !== null;
});
