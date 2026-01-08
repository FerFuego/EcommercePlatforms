<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPushToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        UserPushToken::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'token' => $request->token,
            ],
            [
                'device_type' => $request->device_type,
            ]
        );

        return response()->json(['message' => 'Token saved successfully']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        UserPushToken::where('token', $request->token)->delete();

        return response()->json(['message' => 'Token deleted successfully']);
    }
}
