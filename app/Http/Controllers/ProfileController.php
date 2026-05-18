<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use App\Models\Programme;
use App\Models\ProgrammeType;
use App\Models\School;
use App\Models\User;
use App\Models\Level;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => User::with('programme.department','programme.programme_type',  'level')
                ->where('id', Auth::id())->first(),
        ]);
    }

    public function createSetupAccount(Request $request)
    {
        return Inertia::render('Profile/Setup', [
            'user' => User::with(['programme', 'level'])
            ->where('id', Auth::id())->first(),
            'schools' => School::with('departments')->get(),
            'departments' => Department::all(),
            'programmeTypes' => ProgrammeType::all(),
            'levels' => Level::all(),
        ]);
    }

    public function storeSetUpAccount(Request $request)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
            'programme_type_id' => 'required|exists:programme_types,id',
            'level_id' => 'required|exists:levels,id',
        ]);

        $department = Department::findOrFail($data['department_id']);
        $type = ProgrammeType::findOrFail($data['programme_type_id']);

        $programmeName = $department->name . ' (' . $type->name . ')';

        $programme = Programme::firstOrCreate([
            'department_id' => $data['department_id'],
            'programme_type_id' => $data['programme_type_id'],
        ], [
            'name' => $programmeName,
        ]);

        $user = auth()->user();

        $user->update([
            'programme_id' => $programme->id,
            'level_id' => $data['level_id'],
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
