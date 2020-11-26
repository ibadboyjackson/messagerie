<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MessagesController extends Controller {


    public function read (Request $request, Message $message) {

        $message->update([
            'read_at' => Carbon::now()
        ]);

        return ['success' => 1];

    }


}
