<?php

namespace App\Http\Controllers;

use App\Models\SongPoll;
use App\Models\SongPollEntry;
use App\Models\SongPollVote;
use App\Services\SeoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends Controller
{
    public function index(SeoService $seo): View
    {
        $poll = $this->activePoll();
        abort_unless($poll, 404);

        $entries = $poll->entries()->get();
        $total = (int) $entries->sum('votes_count');
        $votedEntryId = $this->votedEntryId($poll);

        return view('site.vote', [
            'seo' => $seo->page('vote'),
            'poll' => $poll,
            'entries' => $entries,
            'totalVotes' => $total,
            'votedEntryId' => $votedEntryId,
            'isOpen' => $poll->isOpen(),
            'showTicker' => true,
            'activeNav' => 'vote',
            'newsletterHeadline' => 'لا تفوّت نتيجة<br><em>أغنية العام</em>',
            'newsletterSub' => 'اشترك ليصلك إعلان الفائز وملفات الفن العربي أولاً.',
        ]);
    }

    public function store(Request $request): JsonResponse|Response
    {
        $poll = $this->activePoll();
        abort_unless($poll, 404);
        abort_unless($poll->isOpen(), 403, 'التصويت مغلق.');

        $data = $request->validate([
            'entry_id' => ['required', 'integer', 'exists:song_poll_entries,id'],
            'email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'أدخل بريدك الإلكتروني لإكمال التصويت.',
            'email.email' => 'أدخل بريداً إلكترونياً صالحاً.',
        ]);

        $email = strtolower(trim($data['email']));

        $entry = SongPollEntry::query()
            ->where('song_poll_id', $poll->id)
            ->findOrFail($data['entry_id']);

        $voterHash = $this->voterHash($request, $poll);

        if (SongPollVote::query()->where('song_poll_id', $poll->id)->where('voter_hash', $voterHash)->exists()) {
            return $this->alreadyVotedResponse($request, $poll);
        }

        if (SongPollVote::query()->where('song_poll_id', $poll->id)->where('email', $email)->exists()) {
            return $this->emailTakenResponse($request, $poll);
        }

        try {
            DB::transaction(function () use ($poll, $entry, $voterHash, $email, $request) {
                SongPollVote::query()->create([
                    'song_poll_id' => $poll->id,
                    'song_poll_entry_id' => $entry->id,
                    'email' => $email,
                    'voter_hash' => $voterHash,
                    'ip_hash' => hash('sha256', (string) $request->ip()),
                ]);

                $entry->increment('votes_count');
            });
        } catch (QueryException $e) {
            if (SongPollVote::query()->where('song_poll_id', $poll->id)->where('email', $email)->exists()) {
                return $this->emailTakenResponse($request, $poll);
            }

            return $this->alreadyVotedResponse($request, $poll);
        }

        $request->session()->put($this->sessionKey($poll), $entry->id);

        $payload = $this->resultsPayload($poll->fresh(), $entry->id);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'شكراً — صوتك وصل.',
                ...$payload,
            ]);
        }

        return redirect()
            ->route('vote.index')
            ->with('vote_success', 'شكراً — صوتك وصل.');
    }

    protected function activePoll(): ?SongPoll
    {
        return SongPoll::query()->open()->latest('id')->first()
            ?? SongPoll::query()->published()->latest('id')->first();
    }

    protected function voterHash(Request $request, SongPoll $poll): string
    {
        $sessionId = $request->session()->getId();

        return hash_hmac('sha256', $poll->id.'|'.$sessionId, (string) config('app.key'));
    }

    protected function sessionKey(SongPoll $poll): string
    {
        return 'song_poll_voted_'.$poll->id;
    }

    protected function votedEntryId(SongPoll $poll): ?int
    {
        $fromSession = session($this->sessionKey($poll));

        if ($fromSession) {
            return (int) $fromSession;
        }

        $hash = $this->voterHash(request(), $poll);
        $vote = SongPollVote::query()
            ->where('song_poll_id', $poll->id)
            ->where('voter_hash', $hash)
            ->first();

        return $vote?->song_poll_entry_id;
    }

    protected function alreadyVotedResponse(Request $request, SongPoll $poll): JsonResponse|Response
    {
        $votedId = $this->votedEntryId($poll);
        $payload = $this->resultsPayload($poll->fresh(), $votedId);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => false,
                'already_voted' => true,
                'message' => 'لقد صوّت من قبل — صوت واحد لكل قارئ.',
                ...$payload,
            ], 409);
        }

        return redirect()
            ->route('vote.index')
            ->with('vote_error', 'لقد صوّت من قبل — صوت واحد لكل قارئ.');
    }

    protected function emailTakenResponse(Request $request, SongPoll $poll): JsonResponse|Response
    {
        $message = 'هذا البريد صوّت من قبل. صوت واحد لكل قارئ.';

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => false,
                'email_taken' => true,
                'message' => $message,
                'errors' => ['email' => [$message]],
            ], 422);
        }

        return redirect()
            ->route('vote.index')
            ->with('vote_error', $message);
    }

    /** @return array{total_votes: int, voted_entry_id: int|null, entries: list<array<string, mixed>>} */
    protected function resultsPayload(SongPoll $poll, ?int $votedEntryId): array
    {
        $entries = $poll->entries()->get();
        $total = (int) $entries->sum('votes_count');

        return [
            'total_votes' => $total,
            'voted_entry_id' => $votedEntryId,
            'entries' => $entries->values()->map(fn (SongPollEntry $entry, int $index) => [
                'id' => $entry->id,
                'rank' => $index + 1,
                'votes' => $entry->votes_count,
                'share' => $entry->shareOf($total),
            ])->all(),
        ];
    }
}
