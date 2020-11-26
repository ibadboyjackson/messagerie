<?php

namespace App\Repository;

use App\Model\Message;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ConversationsRepository {
    /**
     * @var User
     */
    private $user;
    /**
     * @var Message
     */
    private $message;


    /**
     * ConversationsRepository constructor.
     * @param User $user
     * @param Message $message
     */
    public function __construct (User $user, Message $message) {
        $this->user = $user;
        $this->message = $message;
    }


    /**
     * Get users
     * @param int $userId
     * @return Builder[]|Collection
     */
    public function getConversations (int $userId) {

        return $this->user->newQuery()->select('id', 'name')->where('id', '!=', $userId)->get();
    }

    /**
     * Create a message
     * @param string $content
     * @param int $from
     * @param int $to
     * @return Builder|Model
     */
    public function createMessage (string $content, int $from, int $to) {
        return $this->message->newQuery()->create([
           'content' => $content,
           'from_id' => $from,
           'to_id' => $to,
           'created_at' => Carbon::now()
        ]);
    }

    /**
     * Get messages for login user
     * @param int $from
     * @param int $to
     * @return Builder
     */
    public function getMessagesFor(int $from, int $to)
    {
        return $this->message->newQuery()
            ->whereRaw(" ( (from_id = $from AND to_id = $to) OR (from_id = $to AND to_id = $from) ) ")
            ->orderByDesc('created_at')
            ->with([
                'from' => function ($query) {
                    return $query->select('id', 'name');
                }
            ])
            ;
    }

    /**
     * Get unread messages from a single conversation
     * @param int $userId
     * @return Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function unreadCount (int $userId) {

        return $this->message->newQuery()
            ->where('to_id', $userId)
            ->groupBy('from_id')
            ->selectRaw('from_id, COUNT(id) as count')
            ->whereRaw('read_at IS NULL')
            ->get()
            ->pluck('count', 'from_id');

    }

    /**
     * Put all user's message as read
     * @param int $from
     * @param int $to
     */
    public function readAllFrom (int $from, int $to)
    {
        return $this->message->where('from_id', $from)->where('to_id', $to)->update(['read_at' => Carbon::now()]);
    }

}
