<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskLikes;
use App\Models\User;
use App\Notifications\TaskLiked;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @return void
     */
    public function markRead(Request $request) :void
    {
        $notification_id = request('id');
        $notification = auth()->user()->notifications()->where('id', $notification_id)->first();
        $notification->markAsRead();
    }

    /**
     * @param Task $task
     * @return RedirectResponse
     */
    public function toggle(Task $task) :RedirectResponse
    {
        $owner_id = request('user');
        $record = TaskLikes::where('user_id', auth()->user()->id)->where('task_id', $task->id)->first();

        if (auth()->user()->id !== $owner_id && !$record) {
            $notifiedUser = User::find($owner_id);
            $notifiedUser->notify(new TaskLiked(auth()->user(), $task->slug));
        }

        $task->likes()->toggle(auth()->user()->id);
        return redirect()->back();
    }

    public function notifyComment()
    {

    }
}
