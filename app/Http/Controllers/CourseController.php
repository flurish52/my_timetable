<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Admin/Course/Index', [
            'courses' => Course::where('school_id', $request->user()->school_id)
                ->orderBy('code')
                ->get(['id', 'code', 'title', 'slug', 'credit_unit']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'credit_unit' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        Course::create([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['code'], $request->user()->school_id),
            'school_id' => $request->user()->school_id,
        ]);

        return back()->with('success', 'Course added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'credit_unit' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $course->update([
            ...$validated,
            'slug' => $validated['code'] === $course->code
                ? $course->slug
                : $this->uniqueSlug($validated['code'], $course->school_id, $course->id),
        ]);

        return back()->with('success', 'Course updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    private function uniqueSlug(string $code, int $schoolId, ?int $ignoreId = null): string
    {
        $base = Str::slug($code);
        $slug = $base;
        $i = 1;

        while (
        Course::where('school_id', $schoolId)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
