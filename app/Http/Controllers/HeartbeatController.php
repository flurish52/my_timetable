<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HeartbeatController extends Controller
{
    public function store(Request $request)
    {
        $request->user()->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        return response()->noContent();
    }
}
