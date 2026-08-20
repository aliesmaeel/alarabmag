<?php

namespace App\Support;

use App\Models\SongPoll;
use App\Models\SongPollVote;
use Illuminate\Http\Request;

class SongVoting
{
    public static function currentPoll(): ?SongPoll
    {
        return SongPoll::query()->open()->latest('id')->first()
            ?? SongPoll::query()->published()->latest('id')->first();
    }

    public static function sessionKey(SongPoll $poll): string
    {
        return 'song_poll_voted_'.$poll->id;
    }

    public static function voterHash(Request $request, SongPoll $poll): string
    {
        return hash_hmac('sha256', $poll->id.'|'.$request->session()->getId(), (string) config('app.key'));
    }

    public static function votedEntryId(SongPoll $poll, ?Request $request = null): ?int
    {
        $request ??= request();
        $fromSession = $request->session()->get(self::sessionKey($poll));

        if ($fromSession) {
            return (int) $fromSession;
        }

        $vote = SongPollVote::query()
            ->where('song_poll_id', $poll->id)
            ->where('voter_hash', self::voterHash($request, $poll))
            ->first();

        return $vote?->song_poll_entry_id;
    }
}
