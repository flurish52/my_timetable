<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{


    function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'device_id' => ['required', 'string'],
            'platform' => ['nullable', 'string'],
        ]);

        DeviceToken::updateOrCreate(
            [
                'device_id' => $request->device_id,
            ],
            [
                'token' => $request->token,
                'user_id' => auth()->id(),
                'device_name' => $request->userAgent(),
                'platform' => $request->platform ?? 'web',
                'last_active_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
        ]);
    }
}
