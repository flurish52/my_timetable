<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function show(string $slug): Response|RedirectResponse
    {
        $school = School::where('acronym', $slug)->firstOrFail();

        if ($school->is_supported) {
            return redirect('/dashboard');
        }

        $user = Auth::user();
        $joined = $user->waitlist_school_id === $school->id;

        // Position = base offset + how many people joined this school
        // before (or at the same time as) this user.
        $position = $school->waitlistBaseOffset()
            + $school->waitlistedUsers()
                ->when($joined, fn ($q) => $q->where('waitlist_joined_at', '<=', $user->waitlist_joined_at))
                ->when(!$joined, fn ($q) => $q)
                ->count();

        $schoolRank = School::where('is_supported', false)
            ->withCount('waitlistedUsers')
            ->orderByDesc('waitlisted_users_count')
            ->pluck('id')
            ->search($school->id);

        return Inertia::render('Schools/Landing', [
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'slug' => $school->slug,
            ],
            'joined' => $joined,
            'position' => $joined ? $position : $school->waitlistCount(),
            'schoolRank' => $schoolRank === false ? '—' : $schoolRank + 1,
            'shareUrl' => url("/schools/{$school->slug}"),
        ]);
    }

    public function join(string $slug): RedirectResponse
    {
        $school = School::where('acronym', $slug)->firstOrFail();
        $user = Auth::user();

        if ($user->waitlist_school_id !== $school->id) {
            $user->update([
                'waitlist_school_id' => $school->id,
                'waitlist_joined_at' => now(),
            ]);
        }
        $user->syncRoles(['independent']);



        return redirect("/schools/{$school->acronym}");
    }

    public function leaderboard(): Response
    {
        $schools = School::where('is_supported', false)
            ->withCount('waitlistedUsers')
            ->orderByDesc('waitlisted_users_count')
            ->get()
            ->map(fn ($school) => [
                'id' => $school->id,
                'name' => $school->name,
                'slug' => $school->slug,
                'count' => $school->waitlistCount(),
            ])
            ->values();

        $userSchoolId = Auth::user()->waitlist_school_id;

        return Inertia::render('Schools/Leaderboard', [
            'schools' => $schools,
            'userSchoolSlug' => $userSchoolId
                ? ($schools->firstWhere('id', $userSchoolId)['slug'] ?? null)
                : null,
        ]);
    }
}
