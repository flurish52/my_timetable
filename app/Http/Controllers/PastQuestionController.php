<?php

namespace App\Http\Controllers;

use App\Models\PastQuestion;
use App\Http\Requests\StorePastQuestionRequest;
use App\Http\Requests\UpdatePastQuestionRequest;
use Inertia\Inertia;

class PastQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia::render('PastQuestions', [

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
    public function store(StorePastQuestionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PastQuestion $pastQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PastQuestion $pastQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePastQuestionRequest $request, PastQuestion $pastQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PastQuestion $pastQuestion)
    {
        //
    }
}
