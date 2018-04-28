<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        if ($user->identify == 2) {
            $user->avatar = \DB::table('schools')->whereId($user->school_id)->value('logo');
        }
    }

    public function created(User $user)
    {
        if ($user->identify == 2) {
            \DB::table('schools')->whereId($user->school_id)->update(['bind' => 1]);
        }
    }
}