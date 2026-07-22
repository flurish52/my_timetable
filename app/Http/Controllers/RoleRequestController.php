<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RoleRequestController extends Controller
{
    public function create(): Response
    {
        $user = request()->user();

        return Inertia::render('RoleRequest/Create', [
            'currentRole' => $user->getRoleNames()->first(),
            'latestRequest' => $user->latestRoleRequest,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(
            $user->roleRequests()->pending()->exists(),
            422,
            'You already have a pending request.'
        );

        $validated = $request->validate([
            'requested_role' => ['required', Rule::in(['contributor', 'lecturer', 'admin'])],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->roleRequests()->create($validated);

        return back()->with('success', 'Your request has been submitted for review.');
    }

    // --- admin review side ---

    public function index(): Response
    {
        return Inertia::render('Admin/RoleRequests/Index', [
            'requests' => RoleRequest::with('user')->pending()->latest()->get(),
        ]);
    }

    public function approve(RoleRequest $roleRequest): RedirectResponse
    {
        $roleRequest->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        $roleRequest->user->syncRoles([$roleRequest->requested_role]);

        return back()->with('success', "{$roleRequest->user->name} is now a {$roleRequest->requested_role}.");
    }

    public function reject(Request $request, RoleRequest $roleRequest): RedirectResponse
    {
        $validated = $request->validate(['review_note' => ['nullable', 'string', 'max:1000']]);

        $roleRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return back()->with('success', 'Request rejected.');
    }
}
