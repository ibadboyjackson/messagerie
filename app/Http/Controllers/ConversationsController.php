<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Notifications\MessageReceived;
use App\Repository\ConversationsRepository;
use App\User;
use Illuminate\Auth\AuthManager;

class ConversationsController extends Controller
{
    /**
     * @var ConversationsRepository
     */
    private $r;
    /**
     * @var AuthManager
     */
    private $auth;

    /**
     * ConversationsController constructor.
     * @param ConversationsRepository $r
     * @param AuthManager $auth
     */
    public function __construct (ConversationsRepository $r, AuthManager $auth) {

        $this->r = $r;
        $this->auth = $auth;
        $this->middleware('auth');
    }


    public function index () {

        return view('conversations.index');
    }

    public function show (User $user) {

        $me = $this->auth->user()->id;
        $messages = $this->r->getMessagesFor ($me, $user->id)->paginate(50);
        $unread = $this->r->unreadCount($me);

        if (isset($unread[$user->id])) {
            $this->r->readAllFrom ($user->id, $me);
            unset($unread[$user->id]) ;
        }

        return view('conversations.show', [
            'users' => $this->r->getConversations($me),
            'user' => $user,
            'messages' => $messages,
            'unread' => $unread
        ]);
    }

    public function store (StoreMessageRequest $request, User $user) {

        $message = $this->r->createMessage($request->get('content'), $this->auth->user()->id, $user->id);
        $user->notify(new MessageReceived($message));

        return redirect()->route('conversations.show', $user);

    }
}
