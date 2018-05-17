<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function destroy(User $user, Order $order)
    {
        return $user->can('manage_orders') || $user->isAuthorOf($order);
    }
}
