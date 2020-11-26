<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Repository\ConversationsRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConversationsController extends Controller {
    /**
     * @var ConversationsRepository
     */
    private $conversationsRepository;


    /**
     * ConversationsController constructor.
     * @param ConversationsRepository $conversationsRepository
     */
    public function __construct (ConversationsRepository $conversationsRepository) {

        $this->conversationsRepository = $conversationsRepository;
    }


    public function index (Request $request) {

        $me = $request->user()->id;

        $conversations = $this->conversationsRepository->getConversations($request->user()->id);
        $unread = $this->conversationsRepository->unreadCount($me);

        foreach ($conversations as $conversation) {

            if (isset($unread[$conversation->id])) {
                $conversation->unread = $unread[$conversation->id];
            }else {
                $conversation->unread = 0;
            }

        }

        return [
            'conversations' => $conversations
        ];

    }

    public function show (User $user, Request $request) {

        $messageQuery = $this->conversationsRepository->getMessagesFor($request->user()->id, $user->id);
        $before = $request->get('before');
        $count = null;

        if ($before) {
            $messageQuery = $messageQuery->where('created_at', '<', $before);
        }else {
            $count = $messageQuery->count();
        }

        $messages = $messageQuery->limit(10)->get();
        $update = false;

        foreach ($messages as $message) {

            if ($message->read_at === null && $message->to_id === $request->user()->id) {

                $message->read_at = Carbon::now();

                if ($update === false) {

                    $this->conversationsRepository->readAllFrom($message->from_id, $message->to_id);

                }

                $update = true;
            }

        }

        return [
            'messages' => array_reverse($messages->toArray()),
            'count' => $count
        ];
    }

    public function store (User $user, StoreMessageRequest $request) {

        $message = $this->conversationsRepository->createMessage($request->get('content'), $request->user()->id, $user->id);

        broadcast(new NewMessage($message));

        return [
            'message' => $message
        ];

    }



}
