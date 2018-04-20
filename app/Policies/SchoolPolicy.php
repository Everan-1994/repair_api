<?php

namespace App\Policies;

use App\Models\User;

class SchoolPolicy
{
    public function destroy(User $user)
    {
        return $user->can('manage_users') || $user->hasRole('Founder');
    }
}
