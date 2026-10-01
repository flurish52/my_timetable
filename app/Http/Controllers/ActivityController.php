<?php

namespace App\Http\Controllers;

use App\Models\PastQuestion;
use App\Models\QuestionAttempt;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * "My Activity" — two tabs over two different tables (scanned papers vs
     * practice attempts). Whichever tab is active loads eagerly; the other
     * is wrapped in Inertia::lazy() so it's only queried when the user
     * actually switches tabs (a partial reload requesting it by name),
     * not on every visit to this page.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tab = in_array($request->query('tab'), ['scans', 'notes', 'history'], true)
            ? $request->query('tab')
            : 'scans';


        return Inertia::render('Activity/Index', [
            'activeTab' => $tab,
            'scans' => $tab === 'scans'
                ? fn() => $this->scansQuery($user->id, 'past_question')->paginate(15)->withQueryString()
                : Inertia::lazy(fn() => $this->scansQuery($user->id, 'past_question')->paginate(15)->withQueryString()),
            'notes' => $tab === 'notes'
                ? fn() => $this->scansQuery($user->id, 'notes_quiz')->paginate(15)->withQueryString()
                : Inertia::lazy(fn() => $this->scansQuery($user->id, 'notes_quiz')->paginate(15)->withQueryString()),

            'history' => $tab === 'history'
                ? fn() => $this->historyQuery($user->id)->paginate(15)->withQueryString()
                : Inertia::lazy(fn() => $this->historyQuery($user->id)->paginate(15)->withQueryString()),
        ]);
    }

    /**
     * Papers this user has scanned and kept private. Not the same set as
     * their practice history — a scan can exist with zero attempts against
     * it, and an attempt can be against a paper someone else contributed.
     */
    private function scansQuery(int $userId): Builder
    {
        return PastQuestion::query()
            ->where('created_by', $userId)
            ->where('visibility', 'private')
            ->with('course')
            ->withCount('questions')
            ->latest();
    }

    /**
     * Every practice attempt this user has submitted, regardless of who
     * originally contributed the paper.
     */
    private function historyQuery(int $userId): Builder
    {
        return QuestionAttempt::query()
            ->where('user_id', $userId)
            ->with('pastQuestion.course')
            ->latest();
    }
}
