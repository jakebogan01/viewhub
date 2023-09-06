<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $notification_id = request('id');
        $notification = auth()->user()->notifications()->where('id', $notification_id)->first();
        $notification->markAsRead();
    }
}
