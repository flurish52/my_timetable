<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\PastQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_papers' => PastQuestion::count(),
                'published_papers' => PastQuestion::where('status', 'published')->count(),
                'draft_papers' => PastQuestion::where('status', 'draft')->count(),
                'total_contributors' => User::role('contributor')->count(),
                'total_students' => User::role('student')->count(),
                'papers_per_school' => PastQuestion::join('courses', 'past_questions.course_id', '=', 'courses.id')
                    ->join('schools', 'courses.school_id', '=', 'schools.id')
                    ->selectRaw('schools.name as school, count(*) as total')
                    ->groupBy('schools.name')
                    ->get(),
                'recent_activity' => PastQuestion::with('creator:id,name', 'course:id,title')
                    ->latest()
                    ->take(8)
                    ->get(['id', 'created_by', 'course_id', 'status', 'created_at']),
            ],
        ]);
    }

    public function papers(Request $request): Response
    {
        $papers = PastQuestion::query()
            ->with(['creator:id,name,email', 'course:id,title,school_id', 'course.school:id,name'])
            ->when($request->search, fn ($q, $search) =>
            $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            )
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->school_id, fn ($q, $schoolId) => $q->whereHas('course', fn ($c) => $c->where('school_id', $schoolId)))
            ->when($request->course_id, fn ($q, $courseId) => $q->where('course_id', $courseId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/PastQuestions/Index', [
            'papers' => $papers,
            'filters' => $request->only(['search', 'status', 'school_id', 'course_id']),
        ]);
    }

    public function approvePaper(PastQuestion $paper): RedirectResponse
    {
        $paper->update([
            'status' => 'published',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Paper published.');
    }

    public function rejectPaper(Request $request, PastQuestion $paper): RedirectResponse
    {
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $paper->update([
            'status' => 'draft',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Paper sent back to draft.');
    }

    public function unpublishPaper(PastQuestion $paper): RedirectResponse
    {
        $paper->update(['status' => 'draft']);

        return back()->with('success', 'Paper unpublished.');
    }

    public function contributorHistory(User $user): Response
    {
        return Inertia::render('Admin/Users/ContributorHistory', [
            'contributor' => $user->only('id', 'name', 'email'),
            'papers' => PastQuestion::with('course:id,title')
                ->where('created_by', $user->id)
                ->latest()
                ->get(),
        ]);
    }

    public function users(Request $request): Response
    {
        $users = User::query()
            ->when($request->search, fn ($q, $search) =>
            $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
            )
            ->when($request->role, fn ($q, $role) => $q->role($role))
            ->with('roles:id,name', 'level', 'programme')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'availableRoles' => \Spatie\Permission\Models\Role::pluck('name'),
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$validated['role']]);

        return back()->with('success', "{$user->name}'s role updated to {$validated['role']}.");
    }

    public function bulkUpdateRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();

        foreach ($users as $user) {
            $user->syncRoles([$validated['role']]);
        }

        return back()->with('success', count($users) . " user(s) updated to {$validated['role']}.");
    }
}
