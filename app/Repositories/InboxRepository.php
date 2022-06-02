<?php


namespace App\Repositories;


use App\Models\Conversation;
use App\User;
use Carbon\Carbon;

class InboxRepository
{
	public function fetchAll()
    {
        return Conversation::where('id_from', auth()->id())
            ->orderBy('id', 'desc')
            ->orWhere('id_to', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }

    public function userExists($user)
    {
        if (User::where('name', $user)->first() !== null) {
            return true;
        } else {
            return false;
        }
    }

    public function conversationExists($to, $from)
    {
        //prevent to pm yourself
        if(auth()->user()->name == $to->name){
            return true;
        }
        //check if conversation already exists
        if (Conversation::where('id_to',"=", $to->id)->first() != null  AND Conversation::where('id_from','=', $from)->first() != null OR Conversation::where('id_to','=', $from)->first() !== null AND Conversation::where('id_from','=', $to->id)->first() !== null) {
            return true;
        }
        return false;
    }

}