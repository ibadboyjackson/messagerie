<?php

namespace App\Policies;

use App\Model\Message;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function read (User $user, Message $message) {

        return $user->id === $message->to_id;

    }

}
