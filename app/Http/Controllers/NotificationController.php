<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notifyUser(Request $request, User $user)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'extra' => 'sometimes|array',
        ]);

        $user->notify(new SystemNotification($data['title'], $data['message'], $data['extra'] ?? []));

        return response()->json(['status' => 'Notification dispatched']);
    }
}
